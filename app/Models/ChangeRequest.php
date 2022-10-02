<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    use HasFactory;

    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const TOKEN = 'TOKEN';
    public const TYPE = 'type';

    protected $table = 'change_request';

    protected $fillable = [
        self::ID,
        self::USER_ID,
        self::TOKEN,
        self::TYPE,
    ];
}
