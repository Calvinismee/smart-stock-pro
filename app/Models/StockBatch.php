<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'stock_transaction_id',
        'initial_quantity',
        'remaining_quantity',
        'unit_cost',
        'arrived_at',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'unit_cost' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockTransaction()
    {
        return $this->belongsTo(StockTransaction::class);
    }
}
