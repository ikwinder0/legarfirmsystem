<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\TimeSlot;
use App\Http\Requests\TimeSlotRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TimeSlotCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TimeSlotCrudController extends CrudController
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
        CRUD::setModel(\App\Models\TimeSlot::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/time-slot');
        CRUD::setEntityNameStrings('time slot', 'time slots');
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
            //            [
            //                'label' => 'Case',
            //                'type' => 'select',
            //                'name' => 'case_id',
            //                'entity' => 'caseDetail',
            //                'model' => 'App\Models\CaseDetail'
            //            ],
            [
                'label' => 'Date',
                'type' => 'date',
                'name' => 'date'
            ],
            //            [
            //                'label' => 'Time Slot',
            //                'type' => 'model_function',
            //                'name' => 'time_slot',
            //                'function_name' => 'formatTimeSlot'
            //            ],
            [
                'label' => 'Time Slot',
                'type' => 'json',
                'name' => 'time_slots'
            ],
        ]);

        $this->crud->addButtonFromView('top', 'generate_time_slot', 'generate_time_slot');

        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'date',
            'label' => 'Date range'
        ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->crud->addClause('where', 'date', '>=', $dates->from);
                $this->crud->addClause('where', 'date', '<=', $dates->to . ' 23:59:59');
            });
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(TimeSlotRequest::class);

        $this->crud->addFields([
            //            [
            //                'label'       => "Case",
            //                'type'        => "select2_from_ajax",
            //                'name'        => 'case_id',
            //                'entity'      => 'caseDetail',
            //                'attribute'   => "title",
            //                'data_source' => url("api/v1/cases"),
            //                'placeholder' => 'select case',
            //                'minimum_input_length' => 2
            //            ],
            [
                'name'  => 'date',
                'label' => 'Date',
                'type'  => 'date',
                'attributes' => [
                    'min' => date('Y-m-d')
                ]
            ],
            [
                'name'        => 'time_slots',
                'label'       => "Available time",
                'type'        => 'select2_from_array',
                'options'     => TimeSlot::hourlyTimes(),
                'allows_null' => false,
                'default'     => '10:00',
                'allows_multiple' => true,
            ],
        ]);
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


    protected function setupShowOperation()
    {
        $this->crud->column('date');
        $this->crud->addColumn([
            'type' => 'json',
            'name' => 'time_slots',
            'label' => 'Time Slots'
        ]);
        $this->crud->column('created_at');
        $this->crud->column('updated_at');
    }

    protected function generate()
    {
        $latest_date = TimeSlot::orderBy('date', 'desc')->first()->date;
        $temp_date = Carbon::createFromFormat('Y-m-d', $latest_date)->addDay();
        $end_date = Carbon::createFromFormat('Y-m-d', $latest_date)->addDays(365);
        $datas = [];
        $hourlyTimeSlots = array_keys(TimeSlot::hourlyTimes());

        while ($temp_date->lte($end_date)) {
            $data = [];
            $data['date'] = $temp_date->format('Y-m-d');
            $data['time_slots'] = json_encode($hourlyTimeSlots);
            $temp_date->addDay();

            array_push($datas, $data);
        }

        TimeSlot::insert($datas);

        return redirect()->route('time-slot.index');
    }
}
