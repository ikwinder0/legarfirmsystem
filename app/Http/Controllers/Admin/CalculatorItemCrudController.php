<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CalculatorItemRequest;
use App\Models\Calculator;
use App\Models\CalculatorItem;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class CalculatorItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CalculatorItemCrudController extends CrudController
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
        CRUD::setModel(\App\Models\CalculatorItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/calculator-item');
        CRUD::setEntityNameStrings('calculator item', 'calculator items');
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

        $this->crud->addColumn([
            'label' => 'Calculator',
            'name'  => 'cid',
            'value' => function ($v) {
                return Calculator::find($v->cid)->name;
            }
        ]);
        CRUD::column('section');
        CRUD::column('type_of_price');
        CRUD::column('label');
        CRUD::column('price');

        // dropdown filter
        $this->crud->addFilter(
            [
                'name'  => 'calculator',
                'type'  => 'dropdown',
                'label' => 'Calculator'
            ],
            Calculator::all()->pluck('name', 'id')->toArray(),
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'cid', $value);
            }
        );

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
        CRUD::setValidation(CalculatorItemRequest::class);

        CRUD::addField([
            'label'       => "Calculator",
            'type'        => "select_from_array",
            'name'        => 'cid',
            'options'     => Calculator::all()->pluck('name', 'id'),
        ]);

        $this->crud->getRequest()->cid && $this->crud->modifyField('cid', [
            'value' => $this->crud->getRequest()->cid
        ]);

        $this->crud->addField([
            'label'       => "Section",
            'type'        => "select_from_array",
            'name'        => 'section',
            'options'     => [
                'professional_charges' => "Professional Charges",
                'reimbursements' => "Reimbursements",
                'disbursements' => "Disbursements"
            ],
        ]);
        $this->crud->getRequest()->section && $this->crud->modifyField('section', [
            'value' => $this->crud->getRequest()->section
        ]);

        Widget::add()->type('script')->content(asset('assets/js/create-calc.js'));

        $this->crud->addField([
            'label'       => "Type",
            'type'        => "select_from_array",
            'name'        => 'type_of_price',
            'options'     => [
                "fix_price" => "Fix price",
                "memo_of_transfer" => "Memorandum of Transfer",
                "property_legal_fee" => "Property Legal Fee",
                "pax" => "PAX",
                "min_pp" => "Minimum price and Percentage"
            ],
            'attributes' => [
                "id" => "typeOfPrice",
            ]
        ]);

        // dd($this->crud->getRequest()->q);

        $this->crud->addField([
            'name'  => 'name',
            'type'  => 'hidden',
        ]);

        CRUD::field('label');
        $this->crud->addField([
            'label'       => "Price",
            'type'        => "number",
            'name'        => 'price',
            'hint'       => 'If there is no subsequent price enter the price here.',
            'wrapper'     => [
                'id' => 'price'
            ]
        ]);
        $this->crud->addField([
            'label'       => "Subsequent Price",
            'type'        => "number",
            'name'        => 'subsequent_price',
            'wrapper'     => [
                'id' => 'subsPrice'
            ]
        ]);
        $this->crud->addField([
            'label'       => "Percentage",
            'type'        => "number",
            'name'        => 'percentage',
            'wrapper'     => [
                'id' => 'percentage'
            ]
        ]);
        $this->crud->addField([
            'label'       => "Minumum Price",
            'type'        => "number",
            'name'        => 'min_price',
            'wrapper'     => [
                'id' => 'minPrice'
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
        $this->setupCreateOperation();
    }

    public function store()
    {
        // do something before validation, before save, before everything; for example:
        // $this->crud->removeField('password_confirmation');
        $this->crud->addField('pos');

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

        $cid = $this->crud->getRequest()->cid;
        $type_of_price = $this->crud->getRequest()->type_of_price;

        $pos = CalculatorItem::where('cid', $cid)->pluck('pos')->max() + 1;
        $this->crud->getRequest()->request->add(['pos' => $pos]);

        $label = $this->crud->getRequest()->label;
        $name = implode("_", explode(" ", strtolower($label)));
        $this->crud->getRequest()->request->remove('name');
        $this->crud->getRequest()->request->add(['name' => $name]);

        switch ($cid) {
            case 1:
                $items = \App\Models\Purchase::all();
                $to_add_items = [];

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->purchase_price * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    array_push($to_add_items, [
                        'item_id' => $item->id,
                        'name' => $name,
                        'amount' => $amount ?? $price
                    ]);
                }

                \App\Models\AddedSectionItem::insert($to_add_items);
                break;

            case 2:
                $items = \App\Models\Loan::all();
                $to_add_items = [];

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->loan_amount * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    array_push($to_add_items, [
                        'item_id' => $item->id,
                        'name' => $name,
                        'amount' => $amount ?? $price
                    ]);
                }


                \App\Models\AddedSectionItem::insert($to_add_items);
                break;

            case 3:
                $items = \App\Models\MasterTitleLoan::all();
                $to_add_items = [];

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->loan_amount * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    array_push($to_add_items, [
                        'item_id' => $item->id,
                        'name' => $name,
                        'amount' => $amount ?? $price
                    ]);
                }

                \App\Models\AddedSectionItem::insert($to_add_items);
                break;

            case 4:
                $items = \App\Models\CostOfAssistVendor::all();
                $to_add_items = [];

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->purchase_price * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    array_push($to_add_items, [
                        'item_id' => $item->id,
                        'name' => $name,
                        'amount' => $amount ?? $price
                    ]);
                }


                \App\Models\AddedSectionItem::insert($to_add_items);
                break;

            default:
                # code...
                break;
        }

        $response = $this->traitStore();
        // do something after save
        return $response;
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

        $cid = $this->crud->getRequest()->cid;
        $name = $this->crud->getRequest()->name;
        $this->crud->getRequest()->request->remove('name');
        $type_of_price = $this->crud->getRequest()->type_of_price;

        switch ($cid) {
            case 1:
                $items = \App\Models\Purchase::all();

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->purchase_price * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    if ($amount) {
                        if (isset($item[$name])) {
                            $item[$name] = $amount;
                            $item->save();
                        } else {
                            $item = \App\Models\AddedSectionItem::where([
                                ['item_id', $item->id],
                                ['name', $name]
                            ])->first();
    
                            $item && $amount && $item->update([
                                'amount' => $amount
                            ]);
                        }
                    }
                }

            case 2:
                $items = \App\Models\Loan::all();

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->facility_agreement * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    if ($amount) {
                        if (isset($item[$name])) {
                            $item[$name] = $amount;
                            $item->save();
                        } else {
                            $item = \App\Models\AddedSectionItem::where([
                                ['item_id', $item->id],
                                ['name', $name]
                            ])->first();
    
                            $item && $amount && $item->update([
                                'amount' => $amount
                            ]);
                        }
                    }
                }

            case 3:
                $items = \App\Models\MasterTitleLoan::all();

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->facility_agreement * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    if ($amount) {
                        if (isset($item[$name])) {
                            $item[$name] = $amount;
                            $item->save();
                        } else {
                            $item = \App\Models\AddedSectionItem::where([
                                ['item_id', $item->id],
                                ['name', $name]
                            ])->first();
    
                            $item && $amount && $item->update([
                                'amount' => $amount
                            ]);
                        }
                    }
                }

            case 4:
                $items = \App\Models\CostOfAssistVendor::all();

                foreach ($items as $item) {
                    $pax = $item->pax;
                    $amount = null;

                    $price = (float) $this->crud->getRequest()->price;
                    if ($type_of_price == 'pax') {
                        $subsequent_price = (float) $this->crud->getRequest()->subsequent_price;

                        if ($subsequent_price) {
                            $amount = $price;
                            $amount += ($pax - 1) * $subsequent_price;
                        } else {
                            $amount = $pax * $price;
                        }
                    } else {
                        $amount = $price;
                    }

                    if ($type_of_price == 'min_pp') {
                        $percentage = (float) $this->crud->getRequest()->percentage;
                        $min_price = (float) $this->crud->getRequest()->min_price;

                        if ($percentage && $min_price) {
                            $amount = $item->purchase_price * ($percentage / 100);
                            $amount = max($amount, $min_price);
                        }
                    }

                    if ($amount) {
                        if (isset($item[$name])) {
                            $item[$name] = $amount;
                            $item->save();
                        } else {
                            $item = \App\Models\AddedSectionItem::where([
                                ['item_id', $item->id],
                                ['name', $name]
                            ])->first();
    
                            $item && $amount && $item->update([
                                'amount' => $amount
                            ]);
                        }
                    }
                }

            default:
                # code...
                break;
        }

        $response = $this->traitUpdate();
        // do something after save
        return $response;
    }
}
