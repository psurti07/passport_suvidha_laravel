<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    public function dashboard()
    {
        $charts = [
            'normalcust' => Customer::getDashboardData('normal', 1),
            'normallead' => Customer::getDashboardData('normal', 0),

            'tatkalcust' => Customer::getDashboardData('tatkal', 1),
            'tatkallead' => Customer::getDashboardData('tatkal', 0),
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
