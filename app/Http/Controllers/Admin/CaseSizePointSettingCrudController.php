<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CaseSizePointSettingRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CaseSizePointSettingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CaseSizePointSettingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\CaseSizePointSetting::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/case-size-point-setting');
        CRUD::setEntityNameStrings('case size point setting', 'case size point settings');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('min_price')->type('number');
        CRUD::column('max_price')->type('number');
        CRUD::column('points')->type('number');
        CRUD::column('created_at')->type('text');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CaseSizePointSettingRequest::class);

         CRUD::field('min_price')->type('number')->wrapper(['class' =>'col-md-6 mr-3 required']);
         CRUD::field('max_price')->type('number')->wrapper(['class' =>'col-md-6 mr-3 required']);
         CRUD::field('points')->type('number')->wrapper(['class' =>'col-md-6 required']);
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
}
