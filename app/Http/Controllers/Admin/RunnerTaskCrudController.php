<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Models\RunnerTask;
use Illuminate\Http\Request;
use App\Mail\TaskStatusChanged;
use App\Mail\TaskAssignedToRunner;
use App\Models\RunnerTaskAttachment;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RunnerTaskRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Mail;

/**
 * Class RunnerTaskCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RunnerTaskCrudController extends CrudController
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
        CRUD::setModel(\App\Models\RunnerTask::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/runner-task');
        CRUD::setEntityNameStrings('runner task', 'runner tasks');
		$this->crud->setShowView('runnertasks.show');
		
		if (!(backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Super Admin'))) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }
		if (backpack_user()->hasRole('Runner')) {
            $this->crud->addClause('where', 'user_id', backpack_user()->id);
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
		Widget::add()->type('script')->content('assets/js/runners.js');
        CRUD::column('id');
        CRUD::column('title');
        CRUD::column('description');
        CRUD::column('remarks');
        CRUD::column('status');
        CRUD::column('created_at');
	
        $this->crud->addButtonFromModelFunction('line', 'open_status', 'openStatus', 'beginning');
        

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
        CRUD::setValidation(RunnerTaskRequest::class);

        CRUD::field('title');
        CRUD::field('description');
        CRUD::field('remarks');
        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
		 CRUD::addField([
            'label'       => "Assign Runner", // Table column heading
            'type'        => "select2_from_ajax",
            'name'        => 'user_id', // the column that contains the ID of that connected entity
            'entity'      => 'runner', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/v1/users?role=Runner"),
            'placeholder' => 'Select Runner',
            'minimum_input_length' => 0
        ]);
		CRUD::addField([
            'name' => 'status',
            'type' => 'select_from_array',
            'label' => 'Status',
            'options' => RunnerTask::_STATUS_OPTIONS,
            'allow_null' => false,
            'default' => RunnerTask::PROCESS
        ]);
		CRUD::addField([   // Upload
            'name'      => 'attachments',
            'label'     => 'Attachments <small>(Max size: 2MB)</small>',
            'type'      => 'upload_multiple',
            'upload'    => true,
        ]);
		$request = $this->crud->getRequest();
		$request->status && $this->crud->modifyField('status', [
            'value' => $request->status
        ]);
		$request->user_id && $this->crud->modifyField('user_id', [
            'value' => $request->user_id
        ]);
    }
	
	public function store()
    {

        $request = $this->crud->getRequest();
        $title = $request->title;
        $description = $request->description;
        $remarks = $request->remarks;
		$response = $this->traitStore();
		$user = User::findorfail($request->user_id);
		Mail::to($user->email)
                ->send(new TaskAssignedToRunner($user, $title, $description, $remarks));
		//$entryID = $this->data['entry']->id;
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
	
	public function changeStatus(Request $request)
    {
       
        $id = $request->id;
        $task = RunnerTask::findorfail($id);
		$old_status = $task->status;
        $task->status = $request->status;
        $task->save();
		$admins = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Admin')
                    ->orWhere('name', 'Super Admin');
            }
        )->get();
        $user = backpack_user();
        Mail::to($admins)->send(new TaskStatusChanged($old_status, $task, $user));
        \Alert::success('Status changed successfully')->flash();
        return redirect()->back();
    }
	
	public function update(Request $request)
    {
		
		$request = $this->crud->getRequest();
		$id = $request->id;
        $task = RunnerTask::findorfail($id);
		$old_status = $task->status;
		$status = $request->status;
		$response = $this->traitUpdate();
		
		
		$admins = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Admin')
                    ->orWhere('name', 'Super Admin');
            }
        )->get();
		
        $user = backpack_user();
		
        Mail::to($admins)->send(new TaskStatusChanged($old_status, $task, $user, $status));
		
		return $response;
	}
	
	protected function setupShowOperation()
    {
	  $business_role = backpack_user()->hasRole('Runner') ;

        if ($business_role) {
            $this->crud->denyAccess(['delete', 'update']);
            $this->crud->removeColumn('actions');
        }
		
      $this->setupListOperation();   
	  $this->crud->addColumn([
                'name' => 'attachments',
                'label' => 'Attachments',
                'type' => 'softcopy',
            ]);
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
