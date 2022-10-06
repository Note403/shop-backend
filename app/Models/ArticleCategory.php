<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends Model
{
    use HasFactory;

    public const ID = 'id';
    public const KEY = 'key';
    public const NAME = 'name';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'article_categories';

    protected $fillable = [
        self::ID,
        self::KEY,
        self::NAME,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, Article::CATEGORY_KEY, self::KEY);
    }

    /**
     * @param string $category => can either be the key or name of the category
     * @param string|null $only => value can bey 'key' or 'name', if set the function will only check for the
     *                             given column
     * @return bool
     */
    public static function exists(string $category, string $only = null): bool
    {
        $query = self::query();

        if ($only != null) {
            if ($only == 'key')
                $query->where(self::KEY, $category);

            if ($only == 'name')
                $query->where(self::NAME, $category);
        } else {
            $query
                ->where(self::KEY, $category)
                ->orWhere(self::NAME, $category);
        }


        return $query->get()->isNotEmpty();
    }
}
