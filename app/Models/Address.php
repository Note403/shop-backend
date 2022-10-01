<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    public const ID = 'id';
    public const COUNTRY = 'country';
    public const CITY = 'city';
    public const STREET = 'street';
    public const ZIP = 'zip';
    public const HOUSE_NUMBER = 'house_number';
    public const USER_ID = 'user_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'address';

    protected $fillable = [
        self::ID,
        self::COUNTRY,
        self::CITY,
        self::STREET,
        self::ZIP,
        self::HOUSE_NUMBER,
        self::USER_ID,
        self::CREATED_AT,
        self::UPDATED_AT
    ];

    public function user(): BelongsTo
    {
        $this->belongsTo(User::class, self::USER_ID);
    }
}
