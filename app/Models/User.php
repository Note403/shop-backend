<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const ROLE = 'role';
    public const VERIFIED = 'verified';
    public const BLOCKED = 'blocked';
    public const DELETED_AT = 'deleted_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::ID,
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::ROLE,
        self::VERIFIED,
        self::BLOCKED,
        self::DELETED_AT,
        self::CREATED_AT,
        self::UPDATED_AT
    ];

    protected $hidden = [
        self::PASSWORD
    ];

    protected $table = 'user';

    public static function hashPassword(string $password): string
    {
        $salt = substr(md5(rand(1000, 1000000) . rand(1000, 1000000), true), rand(0, 4), rand(4, 8));

        return hash('sha512', $password . $salt) . '$' . $salt;
    }

    public static function hash(string $password, string $salt): string
    {
        return hash('sha512', $password . $salt);
    }
}
