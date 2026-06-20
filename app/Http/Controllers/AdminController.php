<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $charts = [
            'normalcust' => Customer::getDashboardData('normal', 1),
            'normallead' => Customer::getDashboardData('normal', 0),
            'normalpagecust'  => Customer::getDashboardData(null, 1, 'NP'),
            'normalpagelead'  => Customer::getDashboardData(null, 0, 'NP'),
            // 'normal60pcust'  => Customer::getDashboardData(null, 1, 'NP60'),
            // 'normal60plead'  => Customer::getDashboardData(null, 0, 'NP60'),
            'tatkalcust' => Customer::getDashboardData('tatkal', 1),
            'tatkallead' => Customer::getDashboardData('tatkal', 0),
            'tatkalpagecust'  => Customer::getDashboardData(null, 1, 'TP'),
            'tatkalpagelead'  => Customer::getDashboardData(null, 0, 'TP'),
            // 'tatkal60pcust'  => Customer::getDashboardData(null, 1, 'TP60'),
            // 'tatkal60plead'  => Customer::getDashboardData(null, 0, 'TP60')
        ];

        $data = [];

        foreach ($charts as $key => $list) {

            $list = $list->reverse()->values();

            $data[$key . 'label'] = $list->map(fn($r) => $r->recday . '-' . $r->recmonth)->toArray();
            $data[$key . 'data']  = $list->pluck('totaluser')->toArray();
        }

        return view('admin.dashboard', $data);
    }
}
