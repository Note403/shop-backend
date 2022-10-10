<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    public const ID = 'id';
    public const ORDER = 'order';
    public const ARTICLE = 'article';
    public const AMOUNT = 'amount';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'order_items';

    protected $fillable = [
        self::ID,
        self::ORDER,
        self::ARTICLE,
        self::AMOUNT,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, Order::ID, self::ORDER);
    }

    public function article(): HasOne
    {
        return $this->hasOne(Article::class, Article::ID, self::ARTICLE);
    }
}
