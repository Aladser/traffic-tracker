<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfferSubscription;
use App\Models\User;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $lastDay = StatisticController::getDate('-1 day');
        $lastMonth = StatisticController::getDate('-1 month');
        $lastYear = StatisticController::getDate('-1 year');
        $allTime = StatisticController::getDate();
        $times = ['lastDay' => $lastDay, 'lastMonth' => $lastMonth, 'lastYear' => $lastYear, 'allTime' => $allTime];

        return view('pages/statistics', ['advertiser' => $request->user()->advertiser, 'times' => $times, 'userId' => $request->user()->id] );
    }

    /** получить текущее время с учетом часового пояса */
    public static function getDate($period = null)
    {
        $date = new \DateTime();
        $date->modify('+' . env('TIMEZONE') . 'hours');
        if (!is_null($period)) {
            $date->modify($period);
        }
        return $date->format('Y-m-d H:i:s');
    }
}
