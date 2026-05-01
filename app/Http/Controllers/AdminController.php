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
            'normalcust' => Customer::getDashboardData('normal',1),
            'normal36p'  => Customer::getDashboardData(null,1,'NP36'),
            'normal60p'  => Customer::getDashboardData(null,1,'NP60'),
            'tatkalcust' => Customer::getDashboardData('tatkal',1),
            'tatkal36p'  => Customer::getDashboardData(null,1,'TP36'),
            'tatkal60p'  => Customer::getDashboardData(null,1,'TP60')
        ];

        $data = [];

        foreach ($charts as $key => $list) {

            $list = $list->reverse()->values();

            $data[$key.'label'] = $list->map(fn($r) => $r->recday.'-'.$r->recmonth)->toArray();
            $data[$key.'data']  = $list->pluck('totaluser')->toArray();
        }

        return view('admin.dashboard', $data);
    }
} 