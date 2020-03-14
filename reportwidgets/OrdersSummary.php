<?php

namespace Lbaig\Basket\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Carbon\Carbon;
use Lbaig\Basket\Models\Order;

class OrdersSummary extends ReportWidgetBase
{
    public function defineProperties()
    {
        return [
            'range' => [
                'title'             => 'Summary Range',
                'default'           => 'monthsofar',
                'type'              => 'dropdown',
            ],
        ];
    }

    public function render()
    {
        $this->vars['poop'] = 'Hello';


        $since = Carbon::today();
        $until = Carbon::now();

        $range = $this->property('range');
        if ($range == 'monthsofar') {
            $since->startOfMonth();
        }
        else if ($range == 'days30') {
            $since->subDays(30);
        }
        else if ($range == 'month1') {
            $since->startOfMonth()->subMonths(1);
            $until->startOfMonth()->subMonths(1)->endOfMonth();
        }
        else if ($range == 'month3') {
            $since->startOfMonth()->subMonths(3);
            $until->startOfMonth()->subMonths(1)->endOfMonth();
        }
        else if ($range == 'year') {
            $since->startOfMonth()->subMonths(12);
            $until->startOfMonth()->subMonths(1)->endOfMonth();
        }
        else if ($range == 'lastyear') {
            $since->startOfYear()->subYears(1);
            $until->startOfYear()->subYears(1)->endOfYear();
        }
        
        $this->vars['orderTotal'] = Order::where('created_at', '>', $since)
                                  ->where('created_at', '<=', $until)
                                  ->sum('total');
        $this->vars['rangeText'] = $this->getRangeOptions()[$range];
        
        return $this->makePartial('widget');
    }

    public function getRangeOptions()
    {
        return [
            'monthsofar' => 'Month so far',
            'days30' => 'Last 30 days',
            'month1' => 'Last month',
            'month3' => 'Last 3 months',
            'year' => 'Last 12 months',
            'lastyear' => 'Last year'
        ];
    }
}
