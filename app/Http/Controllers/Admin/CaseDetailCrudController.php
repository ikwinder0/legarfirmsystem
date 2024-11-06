<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CaseDetailRequest;
use App\Models\CaseDetail;
use App\Models\CaseDetailStatusLog;
use App\Models\CasePointTransaction;
use App\Models\User;
use App\Services\CasePointService;
use App\Services\CasePointTransactionService;
use App\Services\MailService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class CaseDetailCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CaseDetailCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
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
        CRUD::setModel(\App\Models\CaseDetail::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/case-detail');
        CRUD::setEntityNameStrings('case detail', 'case details');

        if (!(backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Super Admin'))) {
            $this->crud->denyAccess(['create', 'open_status', 'delete', 'update']);
        }
        if (backpack_user()->hasRole('Business Partner') || backpack_user()->hasRole('Senior Business Partner') ) {
            $this->crud->addClause('where', 'introduced_by', backpack_user()->id);
        } elseif (backpack_user()->hasRole('Customer')) {
            $this->crud->addClause('where', 'customer', backpack_user()->id);
        }

        if (backpack_user()->hasRole('Admin')) {
            $this->crud->denyAccess(['delete']);
        }

        if (request('status')) {
            $this->crud->addClause('where', 'status', request('status'));
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

        Widget::add()->type('script')->content('assets/js/case-detail.js');
        CRUD::column('title');
        CRUD::column('price');

        //        $this->crud->addColumn([
        //            'name' =>'status',
        //            'label' => 'Status',
        //            'type'  => 'text'
        //        ]);

        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type'  => 'textwithprogress',
            'limit' => '1000',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('status', 'like', '%' . $searchTerm . '%');
            }
        ]);

        $this->crud->addColumn([
            // 1-n relationship
            'label'     => 'Customer', // Table column heading
            'type'      => 'select',
            'name'      => 'customer', // the column that contains the ID of that connected entity;
            'entity'    => 'customers', // the method that defines the relationship in your Model
            'model'     => "App\Models\User", // foreign key model
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('customers', function ($q) use ($column, $searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                });
            }
        ]);
        CRUD::column('created_at');

        if (backpack_user()->hasRole('Admin')) {
            $this->crud->addButtonFromModelFunction('line', 'open_status', 'openStatus', 'beginning');
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CaseDetailRequest::class);

        CRUD::addField([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
        ]);
        CRUD::addField([
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Description'
        ]);
        CRUD::addField([
            'name' => 'price',
            'type' => 'number',
            'label' => 'Price'
        ]);
        CRUD::addField([
            'name' => 'status',
            'type' => 'select_from_array',
            'label' => 'Status',
            'options' => CaseDetail::_STATUS_OPTIONS,
            'allow_null' => false,
            'default' => CaseDetail::_RECEIVE_ORDER
        ]);
        //        CRUD::addField([
        //            'name' => 'tracks',
        //            'type' => 'hidden',
        //        ]);

        CRUD::addField([
            'name' => 'remarks',
            'type' => 'textarea',
            'label' => 'Status Remarks',
        ]);

        CRUD::addField([
            'name' => 'case_detail_id',
            'label' => 'Title',
            'type' => 'hidden',
            'value' => \Route::current()->parameter('id')
        ]);

        CRUD::addField([
            'label'       => "Introduced By", // Table column heading
            'type'        => "select2_from_ajax",
            'name'        => 'introduced_by', // the column that contains the ID of that connected entity
            'entity'      => 'introducedBy', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/v1/users?roles=All Business Partner"),
            'placeholder' => 'select business partner',
            'minimum_input_length' => 0
        ]);
        CRUD::addField([
            'label'       => "Customer", // Table column heading
            'type'        => "select2_from_ajax",
            'name'        => 'customer', // the column that contains the ID of that connected entity
            'entity'      => 'customers', // the method that defines the relationship in your Model
            'attribute'   => "name", // foreign key attribute that is shown to user
            'data_source' => url("api/v1/users?role=Customer"),
            'allow_null' => 'false',
            'placeholder' => 'select customer',
            'minimum_input_length' => 0
        ]);
        CRUD::addField([   // Upload
            'name'      => 'softcopy',
            'label'     => 'Soft Copies',
            'type'      => 'upload_multiple',
            'upload'    => true,
        ]);

        $request = $this->crud->getRequest();
        $request->title && $this->crud->modifyField('title', [
            'value' => $request->title
        ]);
        $request->description && $this->crud->modifyField('description', [
            'value' => $request->description
        ]);
        $request->price && $this->crud->modifyField('price', [
            'value' => $request->price
        ]);
        $request->status && $this->crud->modifyField('status', [
            'value' => $request->status
        ]);
        $request->introduced_by && $this->crud->modifyField('introduced_by', [
            'value' => $request->introduced_by
        ]);
        $request->customer && $this->crud->modifyField('customer', [
            'value' => $request->customer
        ]);
    }


    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();



        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // insert item in the db
        try {
            DB::beginTransaction();
            $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
            $item->tracks = $request->status;
            $item->save();
            $this->data['entry'] = $this->crud->entry = $item;

            //If dont have introducer / business partner ignore case point transaction
            if ($item->introduced_by) {
                $point = (new CasePointService())->findCasePointByPrice($item->price);
                if ($point) {
                    (new CasePointTransactionService())->saveCasePointOnCreate($item, $point);
                }
                \App\Models\Notification::create([
                    'text' => backpack_user()->name . ' created a case ' . $request->title . '.',
                    'is_read' => false,
                    'uid' => $item->introduced_by,
                    'case_id' => $item->id
                ]);
            }

            //create case detail status log for tracking
            $caseDetailStatusLog = new CaseDetailStatusLog();
            $caseDetailStatusLog->case_detail_id = $item->id;
            $caseDetailStatusLog->status = $item->status;
            $caseDetailStatusLog->description = $item->description;
            $caseDetailStatusLog->remarks = $item->remarks;
            $caseDetailStatusLog->save();

            DB::commit();
            // show a success message

            \App\Models\Notification::create([
                'text' => backpack_user()->name . ' created a case ' . $request->title . '.',
                'is_read' => false,
                'uid' => $item->customer,
                'case_id' => $item->id
            ]);

            (new MailService())->sendCaseCreatedMail($item);
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
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();



        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // insert item in the db
        try {
            DB::beginTransaction();

            $_prev = $this->crud->getEntry($request->get($this->crud->model->getKeyName()));

            // update the row in the db
            $item = $this->crud->update(
                $request->get($this->crud->model->getKeyName()),
                $this->crud->getStrippedSaveRequest($request)
            );
            $this->data['entry'] = $this->crud->entry = $item;

            // show a success message
            \Alert::success(trans('backpack::crud.update_success'))->flash();
            $item->tracks = $request->status;
            $item->save();


            $point = (new CasePointService())->findCasePointByPrice($item->price);
            if ($point && $_prev->price != $item->price) {
                (new CasePointTransactionService())->saveCasePointOnUpdate($item, $point);
            }

            $remarks = $request->remarks;
            if ($_prev->status != $item->status) {
                (new MailService())->sendStatusChangedMail($_prev->status, $item, $remarks);
                //create case detail status log for tracking
                $caseDetailStatusLog = new CaseDetailStatusLog();
                $caseDetailStatusLog->case_detail_id = $item->id;
                $caseDetailStatusLog->status = $item->status;
                $caseDetailStatusLog->description = $item->description;
                $caseDetailStatusLog->remarks = $remarks;
                $caseDetailStatusLog->save();
            } else {
                $caseDetailStatusLog = CaseDetailStatusLog::where('case_detail_id', $item->id)
                    ->where('status', $item->status)
                    ->orderBy('id', 'DESC')
                    ->first();
                $prevCaseDetailStatusLog = CaseDetailStatusLog::where('case_detail_id', $item->id)
                    ->where('status', $item->status)
                    ->orderBy('id', 'DESC')
                    ->first();
                if ($caseDetailStatusLog) {
                    if (!isset($remarks)) {
                        $remarks = "";
                    }
                    $caseDetailStatusLog->remarks = $remarks;
                    $caseDetailStatusLog->save();

                    if (isset($caseDetailStatusLog->remarks) && $prevCaseDetailStatusLog->remarks != $remarks) {
                        (new MailService())->sendStatusChangedMail($_prev->status, $item, $remarks);
                    }
                }
            }

            DB::commit();
            // show a success message
            \Alert::success(trans('backpack::crud.update_success'))->flash();

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

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        Widget::add()->type('script')->content('assets/js/case-detail-edit.js');
    }


    public function changeStatus(Request $request)
    {

        $id = $request->id;
        $case = CaseDetail::findorfail($id);
        $old_status = $case->status;
        $case->status = $request->status;
        $case->tracks = $request->status;
        $case->save();
        (new MailService())->sendStatusChangedMail($old_status, $case);
        \Alert::success('Status changed successfully')->flash();
        return redirect()->back();
    }
    protected function setupShowOperation()
    {
        Widget::add()->type('script')->content('assets/js/case-detail-show.js');
        Widget::add()->type('style')->content('assets/css/case-detail-show.css');
        $business_role = backpack_user()->hasRole('Business Partner') || backpack_user()->hasRole('Senior Business Partner') ;

        if ($business_role) {
            $this->crud->denyAccess(['delete', 'update']);
            $this->crud->removeColumn('actions');
        }

        $this->crud->column('title');
        $this->crud->column('price');

        if (!backpack_user()->hasRole('Customer')) {
            $this->crud->addColumn([
                'name' => 'introduced_by',
                'type' => 'select',
                'label' => 'Introduced By',
                'entity' => 'introducedBy', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\User",
            ]);
        }

        $this->crud->addColumn([
            'name' => 'Customer',
            'type' => 'select',
            'label' => 'Customer',
            'entity'    => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => "App\Models\User",
        ]);
        $this->crud->column('created_at');
        $this->crud->column('updated_at');

        if (!$business_role) {
            $this->crud->column('description');
            $this->crud->addColumn([
                'name' => 'softcopy',
                'label' => 'Soft Copies',
                'type' => 'softcopy',
            ]);
            $this->crud->column('status');
            //            $this->crud->column('tracks');
        }

        $this->crud->addColumn([
            'name' => 'status_log',
            'label' => 'Status Log',
            'escaped' => false,
            'type' => 'model_function',
            'limit' => '999999',
            'function_name' => 'getCaseDetailStatusLog'
        ]);

        if (backpack_user()->hasRole('Customer')) {
            $this->crud->removeAllButtons();
        }

        $this->crud->setShowView('case_detail.show');
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
