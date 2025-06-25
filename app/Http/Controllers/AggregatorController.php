<?php

namespace App\Http\Controllers;

class AggregatorController extends Controller
{
    public function site()
    {
        return view('site');
    }

    public function account()
    {
        return view('account');
    }

    public function admin()
    {
        return view('admin');
    }
}
