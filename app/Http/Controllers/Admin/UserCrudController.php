<?php

namespace App\Http\Controllers\Admin;

use App\Mail\GuestChangedToCustomer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        $this->crud->setModel(config('backpack.permissionmanager.models.user'));
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute(backpack_url('user'));

        //11-07-2022: dashboard summary user role count result url
        if (request('role-name')) {
            $this->crud->addClause('whereHas', 'roles', function ($q) {
                $q->where('roles.name', request('role-name'));
            });
        }

        //11-07-2022: parameter needed for role module list item count result url
        if (request('role')) {
            $this->crud->addClause('whereHas', 'roles', function ($q) {
                $q->where('roles.id', request('role'));
            });
        }

        if (backpack_user()->hasRole('Admin')) {
            $this->crud->denyAccess(['delete']);
        }
    }

    public function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => config('permission.models.role'), // foreign key model
            ],

        ]);

        if (backpack_pro()) {
            // Role Filter
            $this->crud->addFilter(
                [
                    'name'  => 'role',
                    'type'  => 'dropdown',
                    'label' => trans('backpack::permissionmanager.role'),
                ],
                config('permission.models.role')::all()->pluck('name', 'id')->toArray(),
                function ($value) { // if the filter is active
                    $this->crud->addClause('whereHas', 'roles', function ($query) use ($value) {
                        $query->where('role_id', '=', $value);
                    });
                }
            );

            // Extra Permission Filter
            $this->crud->addFilter(
                [
                    'name'  => 'permissions',
                    'type'  => 'select2',
                    'label' => trans('backpack::permissionmanager.extra_permissions'),
                ],
                config('permission.models.permission')::all()->pluck('name', 'id')->toArray(),
                function ($value) { // if the filter is active
                    $this->crud->addClause('whereHas', 'permissions', function ($query) use ($value) {
                        $query->where('permission_id', '=', $value);
                    });
                }
            );
        }
    }

    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);
    }

    public function setupUpdateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run
        //        dd($this->crud->getRequest());
        return $this->traitStore();
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        $oldUserInfo =  User::find($this->crud->getRequest()->id);
        $user = User::where('email', $oldUserInfo->email)->first();
        $is_guest = $user->hasRole('Guest');
        if ($is_guest && isset($this->crud->getRequest()->roles[0]) && $this->crud->getRequest()->roles[0] == 3) {
            $admins = User::whereHas(
                'roles',
                function ($q) {
                    $q->where('name', 'Admin')
                        ->orWhere('name', 'Super Admin');
                }
            )->get();

            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'text' => 'Guest user ' . $this->crud->getRequest()->name . ' changed to customer.',
                    'is_read' => false,
                    'uid' => $admin->id
                ]);
            }
            \App\Models\Notification::create([
                'text' => 'You are changed to customer by admin.',
                'is_read' => false,
                'uid' => $this->crud->getRequest()->id
            ]);
            // $admins = User::whereHas(
            //     'roles',
            //     function ($q) {
            //         $q->where('name', 'Admin')
            //             ->orWhere('name', 'Super Admin');
            //     }
            // )->get();
            // Mail::to($admins)->send(new GuestChangedToCustomer($user));
        }
        return $this->traitUpdate();
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        if ($request->input('roles'))
            $request->request->set('roles', [$request->input('roles')]);

        return $request;
    }

    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            //            [
            //                'label' => ucfirst(trans('backpack::permissionmanager.roles')),
            //                'type' => 'select',
            //                'name' => 'roles', // the db column for the foreign key
            //                'entity' => 'roles', // the method that defines the relationship in your Model
            //                'attribute' => 'name', // foreign key attribute that is shown to user
            //                'allows_null' => false,
            //                'attributes' => [
            //                    'id' => 'user_role_select'
            //                ],
            //                'model' => config('permission.models.role'), // foreign key model
            //                'options'   => (function ($query) {
            //                    return $query->orderBy('name', 'ASC')->get();
            //                }),
            //                'pivot' => true,            ]
        ]);


        if (backpack_user()->hasRole('Super Admin')) {
            $this->crud->addFields([
                [
                    'label'            => trans('backpack::permissionmanager.roles'),
                    'type' => 'select',
                    'name'             => 'roles', // the method that defines the relationship in your Model
                    'entity'           => 'roles', // the method that defines the relationship in your Model
                    'attribute'        => 'name', // foreign key attribute that is shown to user
                    'model'            => config('permission.models.role'), // foreign key model
                    'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                ],
            ]);
        }
        else
        {
            $this->crud->addFields([
                [
                    'label'            => trans('backpack::permissionmanager.roles'),
                    'type' => 'select',
                    'name'             => 'roles', // the method that defines the relationship in your Model
                    'entity'           => 'roles', // the method that defines the relationship in your Model
                    'attribute'        => 'name', // foreign key attribute that is shown to user
                    'model'            => config('permission.models.role'), // foreign key model
                    'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                    'attributes' => [
                        'disabled'    => 'disabled',
                    ],
                ],
            ]);
        }

        if ($this->crud->getCurrentOperation() == "update") {
            $user_id = explode("/", $this->crud->getRequest()->getRequestUri())[3];
            $user = \App\Models\User::find($user_id);

            if ($user->hasRole(["Guest", "Customer"])) {
                $this->crud->addFields([
                    [
                        'name'  => 'phone',
                        'label' => 'Phone',
                        'type'  => 'text',
                    ],
                    [
                        'name'  => 'address',
                        'label' => 'Address',
                        'type'  => 'text',
                    ],
                ]);
            }
        }

        $this->crud->addFields([
            [
                'name'  => 'phone',
                'label' => 'Phone',
                'type'  => 'text',
            ],
            [
                'name'  => 'address',
                'label' => 'Address',
                'type'  => 'text',
            ],
        ]);

    }
}
