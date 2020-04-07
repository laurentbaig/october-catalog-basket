<?php namespace Lbaig\Basket\Models;

use Model;

/**
 * BasketItem Model
 */
class BasketItem extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lbaig_basket_basket_items';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'basket_id',
        'product_id',
        'quantity'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [
        'productPrice'
    ];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'basket' => 'Lbaig\Basket\Models\Basket',
        'product' => 'Lbaig\Catalog\Models\Product'
    ];
    public $belongsToMany = [
        'propertyOptions' => ['Lbaig\Catalog\Models\PropertyOption',
                              'table' => 'basket_item_property_option']
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    public function getProductPriceAttribute()
    {
        $price = $this->product->price;
        foreach ($this->propertyOptions as $option) {
            $price += $option->price;
        }
        return $price;
    }
}
