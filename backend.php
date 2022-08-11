
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
        
