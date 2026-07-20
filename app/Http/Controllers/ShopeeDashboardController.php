<?php

namespace App\Http\Controllers;

class ShopeeDashboardController extends Controller
{
    public function index()
    {
        return view('shopee.dashboard.dashboard');
    }
}