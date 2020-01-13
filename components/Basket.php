<?php namespace Lbaig\Basket\Components;

use Cms\Classes\ComponentBase;
use Lbaig\Basket\Models\Basket as BasketModel;
use Lbaig\Basket\Models\BasketItem;
use Input;
use Session;


class Basket extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Basket Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function get()
    {
        $basket = BasketModel::where('session_id', Session::getId())->first();
        return $basket;
    }
    
    public function getTotalQuantity()
    {
        $quantity = 0;
        $basket = BasketModel::where('session_id', Session::getId())->first();
        if (!$basket) {
            return $quantity;
        }

        foreach ($basket->items as $item) {
            $quantity += $item->quantity;
        }

        return $quantity;
    }

    public function getBasketSubtotal()
    {
        $subtotal = 0;
        $basket = BasketModel::where('session_id', Session::getId())->first();
        if (!$basket) {
            return $subtotal;
        }

        foreach ($basket->items as $item) {
            $subtotal += $item->quantity * $this->getItemPriceWithOptions($item);
        }
        return $subtotal;
    }

    public function getItemPriceWithOptions(BasketItem $item)
    {
        /*
        $price = $item->product->price;
        foreach ($item->propertyOptions as $option) {
            $price += $option->price;
        }
        return $price;
        */
        return $item->productPrice;
    }
    
    public function onAdd()
    {
        $basket_items = Input::get('basket');

        // TODO: Add search for user basket
        $basket = BasketModel::where('session_id', Session::getId())->first();
        if (!$basket) {
            \Log::info('Create new basket');
            // create the basket for the session
            $basket = BasketModel::create([
                'session_id' => Session::getId()
            ]);
        }

        // now we have a basket. add the item to the basket
        foreach ($basket_items as $basket_item) {
            // $item = BasketItem::firstOrCreate([
            //     'basket_id' => $basket->id,
            //     'product_id' => $basket_item['product_id'],
            // ])->increment('quantity', $basket_item['quantity']);
            $query = BasketItem::where([
                'basket_id' => $basket->id,
                'product_id' => $basket_item['product_id'],
            ]);
            // condition on the query by the property options
            foreach ($basket_item['properties'] as $propid) {
                $query->whereHas('propertyOptions', function ($q) use ($propid) {
                    $q->where('lbaig_catalog_property_options.id', $propid);
                });
            }
            $item = $query->first();
            if ($item) {
                $item->increment('quantity', $basket_item['quantity']);
            }
            else {
                $item = BasketItem::create([
                    'basket_id' => $basket->id,
                    'product_id' => $basket_item['product_id'],
                    'quantity' => $basket_item['quantity']
                ]);
                $item->propertyOptions()->sync($basket_item['properties']);
            }
        }
    }

    public function onUpdate()
    {
        $basket_items = Input::get('basket');

        foreach ($basket_items as $item) {
            $basketItem = BasketItem::find($item['basket_item_id']);
            $basketItem->quantity = $item['quantity'];
            $basketItem->save();
        }
    }

    public function onRemove()
    {
        $basket_items = Input::get('basket');

        foreach ($basket_items as $basket_item_id) {
            $item = BasketItem::find($basket_item_id);
            $item->delete();
        }
    }
}

