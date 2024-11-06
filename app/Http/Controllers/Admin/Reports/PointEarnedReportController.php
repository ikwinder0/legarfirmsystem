<?php

namespace App\Http\Controllers\Admin\Reports;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


/**
 * Class PointEarnedReportCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PointEarnedReportController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\CasePointTransaction::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reports/point-earned');
        CRUD::setEntityNameStrings('point earned report', 'point earned reports');
        $auth_id = backpack_user()->id;
        if(backpack_user()->hasRole('Senior Business Partner')) {
            $this->crud->addClause('where','business_partner',$auth_id);
        }

        //normal business partner not allow to view
        if( backpack_user()->hasRole('Business Partner') || backpack_user()->hasRole('Customer') ||
            backpack_user()->hasRole('Guest')
        )
        {
            abort(403, 'Access denied');
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
                'label'     => 'Case', // Table column heading
                'type'      => 'select',
                'name'      => 'case_id', // the column that contains the ID of that connected entity;
                'entity'    => 'caseDetail', // the method that defines the relationship in your Model
                'model'     => "App\Models\CaseDetail",
            ],
            [
                'label'     => 'Customer', // Table column heading
                'type'      => 'select',
                'name'      => 'customer_id', // the column that contains the ID of that connected entity;
                'entity'    => 'customers', // the method that defines the relationship in your Model
                'model'     => "App\Models\User",
            ]
        ]);
        if(backpack_user()->hasRole('Admin')) {
            $this->crud->addColumn([
                'label'     => 'Business Partner', // Table column heading
                'type'      => 'select',
                'name'      => 'business_partner', // the column that contains the ID of that connected entity;
                'entity'    => 'businessPartner', // the method that defines the relationship in your Model
                'model'     => "App\Models\User",
            ]);
        }
        $this->crud->addColumns([
            [
                'label'     => 'Price',
                'type'      => 'double',
                'name'      => 'price',
            ],
            [
                'label'     => 'Case Point', // Table column heading
                'type'      => 'double',
                'name'      => 'case_point'
            ],
            [
                'label'     => 'Old Total',
                'type'      => 'double',
                'name'      => 'old_points',
            ],
            [
                'label'     => 'Point', // Table column heading
                'type'      => 'double',
                'name'      => 'sub_point',
            ],
            [
                'label'     => 'New Total', // Table column heading
                'type'      => 'double',
                'name'      => 'updated_current_points'
            ]
        ]);
//        $this->crud->addColumn([
//                'label'     => 'Subtracted', // Table column heading
//                'type'      => 'double',
//                'name'      => 'sub_point',
//            ]);
        $this->crud->addColumn([
                'label'     => 'Status', // Table column heading
                'type'      => 'text',
                'name'      => 'status'
            ]);
    }

}
