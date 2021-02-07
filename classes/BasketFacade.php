<?php namespace Lbaig\Basket\Classes;

use Auth;
use Carbon\Carbon;
use Lbaig\Basket\Models\Basket;
use Lbaig\Basket\Models\Discount;
use Lbaig\Basket\Models\Settings;
use Session;


class BasketFacade
{
    
    public static function get()
    {
        if (Auth::check()) {
            $user = Auth::getUser();
            $basket = Basket::where('user_id', $user->id)->first();
        }
        else {
            $basket = Basket::where('session_id', Session::getId())->first();
        }

        return $basket;
    }

    public static function create()
    {
        if (Auth::check()) {
            $user = Auth::getUser();
            $basket = Basket::firstOrCreate(['user_id' => $user->id]);
        }
        else {
            $basket = Basket::firstOrCreate(['session_id' => Session::getId()]);
        }

        return $basket;
    }

    public static function numberItems($basket = null)
    {
        if (!$basket) {
            $basket = BasketFacade::get();
        }

        $quantity = 0;
        if (!$basket) {
            return $quantity;
        }

        foreach ($basket->items as $item) {
            $quantity += $item->quantity;
        }

        return $quantity;
    }

    public static function subtotal($basket = null)
    {
        if (!$basket) {
            $basket = BasketFacade::get();
        }

        $subtotal = 0;
        if (!$basket) {
            return $subtotal;
        }

        foreach ($basket->items as $item) {
            $subtotal += $item->quantity * $item->productPrice;
        }
        
        return $subtotal;
    }

    public static function tax($basket = null)
    {
        if (!$basket) {
            $basket = BasketFacade::get();
        }

        $taxable = 0;
        if (!$basket) {
            return $taxable;
        }

        foreach ($basket->items as $item) {
            if ($item->product->taxable) {
                $taxable += $item->quantity * $item->productPrice;
            }
        }

        // apply order discounts to taxable amount
        $taxable -= self::orderDiscounts($basket);
        
        $tax_amount = 0.0;
        if (Settings::get('is_tax_origin_based')) {
            $tax_amount = floor(Settings::get('origin_based_tax') * $taxable) / 100;
        }
        
        return $tax_amount;
    }

    public static function orderDiscounts($basket = null)
    {
        if (!$basket) {
            $basket = BasketFacade::get();
        }

        $amount = 0.0;
        
        $now = Carbon::now();
        $discounts = Discount::active()
                   ->where('discount_type', 'order')
                   ->where('since', '<=', $now)
                   ->where('until', '>', $now)
                   ->get();

        foreach ($discounts as $discount) {
            $amount += $discount->is_fixed ? $discount->amount :
                     self::subtotal() * $discount->percent / 100;
        }
        return $amount;
    }
}
