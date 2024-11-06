<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\CaseDetail;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Services\MailService;
use App\Mail\AppointmentDecided;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Backpack\CRUD\app\Library\Widget;
use App\Http\Requests\AppointmentRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AppointmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AppointmentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {
        show as traitShow;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Appointment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/appointment');
        CRUD::setEntityNameStrings('appointment', 'appointments');
        if (!backpack_user()->hasRole('Customer')) {
            $this->crud->denyAccess(['create', 'delete']);
        }
        if (request('date')) {
            $date = request('date');
            $this->crud->addClause('where', 'appointment_time', '>', $date . ' 00:00:00');
            $this->crud->addClause('where', 'appointment_time', '<', $date . ' 23:59:59');
        }

        if (backpack_user()->hasRole('Customer')) {
            $this->crud->denyAccess(['update']);
            $this->crud->addClause('where', 'customer_id', backpack_user()->id);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'label' => 'Case',
                'type' => 'select',
                'name' => 'case_id',
                'entity' => 'caseDetail',
                'model' => 'App\Models\CaseDetail',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('caseDetail', function ($q) use ($column, $searchTerm) {
                        $q->where('title', 'like', '%' . $searchTerm . '%');
                    });
                }

            ],
            [
                'label' => 'Time',
                'type' => 'text',
                'name' => 'appointment_time'
            ]
        ]);

        $this->crud->addColumn([
            'label' => 'Status',
            'type' => 'text',
            'name' => 'status'
        ]);

        if (backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Super Admin') )
            $this->crud->addColumn(
                [
                    'label' => 'Customer',
                    'type' => 'select',
                    'name' => 'customer_id',
                    'entity' => 'customer',
                    'model' => 'App\Models\User',
                    'searchLogic' => function ($query, $column, $searchTerm) {
                        $query->orWhereHas('customer', function ($q) use ($column, $searchTerm) {
                            $q->where('name', 'like', '%' . $searchTerm . '%');
                        });
                    }
                ]
            );

        $this->crud->addColumn([
            'label' => 'Remark',
            'type' => 'text',
            'name' => 'remark'
        ]);

        CRUD::column('created_at');

        $this->crud->addButtonFromView('line', 'google_link', 'google_link');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        Widget::add()->type('script')->content('assets/js/moment.min.js');
        Widget::add()->type('script')->content('assets/js/appointment-script.js');

        if ($this->crud->getCurrentOperation() == 'create') {
            CRUD::setValidation(AppointmentRequest::class);
        };

        $is_not_customer = !backpack_user()->hasRole('Customer');
        if ($is_not_customer) {
            $this->crud->addFields([
                [
                    'label'       => "Case",
                    'type'        => "select2_from_ajax",
                    'name'        => 'case_id',
                    'entity'      => 'caseDetail',
                    'attribute'   => "title",
                    'data_source' => url("api/v1/cases?customer=" . backpack_user()->id),
                    'placeholder' => 'select case',
                    'minimum_input_length' => 0,
                    'attributes' => [
                        'disabled' => 'disabled'
                    ]
                ],
                [
                    'label'       => "Appointment Date",
                    'type'        => "datetime",
                    'fake'        => true,
                    'name'        => 'appointment_time',
                    'attributes' => [
                        'id' => 'appointment_time',
                        'disabled' => 'disabled'
                    ],
                ],
                [
                    'name'        => 'remark',
                    'label'       => "Remark",
                    'type'        => 'textarea',
                    'allows_null' => true,
                    'attributes' => [
                        'disabled' => 'disabled'
                    ],
                    'options' => []
                ],

            ]);
        } else {
            $this->crud->addFields([
                [
                    'label'       => "Case",
                    'type'        => "select2_from_ajax",
                    'name'        => 'case_id',
                    'entity'      => 'caseDetail',
                    'attribute'   => "title",
                    'data_source' => url("api/v1/cases?customer=" . backpack_user()->id),
                    'placeholder' => 'select case',
                    'minimum_input_length' => 0,
                    'attributes' => []
                ],
                [
                    'label'       => "Appointment Date",
                    'type'        => "hidden",
                    'name'        => 'appointment_time',
                    'attributes' => [
                        'id' => 'appointment_time',

                    ],
                ],
                [
                    'label'       => "Customer",
                    'type'        => "hidden",
                    'name'        => 'customer_id',
                    'attributes'  => [
                        'value' => backpack_user()->id,

                    ]
                ],
                [
                    'name'  => 'date',
                    'label' => 'Date',
                    'type'  => 'date',
                    'attributes' => [
                        'min' => date('Y-m-d'),
                        'id' => 'appointment_date_crud',

                    ],
                ],
                [
                    'name'        => 'time',
                    'label'       => "Time",
                    'type'        => 'select_from_array',
                    'allows_null' => false,
                    'attributes' => [
                        'id' => 'appointment_time_crud',

                    ],
                    'options' => []
                ],
                [
                    'name'        => 'remark',
                    'label'       => "Remark",
                    'type'        => 'textarea',
                    'allows_null' => true,
                    'attributes' => [],
                    'options' => []
                ],
            ]);
        }


        if (backpack_user()->hasRole(['Admin', 'Super Admin'])) {
            $this->crud->addField([
                'label'       => "Status",
                'type'        => "select_from_array",
                'name'        => 'status',
                'options'     => [
                    'Rejected' => 'Rejected',
                    'Approved' => 'Approved'
                ]
            ]);
        }
    }

    public function store()
    {
        // do something before validation, before save, before everything
        $case = CaseDetail::find($this->crud->getRequest()->case_id);
        $date = $this->crud->getRequest()->date;
        $time = $this->crud->getRequest()->time;


        $this->crud->hasAccessOrFail('create');

        $admins = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Admin')
                    ->orWhere('name', 'Super Admin');
            }
        )->get();

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();


        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // insert item in the db
        try {
            DB::beginTransaction();
            $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));

            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'text' => backpack_user()->name . ' created an appointement on ' . $date . ' at ' . $time . ".",
                    'is_read' => false,
                    'uid' => $admin->id,
                    'appointment_id' => $item->id
                ]);
            }

            \App\Models\Notification::create([
                'text' => 'Your appointement on ' . $date . ' at ' . $time . " was saved.",
                'is_read' => false,
                'uid' => $item->customer_id,
                'appointment_id' => $item->id
            ]);
            $item->save();
            $this->data['entry'] = $this->crud->entry = $item;
            DB::commit();
            // show a success message
            (new MailService())->sendNewAppointmentMail($case, $date, $time);
            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            // save the redirect choice for next time
            $this->crud->setSaveAction();

            return $this->crud->performSaveAction($item->getKey());
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
            \Alert::error('Something went wrong')->flash();
            return redirect()->back();
        }
    }

    public function update()
    {
        // do something before validation, before save, before everything; for example:
        // $this->crud->addField(['type' => 'hidden', 'name' => 'author_id']);
        // $this->crud->removeField('password_confirmation');

        // Note: By default Backpack ONLY saves the inputs that were added on page using Backpack fields.
        // This is done by stripping the request of all inputs that do NOT match Backpack fields for this
        // particular operation. This is an added security layer, to protect your database from malicious
        // users who could theoretically add inputs using DeveloperTools or JavaScript. If you're not properly
        // using $guarded or $fillable on your model, malicious inputs could get you into trouble.

        // However, if you know you have proper $guarded or $fillable on your model, and you want to manipulate 
        // the request directly to add or remove request parameters, you can also do that.
        // We have a config value you can set, either inside your operation in `config/backpack/crud.php` if
        // you want it to apply to all CRUDs, or inside a particular CrudController:
        // $this->crud->setOperationSetting('saveAllInputsExcept', ['_token', '_method', 'http_referrer', 'current_tab', 'save_action']);
        // The above will make Backpack store all inputs EXCEPT for the ones it uses for various features.
        // So you can manipulate the request and add any request variable you'd like.
        // $this->crud->getRequest()->request->add(['author_id'=> backpack_user()->id]);
        // $this->crud->getRequest()->request->remove('password_confirmation');
        // $this->crud->getRequest()->request->add(['author_id'=> backpack_user()->id]);
        // $this->crud->getRequest()->request->remove('password_confirmation');

        $appointment = Appointment::find($this->crud->getRequest()->id);
        
        $status = $this->crud->getRequest()->status;
        if ($status) {
            $dt = explode(' ', $appointment->appointment_time);
            $customer = $appointment->customer;
            \App\Models\Notification::create([
                'text' => 'Appointment on ' . $dt[0] . ' at ' . $dt[1] . ' ' . strtolower($status) . ' by admin.',
                'is_read' => false,
                'uid' => $customer->id,
                'appointment_id' => $appointment->id
            ]);
            $case = CaseDetail::find($appointment->case_id);
            $googleCalendarLink = $appointment->getGoogleCalendarLink();
            Mail::to($customer->email)->send(new AppointmentDecided($case, $customer, $dt[0], $dt[1], $status, $googleCalendarLink));
        }
        $response = $this->traitUpdate();
        // do something after save
        return $response;
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function show(Request $request, $id)
    {
        if ($request->not_id) {
            $notif = \App\Models\Notification::find($request->not_id);
            $notif->is_read = true;
            $notif->save();
        }

        $content = $this->traitShow($id);

        return $content;
    }
}
