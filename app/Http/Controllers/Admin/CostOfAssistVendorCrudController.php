<?php

namespace App\Http\Controllers\Admin;

use App\Models\CalculatorItem;
use App\Http\Requests\CostOfAssistVendorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class CostOfAssistVendorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CostOfAssistVendorCrudController extends CrudController
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
    CRUD::setModel(\App\Models\CostOfAssistVendor::class);
    CRUD::setRoute(config('backpack.base.route_prefix') . '/cost-of-assist-vendor');
    CRUD::setEntityNameStrings('cost of assist vendor', 'cost of assist vendors');

    $this->crud->setShowView('cost_vendor.show');

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
//    CRUD::column('purchaser');
    $this->crud->addColumn([
        'name'  => 'purchaser',
        'label' => 'Vendor',
        'type'  => 'text',
    ]);
    CRUD::column('property');
//    CRUD::column('purchase_price');
    $this->crud->addColumn([
        'name'  => 'purchase_price',
        'label' => 'Selling price',
        'type'  => 'text',
    ]);
    CRUD::column('pax');

    $label = CalculatorItem::where([
      ['cid', 4],
      ['name', 'ckht_1a']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'ckht_1a',
      'label' => $label
    ]);

    $label = CalculatorItem::where([
      ['cid', 4],
      ['name', 'ckht_3']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'ckht_3',
      'label' => $label
    ]);

    $label = CalculatorItem::where([
      ['cid', 4],
      ['name', 'bankruptcy_search']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'bankruptcy_search',
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
    CRUD::setValidation(CostOfAssistVendorRequest::class);

//    CRUD::field('purchaser');
    CRUD::addField([
          'name'      => 'purchaser',
          'label'     => 'Vendor',
          'type'      => 'text'
    ]);
    CRUD::field('property');
//    CRUD::field('purchase_price');
    CRUD::addField([
          'name'      => 'purchase_price',
          'label'     => 'Selling price',
          'type'      => 'text'
    ]);
    CRUD::field('pax');

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
    $this->crud->addField(['type' => 'hidden', 'name' => 'ckht_1a']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'ckht_3']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'bankruptcy_search']);

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

    $amount = (int) $this->crud->getRequest()->purchase_price;
    $pax = (int) $this->crud->getRequest()->pax;
    $ckht_1a = 0;
    $ckht_3 = 0;
    $b_search = 0;
    if ($pax > 0) {
      $item = CalculatorItem::where([
        ['cid', 4],
        ['name', 'ckht_1a']
      ])->first();
      if ($item->subsequent_price) {
        $ckht_1a = $item->price;
        $ckht_1a += ($pax - 1) * $item->subsequent_price;
      } else {
        $ckht_1a = $pax * $item->price;
      }

      $item = CalculatorItem::where([
        ['cid', 4],
        ['name', 'ckht_3']
      ])->first();
      if ($item->subsequent_price) {
        $ckht_3 = $item->price;
        $ckht_3 += ($pax - 1) * $item->subsequent_price;
      } else {
        $ckht_3 = $pax * $item->price;
      }

      $item = CalculatorItem::where([
        ['cid', 4],
        ['name', 'bankruptcy_search']
      ])->first();
      if ($item->subsequent_price) {
        $b_search = $item->price;
        $item->price && $pax--;
        $b_search += ($pax) * $item->subsequent_price;
      } else {
        $b_search = $pax * $item->price;
      }
    }

    $this->crud->getRequest()->request->add(
      [
        'ckht_1a' => $ckht_1a,
        'ckht_3' => $ckht_3,
        'bankruptcy_search' => $b_search
      ]
    );

    $response = $this->traitStore();
    // do something after save
    return $response;
  }

  public function update()
  {
    // do something before validation, before save, before everything; for example:
    $this->crud->addField(['type' => 'hidden', 'name' => 'ckht_1a']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'ckht_3']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'bankruptcy_search']);

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

    $amount = (int) $this->crud->getRequest()->purchase_price;
    $pax = (int) $this->crud->getRequest()->pax;
    $ckht_1a = 0;
    $ckht_3 = 0;
    $b_search = 0;
    if ($pax > 0) {
      $item = CalculatorItem::where([
        ['cid', 4],
        ['name', 'ckht_1a']
      ])->first();
      if ($item->subsequent_price) {
        $ckht_1a = $item->price;
        $ckht_1a += ($pax - 1) * $item->subsequent_price;
      } else {
        $ckht_1a = $pax * $item->price;
      }

      $item = CalculatorItem::where([
        ['cid', 4],
        ['name', 'ckht_3']
      ])->first();
      if ($item->subsequent_price) {
        $ckht_3 = $item->price;
        $ckht_3 += ($pax - 1) * $item->subsequent_price;
      } else {
        $ckht_3 = $pax * $item->price;
      }

      $item = CalculatorItem::where([
        ['cid', 4],
        ['name', 'bankruptcy_search']
      ])->first();
      if ($item->subsequent_price) {
        $b_search = $item->price;
        $item->price && $pax--;
        $b_search += ($pax) * $item->subsequent_price;
      } else {
        $b_search = $pax * $item->price;
      }
    }

    $this->crud->getRequest()->request->add(
      [
        'ckht_1a' => $ckht_1a,
        'ckht_3' => $ckht_3,
        'bankruptcy_search' => $b_search
      ]
    );

    $response = $this->traitUpdate();
    // do something after save
    return $response;
  }
}
