<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboards.admin');
    }

    public function manager()
    {
        return view('dashboards.manager');
    }

    public function staffProduction()
    {
        return view('dashboards.staff-production');
    }

    public function staffQC()
    {
        return view('dashboards.staff-qc');
    }

    public function staffWarehouse()
    {
        return view('dashboards.staff-warehouse');
    }
}
