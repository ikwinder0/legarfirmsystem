<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PurchaseRequest;
use App\Models\CalculatorItem;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class PurchaseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PurchaseCrudController extends CrudController
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
    CRUD::setModel(\App\Models\Purchase::class);
    CRUD::setRoute(config('backpack.base.route_prefix') . '/purchase');
    CRUD::setEntityNameStrings('quotation (SPA)', 'quotation (SPA)');

    $this->crud->setShowView('purchases.show');

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
    CRUD::column('purchase_price');
    CRUD::column('pax');

    $label = CalculatorItem::where([
      ['cid', 1],
      ['name', 'sale_purchase_fees']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'sale_purchase_fees',
      'label' => $label
    ]);

    $label = CalculatorItem::where([
      ['cid', 1],
      ['name', 'transfer_memo_fee']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'transfer_memo_fee',
      'label' => $label
    ]);

    $label = CalculatorItem::where([
      ['cid', 1],
      ['name', 'ckht_2a']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'ckht_2a',
      'label' => $label
    ]);

    $label = CalculatorItem::where([
      ['cid', 1],
      ['name', 'transfer_memo_stamp_duty']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'transfer_memo_stamp_duty',
      'label' => $label
    ]);

    $label = CalculatorItem::where([
      ['cid', 1],
      ['name', 'other_fees']
    ])->first()->label;
    $this->crud->addColumn([
      'name' => 'other_fees',
      'label' => $label
    ]);

    CRUD::column('created_at');

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
    CRUD::setValidation(PurchaseRequest::class);

    CRUD::field('purchaser');
    CRUD::field('property');
    CRUD::field('purchase_price');
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
    $this->crud->addField(['type' => 'hidden', 'name' => 'sale_purchase_fees']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'transfer_memo_fee']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'ckht_2a']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'transfer_memo_stamp_duty']);
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

    $amount = (int) $this->crud->getRequest()->purchase_price;
    $pax = (int) $this->crud->getRequest()->pax;
    $ckht_2a = 0;
    $other_fees = 0;

    if ($pax > 0) {
      $item = CalculatorItem::where([
        ['cid', 1],
        ['name', 'ckht_2a']
      ])->first();
      if ($item->subsequent_price) {
        $ckht_2a = $item->price;
        $ckht_2a += ($pax - 1) * $item->subsequent_price;
      } else {
        $ckht_2a = $pax * $item->price;
      }

      $item = CalculatorItem::where([
        ['cid', 1],
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
        'sale_purchase_fees' => \App\Models\SalePurchaseAgreement::calculateTotalFees($amount),
        'transfer_memo_fee' => \App\Models\TransferMemo::calculateTotalFees($amount),
        'ckht_2a' => $ckht_2a,
        'transfer_memo_stamp_duty' => \App\Models\TransferMemoStampDuty::calculateTotalFees($amount),
        'other_fees' => $other_fees
      ]
    );

    $response = $this->traitStore();
    // do something after save
    return $response;
  }

  public function update()
  {
    // do something before validation, before save, before everything; for example:
    $this->crud->addField(['type' => 'hidden', 'name' => 'sale_purchase_fees']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'transfer_memo_fee']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'ckht_2a']);
    $this->crud->addField(['type' => 'hidden', 'name' => 'transfer_memo_stamp_duty']);
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

    $amount = (int) $this->crud->getRequest()->purchase_price;
    $pax = (int) $this->crud->getRequest()->pax;
    $ckht_2a = 0;
    $other_fees = 0;

    if ($pax > 0) {
      $item = CalculatorItem::where([
        ['cid', 1],
        ['name', 'ckht_2a']
      ])->first();
      if ($item->subsequent_price) {
        $ckht_2a = $item->price;
        $ckht_2a += ($pax - 1) * $item->subsequent_price;
      } else {
        $ckht_2a = $pax * $item->price;
      }

      $item = CalculatorItem::where([
        ['cid', 1],
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
        'sale_purchase_fees' => \App\Models\SalePurchaseAgreement::calculateTotalFees($amount),
        'transfer_memo_fee' => \App\Models\TransferMemo::calculateTotalFees($amount),
        'ckht_2a' => $ckht_2a,
        'transfer_memo_stamp_duty' => \App\Models\TransferMemoStampDuty::calculateTotalFees($amount),
        'other_fees' => $other_fees
      ]
    );

    $response = $this->traitUpdate();
    // do something after save
    return $response;
  }
}
