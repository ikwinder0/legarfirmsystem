<?php

use App\Mail\OrderMade;
use App\Mail\OrderReceiptUploaded;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

// Route to check mail views
Route::get('mailable', function () {
    $order = \App\Models\Order::all()->last();

    return new OrderMade($order);
});

Route::get('order/{folder}/{file}', function ($folder, $file) {
    $url = Storage::url('order/' . $folder . '/' . $file);
    return redirect($url);
});

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('case-size-point-setting', 'CaseSizePointSettingCrudController');
    Route::crud('case-detail', 'CaseDetailCrudController');
    Route::post('case-detail/change-status', 'CaseDetailCrudController@changeStatus')->name('case_detail.change_status');
    Route::crud('time-slot', 'TimeSlotCrudController');
    Route::get('time-slot/generate', 'TimeSlotCrudController@generate');
    Route::crud('appointment', 'AppointmentCrudController');
    Route::get('notification/mark-all-read', 'NotificationCrudController@markAllRead')->name('notifications.markallread');
	Route::post('runner-task/change-status', 'RunnerTaskCrudController@changeStatus')->name('runner_task.change_status');

    Route::group([
        'prefix' => 'reports',
        'namespace' => 'Reports'
    ], function () {
        //        Route::group(['middleware'=>
        //            ['role:Admin|Business Partner']
        //        ],function () {
        Route::crud('point-earned', 'PointEarnedReportController');
        //        });
    });
    Route::crud('notification', 'NotificationCrudController');
    Route::crud('transfer-memo', 'TransferMemoCrudController');
    Route::crud('sale-purchase-agreement', 'SalePurchaseAgreementCrudController');
    Route::crud('purchase', 'PurchaseCrudController');
    Route::crud('transfer-memo-stamp-duty', 'TransferMemoStampDutyCrudController');
    Route::crud('loan', 'LoanCrudController');
    Route::crud('order', 'OrderCrudController');
    Route::crud('calculator', 'CalculatorCrudController');
    Route::crud('master-title-loan', 'MasterTitleLoanCrudController');
    Route::crud('cost-of-assist-vendor', 'CostOfAssistVendorCrudController');
    Route::crud('calculator-item', 'CalculatorItemCrudController');
    Route::get('calculator/{id}/report', 'CalculatorCrudController@report')->name('calculator.report');
    Route::crud('runner-task', 'RunnerTaskCrudController');
    Route::crud('refinance-loan', 'RefinanceLoanCrudController');
});