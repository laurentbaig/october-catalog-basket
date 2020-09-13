<?php namespace Lbaig\Basket\Components;

use Cms\Classes\ComponentBase;
use Lbaig\Basket\Classes\BasketFacade;
use Lbaig\Basket\Models\Basket as BasketModel;
use Lbaig\Basket\Models\BasketItem;
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
        /*
        $quantity = 0;
        $basket = BasketModel::where('session_id', Session::getId())->first();
        if (!$basket) {
            return $quantity;
        }

        foreach ($basket->items as $item) {
            $quantity += $item->quantity;
        }
        */
        $quantity = BasketFacade::numberItems();

        return $quantity;
    }

    public function getBasketSubtotal()
    {
        \Log::info('getBasketSubtotal');
        /*
        $subtotal = 0;
        $basket = BasketModel::where('session_id', Session::getId())->first();
        if (!$basket) {
            return $subtotal;
        }

        foreach ($basket->items as $item) {
            $subtotal += $item->quantity * $this->getItemPriceWithOptions($item);
        }
        */
        $subtotal = BasketFacade::subtotal();

        return $subtotal;
    }

    public function getTaxable()
    {
        \Log::info('getTaxable');
        /*
        $taxable = 0;
        $basket = BasketModel::where('session_id', Session::getId())->first();
        if (!$basket) {
            return $taxable;
        }

        foreach ($basket->items as $item) {
            \Log::info($item->product);
            if ($item->product->taxable) {
                $taxable += $item->quantity * $this->getItemPriceWithOptions($item);
            }
        }

        $tax_amount = 0.0;
        if (Settings::get('is_tax_origin_based')) {
            $tax_amount = floor(Settings::get('origin_based_tax') * $taxable) / 100;
        }
        */

        $tax_amount = BasketFacade::tax();
        return $tax_amount;
    }
    
    public function getItemPriceWithOptions(BasketItem $item)
    {
        return $item->productPrice;
    }
    
    public function onAdd()
    {
        $basket_items = Input::get('basket');

        // \Log::info($basket_items);

        // TODO: Add search for user basket
        // $basket = BasketModel::where('session_id', Session::getId())->first();
        $basket = BasketFacade::get();
        if (!$basket) {
            \Log::info('Create new basket');
            // create the basket for the session
            // $basket = BasketModel::create([
            //     'session_id' => Session::getId()
            // ]);
            $basket = BasketFacade::create();
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
            $basketItem->quantity = $item['quantity'];
            $basketItem->save();
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
        \Log::info('Basket::onRun');
        //$this->addJs('/plugins/lbaig/basket/assets/javascript/basket.js');
    }
}

