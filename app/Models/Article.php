<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    public const ID = 'id';
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';
    public const CATEGORY_KEY = 'category_KEY';
    public const PRICE = 'price';
    public const VAT_PERCENT = 'vat_percent';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'articles';

    public $incrementing = false;

    protected $fillable = [
        self::ID,
        self::TITLE,
        self::DESCRIPTION,
        self::CATEGORY_KEY,
        self::PRICE,
        self::VAT_PERCENT,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];
    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, self::CATEGORY_KEY, ArticleCategory::KEY);
    }
}
