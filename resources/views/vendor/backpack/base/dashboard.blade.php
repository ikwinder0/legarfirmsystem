@extends(backpack_view('blank'))

@php
    /**$widgets['before_content'][] = [
        'type'        => 'jumbotron',
        'heading'     => trans('backpack::base.welcome'),
        'content'     => trans('backpack::base.use_sidebar'),
        'button_link' => backpack_url('logout'),
        'button_text' => trans('backpack::base.logout'),
    ];*/
$total_outgoing_orders = 0;
$total_incoming_orders = 0;
$total_business_partner = 0;
$total_customers = 0;
$total_case = 0;
$total_task = 0;
$total_appointment_today = 0;
$cases = new \App\Models\CaseDetail();
$order = new \App\Models\Order();
$tasks = new \App\Models\RunnerTask();
$appointments_today = \App\Models\Appointment::where([
    ['appointment_time','>=',date('Y-m-d').' 00:00:00'],
    ['appointment_time', '<=', date('Y-m-d').' 23:59:59']
]);
$orders = $order->select('action_by')->get();

foreach($orders as $item){
	
	if($item->actionby && ($item->actionby->roles[0]->name == 'Senior Business Partner' || $item->actionby->roles[0]->name == 'Business Partner')){
		$total_incoming_orders++;
	}elseif($item->actionby && ($item->actionby->roles[0]->name == 'Admin' || $item->actionby->roles[0]->name == 'Super Admin')){
		$total_outgoing_orders++;
	}
}
$widgets = [];
$auth_id = backpack_user()->id;
switch (strtolower(backpack_user()->roles[0]->name)) {
    case 'admin':
	   
        $total_senior_business_partner = App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Senior Business Partner');
        })->count();
        $total_business_partner = App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Business Partner');
        })->count();
        $total_customers = App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Customer');
        })->count();
        $total_case = $cases->count();
		$total_task = $tasks->count();
        $total_case_completed = $cases->where('status', 'Completed')->count();
        $total_order = $order->count();
        $total_appointment_today = $appointments_today->count();
        array_push($widgets,Widget::make(['link' => route('user.index',['role-name'=>'Senior Business Partner'])])
                        ->type('progress')
                        ->class('card border-0 text-white bg-success')
                        ->progressClass('progress-bar')
                        ->value($total_senior_business_partner)
                        ->description('Total Senior Business Partner'));
        array_push($widgets,Widget::make(['link' => route('user.index',['role-name'=>'Business Partner'])])
                        ->type('progress')
                        ->class('card border-0 text-white bg-primary')
                        ->progressClass('progress-bar')
                        ->value($total_business_partner)
                        ->description('Total Business Partner'));
        array_push($widgets,Widget::make(['link' => route('user.index',['role-name'=>'Customer'])])
                        ->type('progress')
                        ->class('card border-0 text-white bg-secondary text-dark')
                        ->progressClass('progress-bar')
                        ->value($total_customers)
                        ->description('Total Customer'));
        array_push($widgets,Widget::make(['link' => route('order.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-orange')
                ->progressClass('progress-bar')
                ->value($total_order)
                ->description('Total Order'));
        array_push($widgets,Widget::make(['link' => route('case-detail.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-danger')
                ->progressClass('progress-bar')
                ->value($total_case)
                ->description('Total Case'));
        array_push($widgets,Widget::make(['link' => route('case-detail.index',['status'=>'completed'])])
                ->type('progress')
                ->class('card border-0 text-white bg-light-blue')
                ->progressClass('progress-bar')
                ->value($total_case_completed)
                ->description('Total Case Completed'));
        array_push($widgets,Widget::make(['link' => route('appointment.index',['date'=>date('Y-m-d')])])
                ->type('progress')
                ->class('card border-0 text-white bg-warning')
                ->progressClass('progress-bar')
                ->value($total_appointment_today)
                ->description('Total Appointment Today'));
		array_push($widgets,Widget::make(['link' => route('order.index',['type'=>'outgoing'])])
                ->type('progress')
                ->class('card border-0 text-white bg-success')
                ->progressClass('progress-bar')
                ->value($total_outgoing_orders)
                ->description('Total Outgoing Orders'));
		array_push($widgets,Widget::make(['link' => route('order.index',['type'=>'incoming'])])
                ->type('progress')
                ->class('card border-0 text-white bg-info')
                ->progressClass('progress-bar')
                ->value($total_incoming_orders)
                ->description('Total Incoming Orders'));
		array_push($widgets,Widget::make(['link' => route('runner-task.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-primary')
                ->progressClass('progress-bar')
                ->value($total_task)
                ->description('Total Runner Tasks'));
    break;

    case 'super admin':
        
		$total_senior_business_partner = App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Senior Business Partner');
        })->count();
        $total_business_partner = App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'Business Partner');
            })->count();
        $total_customers = App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Customer');
        })->count();
        $total_case = $cases->count();
		$total_task = $tasks->count();
        $total_case_completed = $cases->where('status', 'Completed')->count();
        $total_order = $order->count();
        $total_appointment_today = $appointments_today->count();
        array_push($widgets,Widget::make(['link' => route('user.index',['role-name'=>'Senior Business Partner'])])
                        ->type('progress')
                        ->class('card border-0 text-white bg-success')
                        ->progressClass('progress-bar')
                        ->value($total_senior_business_partner)
                        ->description('Total Senior Business Partner'));
        array_push($widgets,Widget::make(['link' => route('user.index',['role-name'=>'Business Partner'])])
                        ->type('progress')
                        ->class('card border-0 text-white bg-primary')
                        ->progressClass('progress-bar')
                        ->value($total_business_partner)
                        ->description('Total Business Partner'));
        array_push($widgets,Widget::make(['link' => route('user.index',['role-name'=>'Customer'])])
                        ->type('progress')
                        ->class('card border-0 text-white bg-secondary text-dark')
                        ->progressClass('progress-bar')
                        ->value($total_customers)
                        ->description('Total Customer'));
        array_push($widgets,Widget::make(['link' => route('order.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-orange')
                ->progressClass('progress-bar')
                ->value($total_order)
                ->description('Total Order'));
        array_push($widgets,Widget::make(['link' => route('case-detail.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-danger')
                ->progressClass('progress-bar')
                ->value($total_case)
                ->description('Total Case'));
        array_push($widgets,Widget::make(['link' => route('case-detail.index',['status'=>'completed'])])
                ->type('progress')
                ->class('card border-0 text-white bg-light-blue')
                ->progressClass('progress-bar')
                ->value($total_case_completed)
                ->description('Total Case Completed'));
        array_push($widgets,Widget::make(['link' => route('appointment.index',['date'=>date('Y-m-d')])])
                ->type('progress')
                ->class('card border-0 text-white bg-warning')
                ->progressClass('progress-bar')
                ->value($total_appointment_today)
                ->description('Total Appointment Today'));
		array_push($widgets,Widget::make(['link' => route('order.index',['type'=>'outgoing'])])
                ->type('progress')
                ->class('card border-0 text-white bg-success')
                ->progressClass('progress-bar')
                ->value($total_outgoing_orders)
                ->description('Total Outgoing Orders'));
		array_push($widgets,Widget::make(['link' => route('order.index',['type'=>'incoming'])])
                ->type('progress')
                ->class('card border-0 text-white bg-info')
                ->progressClass('progress-bar')
                ->value($total_incoming_orders)
                ->description('Total Incoming Orders'));
		array_push($widgets,Widget::make(['link' => route('runner-task.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-primary')
                ->progressClass('progress-bar')
                ->value($total_task)
                ->description('Total Runner Tasks'));
    break;
    
    case 'business partner':
    case 'senior business partner':
        $total_point_earned = backpack_user()->case_points;
        //  \App\Models\CasePointTransaction::where('business_partner', $auth_id)
        //  ->pluck('case_point')->sum();
        $total_case = $cases->where('introduced_by', $auth_id)->count();
        $total_order = $order->where('business_partner', $auth_id)->count();

        array_push($widgets,Widget::make(['link' => route('order.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-orange')
                ->progressClass('progress-bar')
                ->value($total_order)
                ->description('Total Order'));
        array_push($widgets,Widget::make(['link' => route('case-detail.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-danger')
                ->progressClass('progress-bar')
                ->value($total_case)
                ->description('Total Case'));
        array_push($widgets,Widget::make(['link' => route('point-earned.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-warning')
                ->progressClass('progress-bar')
                ->value($total_point_earned)
                ->description('Total Point Earned'));
    break;
    case 'customer':
        $total_case = $cases->where('customer', $auth_id)->count();
        $total_appointment_today = $appointments_today->where('customer_id', $auth_id)->count();
        array_push($widgets,Widget::make(['link' => route('case-detail.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-danger')
                ->progressClass('progress-bar')
                ->value($total_case)
                ->description('Total Case'));
        array_push($widgets,Widget::make(['link' => route('appointment.index',['date'=>date('Y-m-d')])])
                ->type('progress')
                ->class('card border-0 text-white bg-warning')
                ->progressClass('progress-bar')
                ->value($total_appointment_today)
                ->description('Total Appointment Today'));
    break;
	
	case 'runner':
	    $total_task = $tasks->where('user_id', $auth_id)->count();
		array_push($widgets,Widget::make(['link' => route('runner-task.index')])
                ->type('progress')
                ->class('card border-0 text-white bg-primary')
                ->progressClass('progress-bar')
                ->value($total_task)
                ->description('Total Runner Tasks'));
	break;
	
    default:
    break;
}



    Widget::add()
        ->to('before_content')
        ->type('div')
        ->class('row')
        ->content($widgets);
@endphp

@section('content')
@endsection