<?php namespace Lbaig\Basket\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Lbaig\Basket\Classes\BasketFacade;
use Lbaig\Basket\Models\Basket as BasketModel;
use Lbaig\Basket\Models\BasketItem;
use Lbaig\Basket\Models\Discount;
use Lbaig\Basket\Models\Settings;
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
        // $basket = BasketModel::where('session_id', Session::getId())->first();
        $basket = BasketFacade::get();
        return $basket;
    }
    
    public function getTotalQuantity()
    {
        $quantity = BasketFacade::numberItems();

        return $quantity;
    }

    public function getBasketSubtotal()
    {
        $subtotal = BasketFacade::subtotal();

        return $subtotal;
    }

    public function getTaxable()
    {
        $tax_amount = BasketFacade::tax();
        return $tax_amount;
    }
    
    public function getItemPriceWithOptions(BasketItem $item)
    {
        return $item->productPrice;
    }
    
    public function getOrderDiscounts()
    {
        return BasketFacade::orderDiscounts();
    }

    public function onAdd()
    {
        $basket_items = Input::get('basket');

        // TODO: Add search for user basket
        $basket = BasketFacade::get();
        if (!$basket) {
            \Log::info('Creating new basket');
            // create the basket for the session
            $basket = BasketFacade::create();
        }

        // now we have a basket. add the item to the basket
        foreach ($basket_items as $basket_item) {
            $query = BasketItem::where([
                'basket_id' => $basket->id,
                'product_id' => $basket_item['product_id'],
            ]);
            // condition on the query by the property options
            if (array_key_exists('properties', $basket_item)) {
                foreach ($basket_item['properties'] as $propid) {
                    $query->whereHas('propertyOptions', function ($q) use ($propid) {
                        $q->where('lbaig_catalog_property_options.id', $propid);
                    });
                }
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
                if (array_key_exists('properties', $basket_item)) {
                    $item->propertyOptions()->sync($basket_item['properties']);
                }
            }
        }
    }

    public function onUpdate()
    {
        $basket_items = Input::get('basket');

        foreach ($basket_items as $item) {
            $basketItem = BasketItem::find($item['basket_item_id']);
            if ($basketItem) {
                $basketItem->quantity = $item['quantity'];
                $basketItem->save();
            } else {
                \Log::error("Failed to find a basket item {$item['basket_item_id']}");
                \Log::error($basket_items);
            }
        }
    }

    public function onRemove()
    {
        $basket_items = Input::get('basket');

        foreach ($basket_items as $basket_item_id) {
            $item = BasketItem::find($basket_item_id);
            if ($item) {
                $item->delete();
            }
        }
    }

    public function onRun()
    {
    }
}

