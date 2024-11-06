<?php

namespace App\Http\Controllers\Admin;

use App\Models\CalculatorItem;
use Backpack\CRUD\app\Library\Widget;
use Backpack\Settings\app\Models\Setting;
use App\Http\Requests\RefinanceLoanRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RefinanceLoanCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RefinanceLoanCrudController extends CrudController
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

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\RefinanceLoan::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/refinance-loan');
        CRUD::setEntityNameStrings('quotation (Loan - Refinance)', 'quotation (Loan - Refinance)');
		
		$this->crud->setShowView('refinance_loan.show');

        if (backpack_user()->hasRole('Admin')) {
            $this->crud->denyAccess(['delete']);
        }

        Widget::add()->type('style')->content('assets/css/calculator-quotation.css');
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
        CRUD::column('purchaser');
        CRUD::column('property');
        CRUD::column('pax');
        CRUD::column('loan_amount');

        $label = CalculatorItem::where([
            ['cid', 5],
            ['name', 'facility_agreement']
        ])->first()->label;
        $this->crud->addColumn([
            'name' => 'facility_agreement',
            'label' => $label
        ]);

        $label = CalculatorItem::where([
            ['cid', 5],
            ['name', 'memo_charge']
        ])->first()->label;
        $this->crud->addColumn([
            'name' => 'memo_charge',
            'label' => $label
        ]);

        $label = CalculatorItem::where([
            ['cid', 5],
            ['name', 'stamp_duty_facility_agreement']
        ])->first()->label;
        $this->crud->addColumn([
            'name' => 'stamp_duty_facility_agreement',
            'label' => $label
        ]);

        $label = CalculatorItem::where([
            ['cid', 5],
            ['name', 'other_fees']
        ])->first()->label;
        $this->crud->addColumn([
            'name' => 'other_fees',
            'label' => $label
        ]);

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
        CRUD::setValidation(RefinanceLoanRequest::class);
        CRUD::field('purchaser');
        CRUD::field('property');
        CRUD::field('loan_amount');
        CRUD::field('pax');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }
	
	public function store()
    {
        // do something before validation, before save, before everything; for example:
        $this->crud->addField(['type' => 'hidden', 'name' => 'facility_agreement']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'memo_charge']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'stamp_duty_facility_agreement']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'other_fees']);
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

        $amount = (int) $this->crud->getRequest()->loan_amount;
        $pax = (int) $this->crud->getRequest()->pax;
        $facility_agreement = ((float) Setting::get("fa") / 100) * $amount;

        $item = CalculatorItem::where([
            ['cid', 5],
            ['name', 'memo_charge']
        ])->first();
        $memo_charge = max(($item->percentage / 100) * $facility_agreement, $item->min_price);
        $other_fees = 0;

        if ($pax > 0) {
            $item = CalculatorItem::where([
                ['cid', 5],
                ['name', 'other_fees']
            ])->first();
            if ($item->subsequent_price) {
                $other_fees = $item->price;
                $item->price && $pax--;
                $other_fees += ($pax) * $item->subsequent_price;
            } else {
                $other_fees = $pax * $item->price;
            }
        }

        $this->crud->getRequest()->request->add(
            [
                'facility_agreement' => $facility_agreement,
                'memo_charge' => $memo_charge,
                'stamp_duty_facility_agreement' => ((float) Setting::get("sd_fa") / 100) * $amount,
                'other_fees' => $other_fees
            ]
        );

        $response = $this->traitStore();
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
	
	public function update()
    {
        // do something before validation, before save, before everything; for example:
        $this->crud->addField(['type' => 'hidden', 'name' => 'facility_agreement']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'memo_charge']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'stamp_duty_facility_agreement']);
        $this->crud->addField(['type' => 'hidden', 'name' => 'other_fees']);
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

        $amount = (int) $this->crud->getRequest()->loan_amount;
        $pax = (int) $this->crud->getRequest()->pax;
        $facility_agreement = ((float) Setting::get("fa") / 100) * $amount;

        $item = CalculatorItem::where([
            ['cid', 5],
            ['name', 'memo_charge']
        ])->first();
        $memo_charge = max(($item->percentage / 100) * $facility_agreement, $item->min_price);
        $other_fees = 0;

       $item = CalculatorItem::where([
            ['cid', 5],
            ['name', 'memo_charge']
        ])->first();
        $memo_charge = max(($item->percentage / 100) * $facility_agreement, $item->min_price);
        $other_fees = 0;

        if ($pax > 0) {
            $item = CalculatorItem::where([
                ['cid', 5],
                ['name', 'other_fees']
            ])->first();
            if ($item->subsequent_price) {
                $other_fees = $item->price;
                $item->price && $pax--;
                $other_fees += ($pax) * $item->subsequent_price;
            } else {
                $other_fees = $pax * $item->price;
            }
        }

        $this->crud->getRequest()->request->add(
            [
                'facility_agreement' => $facility_agreement,
                'memo_charge' => $memo_charge,
                'stamp_duty_facility_agreement' => ((float) Setting::get("sd_fa") / 100) * $amount,
                'other_fees' => $other_fees
            ]
        );

        $response = $this->traitUpdate();
        // do something after save
        return $response;
    }
}
