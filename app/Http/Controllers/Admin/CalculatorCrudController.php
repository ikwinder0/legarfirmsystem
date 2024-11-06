<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CalculatorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CalculatorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CalculatorCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Calculator::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/calculator');
        CRUD::setEntityNameStrings('calculator', 'calculators');

        $this->crud->denyAccess(['create', 'show', 'update', 'delete']);
        $this->crud->addButtonFromView('line', 'show_report', 'show_report', 'beginnning');
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
        CRUD::column('name');

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
        CRUD::setValidation(CalculatorRequest::class);

        CRUD::field('name');
        $this->crud->addField([
            'label'       => "Type",
            'type'        => "select_from_array",
            'name'        => 'type',
            'options'     => [
                'spa' => 'SPA',
                'loan' => 'Loan',
                'master_title_loan' => 'Master Title Loan',
                'cost_of_assist_vendor' => 'Cost of Assist Vendor',
                'loan_refinance' => 'Refinance Loan'
            ]
        ]);

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
        $this->crud->addField([
            'label'       => "Name",
            'type'        => "text",
            'name'        => 'name',
            'attributes'  => [
                'disabled' => 'disabled'
            ]
        ]);
        $this->crud->addField([
            'label'       => "Type",
            'type'        => "select_from_array",
            'name'        => 'type',
            'options'     => [
                'spa' => 'SPA',
                'loan' => 'Loan',
                'master_title_loan' => 'Master Title Loan',
                'cost_of_assist_vendor' => 'Cost of Assist Vendor',
				'loan_refinance' => 'Refinance Loan'
            ],
            'attributes'  => [
                'disabled' => 'disabled'
            ]
        ]);
        $this->crud->addField([
            'label'       => "Type",
            'type'        => "select_from_array",
            'name'        => 'type',
            'fake'        => true,
            'options'     => [
                'spa' => 'SPA',
                'loan' => 'Loan',
                'master_title_loan' => 'Master Title Loan',
                'cost_of_assist_vendor' => 'Cost of Assist Vendor',
				'loan_refinance' => 'Refinance Loan'
            ],
            'attributes'  => [
                'disabled' => 'disabled'
            ]
        ]);
    }

    public function report($cid)
    {
        switch ($cid) {
            case '1':
                return view('purchases.report');
                break;
            case '2':
                return view('loans.report');
                break;
            case '3':
                return view('master_loan.report');
                break;
            case '4':
                return view('cost_vendor.report');
                break;
			case '5':
                return view('refinance_loan.report');
                break;
            default:
                # code...
                break;
        }
    }
}
