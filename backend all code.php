<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Agent;
use App\Agency;
use App\User;
use App\AuthTrail;
use DB;
use App\PackageInquiry;
use App\Booking;
use DateTime;
use DatePeriod;
use DateInterval;

class ReportsController extends Controller
{

    public $successStatus = 200;
    public function agentRegistrationsReport(Request $request)
    {
        $from = date($request->startdate);
        $to = date($request->enddate);
        // if($request->type == 1){
            $agents = Agent::select(Agent::raw("(count(id)) as total_agents"),
            Agent::raw("(DATE_FORMAT(created_at, '%d-%m-%Y')) as my_date"))
                    ->groupby('my_date')
                    ->orderBy('my_date', 'desc')
                    ->whereBetween('created_at', [$from, $to])
                    ->paginate(500);
        // }else if($request->type == 2){
        //     $agents = Agent::select(Agent::raw("(count(id)) as total_agents"),
        //     Agent::raw("(DATE_FORMAT(created_at, '%m-%Y')) as my_date"))
        //             ->groupby('my_date')
        //             ->orderBy('id', 'desc')->paginate(10);
        // }

        $respond = 1;
        $message = '';

        return response()
            ->json([
                'respond' => $respond,
                'message' => $message,
                'result'  => $agents,
            ], $this->successStatus);
    }

    public function agencyRegistrationsReport(Request $request)
    {
        $from = date($request->startdate);
        $to = date($request->enddate);
        $agencies = DB::table('agencies')->select(Agency::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date, count(*) as total_agencies"))
            ->orderBy('id', 'desc')
            ->groupBy(User::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
            ->whereBetween('created_at', [$from, $to])
            ->paginate(500);
        $respond = 1;
        $message = '';

        return response()
            ->json([
                'respond' => $respond,
                'message' => $message,
                'result'  => $agencies,
            ], $this->successStatus);
    }

    public function userRegistrationsReport(Request $request)
    {
        $result = [];
        $result['data'] = [];
        $test = [];

        switch($request->type){
            case '1' :  // Daily report
                        $from = date('Y-m-d',strtotime("-7 days"));
                        $to = date('Y-m-d');
                        $begin = new DateTime( $from ); $end = new DateTime( $to );
                
                        $period = new DatePeriod($begin, new DateInterval('P1D'), $end);
                        foreach ($period as $date) {
                            $dates[] = $date->format("Y-m-d");
                        }

                        foreach ($dates as $date) {    
                            $users = DB::table('users')->select(User::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date, count(*) as total_users"))
                                    ->orderBy('id', 'desc')
                                    ->groupBy(User::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
                                    ->whereDate('created_at', $date)
                                    ->paginate(1);
                            if($users->isEmpty()){
                                $result['data'][] = array('date' => $date, 'total_users'=> 0);
                            }else{
                                $users = json_decode(json_encode($users), true);
                                $result['data'][] = $users['data'][0];
                            }        
                                  
                        }

                        break;

            case '2' :  // Weekly report
                        $from = date('Y-m-d',strtotime("-1 months"));
                        $to = date('Y-m-d');
                        $begin = new DateTime( $from ); $end = new DateTime( $to );
                
                        $period = new DatePeriod($begin, new DateInterval('P1W'), $end);
                        foreach ($period as $date) {
                            $dates[] = $date->format("Y-m-d");
                        }

                        foreach ($dates as $date) {
                            $startDate = date('Y-m-d',strtotime("-7 days"));
                            $users = DB::table('users')->select(User::raw("DATE_FORMAT(created_at, '%W') as date, count(*) as total_users"))
                                    ->orderBy('id', 'desc')
                                    ->groupBy(User::raw("DATE_FORMAT(created_at,'%w')"))
                                    ->whereBetween('created_at', [$startDate, $date])   
                                    ->paginate(1);
                            if($users->isEmpty()){
                                $result['data'][] = array('date' => $date, 'total_users'=> 0);
                            }else{
                                $users = json_decode(json_encode($users), true);
                                $result['data'][] = $users['data'][0];
                            }        
                                
                        }

                        break;
                
            case '3' :  // Monthly report
                        $from = date('Y-m-d',strtotime("-10 months"));
                        $to = date('Y-m-d',strtotime("+1 months"));
                        $begin = new DateTime( $from ); $end = new DateTime( $to );
                
                        $period = new DatePeriod($begin, new DateInterval('P1M'), $end);
                        foreach ($period as $date) {
                            $dates[] = $date->format("Y-m-d");
                        }

                        foreach ($dates as $date) { 
                            $Month = date('m', strtotime($date));   
                            $users_data = DB::table('users')->select(User::raw("DATE_FORMAT(created_at, '%M') as date, count(*) as total_users"))
                                    ->orderBy('id', 'desc')
                                    ->groupBy(User::raw("DATE_FORMAT(created_at, '%m-%Y')"))
                                    ->whereMonth('created_at', $Month)
                                    ->paginate(1);
                            if($users_data->isEmpty()){
                                $result['data'][] = array('date' => date('F', strtotime($date)), 'total_users'=> 0);
                            }else{
                                $users_data = json_decode(json_encode($users_data), true);
                                $result['data'][] = $users_data['data'][0];
                            }        
                                
                        }
                        break;
                
            case '4' :  // Yealy report
                        $from = date('Y-m-d',strtotime("-5 years"));
                        $to = date('Y-m-d',strtotime("+1 years"));
                        $begin = new DateTime( $from ); $end = new DateTime( $to );
                
                        $period = new DatePeriod($begin, new DateInterval('P1Y'), $end);
                        foreach ($period as $date) {
                            $dates[] = $date->format("Y-m-d");
                        }

                        foreach ($dates as $date) { 
                            $Year = date('Y', strtotime($date));   
                            $users_data = DB::table('users')->select(User::raw("DATE_FORMAT(created_at, '%Y') as date, count(*) as total_users"))
                                    ->orderBy('id', 'desc')
                                    ->groupBy(User::raw("DATE_FORMAT(created_at, '%Y')"))
                                    ->whereYear('created_at', $Year)
                                    ->paginate(1);
                            if($users_data->isEmpty()){
                                $result['data'][] = array('date' => date('Y', strtotime($date)), 'total_users'=> 0);
                            }else{
                                $users_data = json_decode(json_encode($users_data), true);
                                $result['data'][] = $users_data['data'][0];
                            }        
                                
                        }
                        break;
                
            case '5' :  // Custom report
                        $from = date($request->startdate);
                        $to = date($request->enddate);
                        $begin = new DateTime( $from ); $end = new DateTime( $to );
                
                        $period = new DatePeriod($begin, new DateInterval('P1D'), $end);
                        foreach ($period as $date) {
                            $dates[] = $date->format("Y-m-d");
                        }

                        foreach ($dates as $date) {  
                            $users_data = DB::table('users')->select(User::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date, count(*) as total_users"))
                                            ->orderBy('id', 'desc')
                                            ->groupBy(User::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
                                            ->whereDate('created_at', $date)
                                            ->paginate(1);
                            if($users_data->isEmpty()){
                                $result['data'][] = array('date' => $date, 'total_users'=> 0);
                            }else{
                                $users_data = json_decode(json_encode($users_data), true);
                                $result['data'][] = $users_data['data'][0];
                            }        
                                
                        }
                        break;
        }
        

        
        /*$users = DB::table('users')->select(User::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date, count(*) as total_users"))
            ->orderBy('id', 'desc')
            ->groupBy(User::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
            ->whereBetween('created_at', [$from, $to])
            ->paginate(500);*/

        
        $respond = 1;
        $message = '';

        return response()
            ->json([
                'respond' => $respond,
                'message' => $message,
                'result'  => $result,
            ], $this->successStatus);
    }

    public function agentLoginReport(Request $request)
    {
        $from = date($request->startdate);
        $to = date($request->enddate);
        $reportType = $request->reportType;

        switch($reportType){
            case 1: $authenticatable_type = 'App\Agent';
                    break;
            case 2: $authenticatable_type = 'App\Agency';
                    break;
            case 3: $authenticatable_type = 'App\User';
                    break;

        }

        $conditions = ['authenticatable_type' => $authenticatable_type, 'action' => 'login'];

        $agents_trail = DB::table('auth_trails')
        ->select(DB::raw('DATE(created_at) as date, count(*) as total_users'))
        ->groupBy("date")
        ->where($conditions)
        ->get();
        $respond = 1;
        $message = '';

        return response()
            ->json([
                'respond' => $respond,
                'message' => $message,
                'result'  => $agents_trail,
            ], $this->successStatus);
    }

    public function inquiriesReport(Request $request)
    {
            $from = date($request->startdate);
            $to = date($request->enddate);
    
            $inquiries = DB::table('package_inquiries')->select(PackageInquiry::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date,no_of_people, full_name, phone_number,email_address"))
                ->orderBy('id', 'desc')
                ->whereBetween('created_at', [$from, $to])
                ->paginate(500);
            $respond = 1;
            $message = '';
    
            return response()
                ->json([
                    'respond' => $respond,
                    'message' => $message,
                    'result'  => $inquiries,
                ], $this->successStatus);
    }


    public function BookingsReport(Request $request)
    {
        $from = date($request->startdate);
        $to = date($request->enddate);


        $bookings = DB::table('bookings')->select(Booking::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date, sum(number_of_people) as number_of_people, sum(unit_cost) as total_cost"))                    
            // ->orderBy('id', 'desc')
            ->groupBy(Booking::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
            ->whereBetween('created_at', [$from, $to])
            ->paginate(200);
        $respond = 1;
        $message = '';
 
        return response()
            ->json([
                'respond' => $respond,
                'message' => $message,
                'result'  => $bookings,
            ], $this->successStatus);
    }

    public function PackageReport(Request $request)
    {
        // $from = date($request->startdate);
        // $to = date($request->enddate);

        $packages = DB::table('packages')
        ->join('package_category_package', 'package_id', '=', 'packages.id')
        ->join('package_categories', 'package_categories.id', '=', 'package_category_package.category_id')
        ->join('agents','agents.id','=','packages.agent_id')
        ->join('agencies','agencies.id','=','packages.agency_id')
        ->select('packages.id', 'packages.title as package_title', 'package_categories.title as categories_title','agents.first_name as Agent_Name','agencies.name as Agency_name')
        ->get();
       // $respond = 1;
        $message = '';

        return response()
            ->json([
               // 'respond' => $respond,
                'message' => $message,
                'result'  => $packages,
            ], $this->successStatus);
    }


    
}

