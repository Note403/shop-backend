<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderLog extends Model
{
    use HasFactory;

    public const ID = 'id';
    public const ORDER = 'order';
    public const OLD_DATA = 'old_data';
    public const UPDATED_DATA = 'updated_data';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'order_log';

    protected $fillable = [
        self::ID,
        self::ORDER,
        self::OLD_DATA,
        self::UPDATED_DATA,
        self::CREATED_AT,
        self::UPDATED_AT
    ];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, Order::ID, self::ORDER);
    }
}
