<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Readings;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Route;

class PageController extends Controller
{
    public function Home(Request $request)
    {
        $data = Readings::latest()->get();
        $max = DB::select('SELECT MAX(value) maxVal FROM readings WHERE  DATE(created_at) = CURDATE();');
        $avg = DB::select('SELECT  ROUND(AVG(value), 2) as avg FROM readings WHERE  DATE(created_at) = CURDATE()');
        return view('dashboard', compact('data', 'max', 'avg'));
    }

    public function HomeChart(Request $request)
    {
        $data = DB::select('SELECT value, UNIX_TIMESTAMP(created_at) as ts FROM readings order by created_at desc LIMIT 5');

        // $data = Readings::all()->toArray();
        return response()->json($data);
    }
    public function SWeek(Request $request)
    {
        if (request()->ajax()) {
            $data = DB::select('select week(DATE_SUB(DATE(created_at), INTERVAL 1 DAY)) no_week, DATE_ADD(DATE(created_at), INTERVAL(-WEEKDAY(created_at)) DAY) interv ,COUNT(id) as total 
            from readings 
            group by  no_week, interv
            order by no_week asc');
            // $data = Readings::latest()->get();
            return Datatables::of($data)->addColumn('sdate', function ($row) {

                // $mysqldate = date('Y-m-d H:i:s', strtotime($row->interv));
                $mysqldate = date('Y-m-d ', strtotime($row->interv));
                return $mysqldate;
            })->addColumn('edate', function ($row) {

                $mysqldate = date('Y-m-d', strtotime($row->interv . ' + 6 days'));

                return $mysqldate;
            })->addColumn('action', function ($act) {
                // $id = $act->interv;
                $sdate = date('Y-m-d ', strtotime($act->interv));
                $edate = date('Y-m-d', strtotime($act->interv . ' + 6 days'));
                $id = $sdate . $edate;
                $btn = ' <a href="' . route("week", $id) . '"data-original-title="Detail" class="btn btn-primary mr-1 btn-sm detailWeek">Detail</a>';

                return $btn;
            })
                ->rawColumns(['sdate', 'edate', 'action'])->make(true);
        }

        $max = DB::select('SELECT id, value, classification FROM readings WHERE value = (SELECT  MAX(value) FROM readings) AND CURDATE()');
        $avg = DB::select('SELECT  ROUND(AVG(value), 2) as avg FROM readings WHERE CURDATE()');
        return view('xweek', compact('max', 'avg'));
    }

    public function DetailWeekChart(Request $request, $id)
    {

        $data = DB::select('SELECT value, UNIX_TIMESTAMP(created_at) as ts FROM readings order by created_at desc LIMIT 5');

        // $data = Readings::all()->toArray();
        return response()->json($data);
    }
    public function WeekChart(Request $request)
    {

        $data = DB::select('SELECT id, sensor, value, UNIX_TIMESTAMP(created_at) as ts FROM readings  WHERE CURDATE() order by created_at');
        // $data = Readings::all()->toArray();
        return response()->json($data);
    }
    public function Detail(Request $request)
    {
        if (request()->ajax()) {
            $data = DB::select('SELECT YEAR(created_at) no_year from readings  group by no_year order by no_year asc');

            return Datatables::of($data)->addColumn('action', function ($act) {

                $year = $act->no_year;
                $id = $year;
                $btn = ' <a href="' . route("year", $id) . '"data-original-title="Detail" class="btn btn-primary mr-1 btn-sm detailWeek"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
              </svg></a>';

                return $btn;
            })
                ->rawColumns(['action'])->make(true);
        }

        $max = DB::select('SELECT MAX(value) maxVal FROM readings');
        $avg = DB::select('SELECT  ROUND(AVG(value), 2) as avg FROM readings');
        $most = DB::select("SELECT year(created_at) no_year, MAX(value) maxVal FROM readings GROUP by no_year LIMIT 1");
        return view('detail', compact('max', 'avg', 'most'));
    }
    public function Year(Request $request, $year)
    {
        if (request()->ajax()) {
            $data = DB::select("SELECT month(created_at) no_mon, MONTHNAME(created_at) mon, YEAR(created_at) no_year from readings WHERE YEAR(created_at) = '$year' group by no_mon, mon, no_year order by no_mon asc;");

            return Datatables::of($data)->addColumn('action', function ($act) {

                $id = $act->mon . "-" . $act->no_year;
                $btn = ' <a href="' . route("month", $id) . '"data-original-title="Detail" class="btn btn-primary mr-1 btn-sm detailWeek"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
              </svg></a>';

                return $btn;
            })
                ->rawColumns(['sdate', 'edate', 'action'])->make(true);
        }

        $max = DB::select("SELECT MAX(value) maxVal FROM readings WHERE YEAR(created_at) = '$year' ");
        $avg = DB::select("SELECT  ROUND(AVG(value), 2) as avg FROM readings WHERE YEAR(created_at) = '$year' ");
        $most = DB::select("SELECT  MONTH(created_at) mon, MONTHNAME(created_at) no_month, AVG(value) as avg FROM readings WHERE YEAR(created_at) = '$year' GROUP BY no_month, mon order by avg desc LIMIT 1");
        return view('year', compact('max', 'avg', 'most'));
    }
    public function Month(Request $request, $mon)
    {
        $date = explode("-", $mon);
        if (request()->ajax()) {
            $data = DB::select("SELECT week(DATE_SUB(DATE(created_at), INTERVAL 1 DAY)) no_week, DATE_ADD(DATE(created_at), INTERVAL(-WEEKDAY(created_at)) DAY) interv from readings WHERE MONTHNAME(created_at) = '$date[0]' and YEAR(created_at) = '$date[1]' group by no_week, interv order by no_week asc;");

            return Datatables::of($data)->addColumn('edate', function ($row) {
                $d1 = date('d M Y', strtotime($row->interv));
                $d2 = date('d M Y', strtotime($row->interv . ' + 6 days'));
                $mysqldate = $d1 . " sampai " . $d2;
                return $mysqldate;
            })
                ->addColumn('action', function ($act) {
                    $id = $act->interv;
                    $sdate = date('Y-m-d ', strtotime($act->interv));
                    $edate = date('Y-m-d', strtotime($act->interv . ' + 6 days'));

                    $id = $sdate . "" . $edate;
                    $btn = ' <a href="' . route("week", $id) . '"data-original-title="Detail" class="btn btn-primary mr-1 btn-sm detailWeek"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                  </svg></a>';

                    return $btn;
                })
                ->rawColumns(['sdate', 'edate', 'action'])->make(true);
        }

        $max = DB::select("SELECT MAX(value) maxVal FROM readings WHERE MONTHNAME(created_at) = '$date[0]' and YEAR(created_at) = '$date[1]' ");
        $avg = DB::select("SELECT  ROUND(AVG(value), 2) as avg FROM readings WHERE MONTHNAME(created_at) = '$date[0]' and YEAR(created_at) = '$date[1]' ");
        $most = DB::select("SELECT  week(DATE_SUB(DATE(created_at), INTERVAL 1 DAY)) no_week, AVG(value) as avg FROM readings WHERE MONTHNAME(created_at) = 'December' and YEAR(created_at) = '$date[1]' group by no_week order by avg desc LIMIT 1");
        return view('month', compact('max', 'avg', 'most'));
    }
    public function Week(Request $request, $week)
    {
        // $url = url()->current();
        // $param = parse_url($url, PHP_URL_PATH);
        // $param = str_replace("/detail/", "", $param);
        // $ids = explode(" ", $id);
        $date = explode(" ", $week);
        if (request()->ajax()) {
            // $data = DB::select("SELECT * FROM readings where created_at between '$date[0]' and '$date[1]' order by created_at");
            $data = DB::select("SELECT date(created_at) no_date FROM readings where created_at between '$date[0]' and '$date[1]' GROUP BY DATE(created_at) order by date(created_at);");

            return Datatables::of($data)
                ->addColumn('action', function ($act) {

                    $btn = ' <a href="' . route("day", $act->no_date) . '"data-original-title="Detail" class="btn btn-primary mr-1 btn-sm detailWeek"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                  </svg></a>';

                    return $btn;
                })->addColumn('no_date', function ($fun) {
                    $d1 = date('l, d F Y', strtotime($fun->no_date));
                    return $d1;
                })
                ->rawColumns(['sdate', 'edate', 'action'])->make(true);
        }


        $max = DB::select("SELECT MAX(value) maxVal FROM readings WHERE created_at >= '$date[0]' AND created_at < '$date[1]'");
        $avg = DB::select("SELECT  ROUND(AVG(value), 2) as avg FROM readings WHERE created_at >= '$date[0]' AND created_at < '$date[1]'");
        $most = DB::select("SELECT  DATE(created_at) no_date, AVG(value) as avg FROM readings WHERE created_at >= '$date[0]' AND created_at < '$date[1]' GROUP by no_date ORDER BY avg DESC LIMIT 1");
        return view('week', compact('max', 'avg', 'most'));
    }
    public function Day(Request $request, $day)
    {

        if (request()->ajax()) {
            $data = DB::select("SELECT * FROM readings where DATE(created_at) = '$day' order by created_at");

            return Datatables::of($data)

                ->addColumn('action', function ($act) {

                    $btn = ' <a href="' . route("week", '2020') . '"data-original-title="Detail" class="btn btn-primary mr-1 btn-sm detailWeek"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                  </svg></a>';

                    return $btn;
                })
                ->rawColumns(['sdate', 'edate', 'action'])->make(true);
        }


        $max = DB::select("SELECT MAX(value) maxVal FROM readings where DATE(created_at) = '$day' ");
        $avg = DB::select("SELECT  ROUND(AVG(value), 2) as avg FROM readings where DATE(created_at) = '$day' ");
        $most = DB::select("SELECT CONCAT(Hour, ':00-', Hour+1, ':00') AS Hours , AVG(value) AS `avg` FROM readings RIGHT JOIN ( SELECT 0 AS Hour UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 ) AS AllHours ON HOUR(created_at) = Hour WHERE DATE(created_at) = '$day' OR created_at IS NULL GROUP BY Hour ORDER BY avg desc LIMIT 1;");
        return view('day', compact('max', 'avg', 'most'));
    }
    public function ApiDetail()
    {
        $data = DB::select("SELECT id, sensor, value, UNIX_TIMESTAMP(created_at) as ts  from readings  order by created_at");
        return response()->json($data);
    }
    public function ApiYear($year)
    {
        $data = DB::select("SELECT id, sensor, value, UNIX_TIMESTAMP(created_at) as ts  from readings WHERE YEAR(created_at) = '$year'  order by created_at");
        return response()->json($data);
    }
    public function ApiMonth($mon)
    {
        $date = explode("-", $mon);
        $data = DB::select("SELECT id, sensor, value, UNIX_TIMESTAMP(created_at) as ts FROM readings WHERE MONTHNAME(created_at) = '$date[0]' and YEAR(created_at) = '$date[1]' order by created_at");
        return response()->json($data);
    }
    public function ApiWeek($week)
    {
        $date = explode(" ", $week);
        $data = DB::select("SELECT id, sensor, value, UNIX_TIMESTAMP(created_at) as ts FROM readings where created_at between '$date[0]' and '$date[1]' order by created_at");
        return response()->json($data);
    }
    public function ApiDay($day)
    {
        $data = DB::select("SELECT id, sensor, value, UNIX_TIMESTAMP(created_at) as ts FROM readings where DATE(created_at) = '$day' order by created_at");
        return response()->json($data);
    }
    public function Test()
    {
        $data = DB::select("SELECT year(created_at) no_year, MAX(value) maxVal FROM readings GROUP by no_year LIMIT 1");
        return response()->json($data);
    }
}
