<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const ID = 'id';
    public const PRICE = 'price';
    public const STATUS = 'STATUS';
    public const PAYED = 'payed';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'order';

    protected $fillable = [
        self::ID,
        self::PRICE,
        self::STATUS,
        self::PAYED,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];
}
