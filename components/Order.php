<?php namespace Lbaig\Basket\Components;

use Cms\Classes\ComponentBase;
use Input;
use Lbaig\Basket\Models\Address as AddressModel;
use Lbaig\Basket\Models\Basket as BasketModel;
use Lbaig\Basket\Models\Order as OrderModel;
use Session;


class Order extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Order Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onCreate()
    {
        \Log::info(Input::all());
        
        /*
        $order = Input::get('order');
        $shipping_address = $order['shipping_address'];

        \Log::info($shipping_address);
        $shipping = AddressModel::create($shipping_address);

        $basket = BasketModel::where('session_id', Session::getId())->first();
        $order = OrderModel::create([
            'address_id' => $shipping->id,
            'payment_id' => $order['payment_method_id'],
            'email' => $order['email'],
            'phone' => $order['phone'],
        ]);

        $subtotal = 0;
        foreach ($basket->items as $item) {
            $item->order_id = $order->id;
            $item->basket_id = null;
            $item->save();
            $subtotal += $item->quantity * $item->productPrice;
        }

        $order->subtotal = $subtotal;
        $order->tax = 0;
        $order->shipping = 0;
        
        $order->total = $order->subtotal +
                      $order->tax +
                      $order->shipping;
        $order->save();

        // send confirmation of order email
        $vars = [
            'name' => $order->address->addressee,
            'order' => $order,
            'items' => []
        ];
    
        $total = 0.0;
        foreach ($order->items as $item) {
            $detail = [
                'name' => $item->product->name,
                'options' => $item->propertyOptions,
                'quantity' => $item->quantity,
                'unit_price' => number_format($item->productPrice, 2),
                'line_price' => number_format($item->quantity * $item->productPrice, 2)
            ];
            $vars['items'][] = $detail;
            $total = $total + $detail['line_price'];
        }
        $vars['total'] = number_format($total, 2);

        Mail::send('order::mail.thank-you', $vars, function ($msg) {
            $msg->to('ljb0904@gmail.com');
        });
        */

    }
}
