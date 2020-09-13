<?php namespace Lbaig\Basket\Components;

use Cms\Classes\ComponentBase;
use Auth;

class OrderHistory extends ComponentBase
{
    public $orders = [];
    
    public function componentDetails()
    {
        return [
            'name'        => 'OrderHistory Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        if (!Auth::check()) {
            \Log::info('not logged in');
            return;
        }
        $user = Auth::getUser();
        \Log::info($user);
        $this->orders = $user->orders;

        \Log::info($user->orders);
    }
}
