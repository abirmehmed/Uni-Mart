<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_name', 'customer_email', 'customer_address',
        'total_price_cents', 'source', 'status', 'cashier_id',
    ];

    protected $casts = [
        'total_price_cents' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price_at_time_cents')
            ->withTimestamps();
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public static function generateOrderNumber(string $source): string
    {
        $prefix = $source === 'pos' ? 'POS' : 'UM';

        return sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), Str::upper(Str::random(5)));
    }
}
