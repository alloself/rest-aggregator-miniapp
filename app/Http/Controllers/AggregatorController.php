<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;

class AggregatorController extends Controller
{
    public function site(Request $request)
    {
        $yandexMetricaCode = null;

        $any = $request->route('any');
        if (is_string($any) && $any !== '') {
            $slug = explode('/', $any)[0];
            $yandexMetricaCode = Restaurant::query()
                ->where('slug', $slug)
                ->value('yandex_metrica_code');
        }

        return view('site', [
            'yandex_metrica_code' => $yandexMetricaCode,
        ]);
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
