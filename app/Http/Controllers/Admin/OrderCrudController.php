<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Mail\OrderMade;
use App\Mail\OrderUpdated;
use Illuminate\Http\Request;
use App\Mail\OrderReceiptUploaded;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Mail;
use Backpack\CRUD\app\Library\Widget;
use App\Mail\OrderPaymentSlipUploaded;
use Illuminate\Support\Facades\Storage;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrderCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Order::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/order');
        CRUD::setEntityNameStrings('order', 'orders');

        if (backpack_user()->hasRole('Business Partner') || backpack_user()->hasRole('Senior Business Partner')) {
            $this->crud->addClause('where', 'business_partner', backpack_user()->id);
        }

        if (backpack_user()->hasRole('Admin')) {
            $this->crud->denyAccess(['delete']);
        }
		
		if (request('type')) {
			
            if(request('type') == 'outgoing'){
				$users = User::select('id')->whereHas('roles', function ($q) {
					$q->where('name', 'Admin');
					$q->orWhere('name', 'Super Admin');
				})->get()->toArray();
			}elseif(request('type') == 'incoming'){
				$users = User::select('id')->whereHas('roles', function ($q) {
					$q->where('name', 'Senior Business Partner');
					$q->orWhere('name', 'Business Partner');
				})->get()->toArray();
			}	
			
            $this->crud->addClause('whereIn', 'action_by',$users);
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
		
		
        CRUD::column('id');
        CRUD::column('title');
        CRUD::column('description');
		if (backpack_user()->hasRole(['Admin','Super Admin'])) {
			CRUD::addColumn([
				// 1-n relationship
				'label'     => 'Order Status', // Table column heading
				'type'      => 'text',
				'value' => function($entry) {
						if($entry->actionby){  
						 return ($entry->actionby->roles[0]->name == 'Senior Business Partner' || $entry->actionby->roles[0]->name == 'Business Partner') ? 'Incoming' : 'Outgoing';
						}else{
							return '-';
						}
			},
			]);
		}
        CRUD::column('amount');
		
        CRUD::addColumn([
            // 1-n relationship
            'label'     => 'Guest', // Table column heading
            'type'      => 'select',
            'name'      => 'guest', // the column that contains the ID of that connected entity;
            'entity'    => 'guest', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => "App\Models\User", // foreign key model
        ]);

		
        CRUD::addColumn([
            // 1-n relationship
            'label'     => 'Business Partner', // Table column heading
            'type'      => 'select',
            'name'      => 'business_partner', // the column that contains the ID of that connected entity;
            'entity'    => 'businessPartner', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => "App\Models\User", // foreign key model
        ]);
		
        CRUD::column('remarks');
        CRUD::column('created_at');

        $this->crud->addButtonFromView('line', 'guest_info', 'guest_info');
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }
	
	

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(OrderRequest::class);

        $this->crud->addFields([
            [
                'name' => 'name',
                'fake' => true,
            ],
            [
                'name' => 'email',
                'fake' => true,
            ],
            [
                'name' => 'phone',
                'fake' => true,
            ],
            [
                'name' => 'address',
                'fake' => true,
            ]
        ]);

        CRUD::field('title');
        CRUD::field('description');

        if (backpack_user()->hasRole(['Admin', 'Super Admin'])) {
            CRUD::addField([
                'label'       => "Business Partner", // Table column heading
                'type'        => "select2_from_ajax",
                'name'        => 'business_partner', // the column that contains the ID of that connected entity
                'entity'      => 'businessPartner', // the method that defines the relationship in your Model
                'attribute'   => "name", // foreign key attribute that is shown to user
                'data_source' => url("api/v1/users?roles=All Business Partner"),
                'placeholder' => 'select business partner',
                'minimum_input_length' => 0
            ]);
        }
        CRUD::addField([   // Upload
            'name'      => 'payment_slip',
            'label'     => 'Payment Slip <small>(Max size: 2MB)</small>',
            'type'      => 'upload_multiple',
            'upload'    => true,
        ]);

        CRUD::addField([   // Upload
            'name'      => 'receipt',
            'label'     => 'Receipt <small>(Max size: 2MB)</small>',
            'type'      => 'upload_multiple',
            'upload'    => true,
        ]);

        CRUD::field('amount')->type('number');
        CRUD::field('remarks')->type('textarea');
        if (!backpack_user()->hasRole(["Admin", "Super Admin"])) {
            $this->crud->removeField('receipt');
        }
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
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
        $order_id = explode("/", $this->crud->getRequest()->getRequestUri())[3];
        $guest = Order::find($order_id)->guest;
        $this->crud->modifyField('email', [
            'default' => $guest->email
        ]);
        $this->crud->modifyField('name', [
            'default' => $guest->name
        ]);
        $this->crud->modifyField('phone', [
            'default' => $guest->phone
        ]);
        $this->crud->modifyField('address', [
            'default' => $guest->address
        ]);
        if (backpack_user()->hasRole(["Admin", "Super Admin"])) {
            $this->crud->modifyField('payment_slip', [
                'attributes' => [
                    'disabled' => 'disabled'
                ]
            ]);
        } else {
            $this->crud->removeField('receipt');
        }

        Widget::add()->type('script')->content('assets/js/order-edit.js');
    }

    public function store()
    {
		
		
        // do something before validation, before save, before everything; for example:
        // $this->crud->addField(['type' => 'hidden', 'name' => 'author_id']);

        $this->crud->addField(['type' => 'hidden', 'name' => 'action_by']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'guest']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'phone']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'address']);

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

        $request = $this->crud->getRequest();

        if (!backpack_user()->hasRole(['Admin', 'Super Admin'])) {
            $this->crud->addField(['type' => 'hidden', 'name' => 'business_partner']);
            $request->request->add(['business_partner' => backpack_user()->id]);
        }

        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $address = $request->address;
        $actionBy = backpack_auth()->user()->id;

        $user = \App\Models\User::firstWhere('email', $email);

        if ($user) {
            if ($name)
                $user->name = $name;
            if ($phone)
                $user->phone = $phone;
            if ($address)
                $user->address = $address;
            $user->save();
        } else {
            $user = \App\Models\User::create([
                'name' => $name ?? "",
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'password' => bcrypt($email)
            ]);

            $user->assignRole("Guest");
        }
        $request->request->add(['guest' => $user->id]);
        $request->request->add(['action_by' => $actionBy]);

        $response = $this->traitStore();

        $admins = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Admin')
                    ->orWhere('name', 'Super Admin');
            }
        )->get();

        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'text' => backpack_user()->name . ' created an order ' . $request->title . '.',
                'is_read' => false,
                'uid' => $admin->id,
                'order_id' => $this->crud->entry->id
            ]);
        }


        Mail::to($admins)->send(new OrderMade($this->crud->entry));

        // do something after save
        return $response;
    }


    public function update()
    {
        // do something before validation, before save, before everything; for example:
        // $this->crud->addField(['type' => 'hidden', 'name' => 'author_id']);
        // $this->crud->removeField('password_confirmation');

        $this->crud->addField(['type' => 'hidden', 'name' => 'guest']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'action_by']);
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

        if (backpack_user()->hasRole(['Admin', 'Super Admin'])) {
            $this->crud->removeField('payment_slip');
            $this->crud->getRequest()->request->remove('payment_slip');
        }
        $request = $this->crud->getRequest();
        if ($request->receive_order == "1") {
            $order = Order::find($request->id);
            $data = $request->only([
                'title',
                'description'
            ]);
            $data['status'] = "Receive Order";
            $data['introduced_by'] = $order->business_partner;
            $data['customer'] = $order->guest_id;
            $data['price'] = $request->amount;

            $order->guest->assignRole('Customer');
            $order->guest->removeRole('Guest');

            return redirect()->route('case-detail.create', $data);
        }
        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $address = $request->address;
        $actionBy = backpack_auth()->user()->id;
		
        $user = \App\Models\User::firstWhere('email', $email);

        if ($user) {
            if ($name)
                $user->name = $name;
            if ($phone)
                $user->phone = $phone;
            if ($address)
                $user->address = $address;
            $user->save();
        } else {
            $user = \App\Models\User::create([
                'name' => $name ?? "",
                'email' => $email,
                'password' => bcrypt($email)
            ]);

            $user->assignRole("Guest");
        }
        $request->request->add(['guest' => $user->id]);
        $request->request->add(['action_by' => $actionBy]);

        $response = $this->traitUpdate();

        $admins = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Admin')
                    ->orWhere('name', 'Super Admin');
            }
        )->get();


        if ($this->crud->getRequest()->payment_slip) {
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'text' => 'Order ' . $request->title . ' payment slip have been uploaded.',
                    'is_read' => false,
                    'uid' => $admin->id,
                    'order_id' => $this->crud->entry->id
                ]);
            }
            Mail::to($admins)->send(new OrderPaymentSlipUploaded($this->crud->entry));
        } else if ($this->crud->getRequest()->receipt) {
            $business_partner = $this->crud->entry->businessPartner;
            \App\Models\Notification::create([
                'text' => 'Order ' . $request->title . ' receipt have been uploaded.',
                'is_read' => false,
                'uid' => $business_partner->id,
                'order_id' => $this->crud->entry->id
            ]);
            Mail::to($business_partner)->send(new OrderReceiptUploaded($this->crud->entry));
        } else {
            Mail::to($admins)->send(new OrderUpdated($this->crud->entry));
        }
        // do something after save
        return $response;
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();

        $this->crud->addColumn([
            'name'      => 'payment_slip', // The db column name
            'type'      => 'bill',
            'value'     => function ($s) {
                if ($s->payment_slip) {
                    return Storage::url($s->payment_slip);
                } else {
                    return "";
                }
            }
            // image from a different disk (like s3 bucket)
        ]);
        $this->crud->addColumn([
            'name'      => 'receipt', // The db column name
            'type'      => 'bill',
            'value'     => function ($s) {
                if ($s->receipt) {
                    return Storage::url($s->receipt);
                } else {
                    return "";
                }
            }
            // image from a different disk (like s3 bucket)
        ]);
    }

    protected function show($id, Request $request)
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
