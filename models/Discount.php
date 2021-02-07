<?php namespace Lbaig\Basket\Models;

use Model;

/**
 * Discount Model
 */
class Discount extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lbaig_basket_discounts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

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
    protected $appends = [];

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
        'category' => 'Lbaig\Catalog\Models\Category',
        'product' => 'Lbaig\Catalog\Models\Product'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function filterFields($fields, $context = null)
    {
        \Log::info('Discount::filterFields');
        if ($this->product_id) {
            $this->load('product');
            $fields->amount->config['max'] = $this->product->price;
        }
    }

    public function getAmountOffAttribute($value)
    {
        return $this->is_fixed ? $this->amount :
            ($this->percent * $this->product->price) / 100;
    }

    public function getOrdersCountAttribute()
    {
        return Order::whereHas('items', function ($query) {
            $query->where('product_id', $this->product_id);
        })
            ->where('created_at', '>=', $this->since)
            ->where('created_at', '<', $this->until)
            ->count();
    }

    public function categoryProductAmountOff($product)
    {
        return $this->is_fixed ? $this->amount :
            ($this->percent * $product->price) / 100.0;
    }

    public function scopeActive($query)
    {
        $query->where('active', 1);
    }
}
