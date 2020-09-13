<?php namespace Lbaig\Basket\Components;

use Auth;
use Cms\Classes\ComponentBase;
use Input;
use Lbaig\Basket\Models\Address as AddressModel;
use Lbaig\Basket\Models\Basket as BasketModel;
use Lbaig\Basket\Models\Order as OrderModel;
use Mail;
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
        \Log::info('Order::onCreate');
        $orderIn = input('order');
        \Log::info($orderIn);

        // create the shipping model
        $shipping_address = $orderIn['shipping_address'];
        $shipping = AddressModel::create($shipping_address);

        $orderModelData = [
            'address_id' => $shipping->id,
            'payment_id' => $orderIn['payment_method_id'],
            'email' => $orderIn['email'],
            'phone' => $orderIn['phone'],
            'subtotal' => $orderIn['subtotal'],
            'tax' => $orderIn['tax_amount'],
            'shipping' => 0,
            'total' => $orderIn['total_price']
        ];

        if (Auth::check()) {
            $user = Auth::getUser();
            $orderModalData['user_id'] = $user->id;
        }

        
        // create the order model
        $order = OrderModel::create($orderModelData);

        // reassign items from the basket to the order
        $basket = BasketModel::where('id', $orderIn['basket_id'])->firstOrFail();
        foreach ($basket->items as $item) {
            $item->order_id = $order->id;
            $item->basket_id = null;
            $item->line_price = $item->productPrice;
            $item->save();
        }

        // send confirmation of order email
        $vars = [
            'name' => $order->address->addressee,
            'order' => $order,
            'items' => []
        ];
    
        foreach ($order->items as $item) {
            $detail = [
                'name' => $item->product->name,
                'options' => $item->propertyOptions,
                'quantity' => $item->quantity,
                'unit_price' => number_format($item->line_price, 2),
                'line_price' => number_format($item->quantity * $item->line_price, 2)
            ];
            $vars['items'][] = $detail;
        }
        $vars['total'] = $order->total;

        Mail::send('order::mail.thank-you', $vars, function ($msg) use ($order) {
            $msg->to($order->email);
        });
    }
}
