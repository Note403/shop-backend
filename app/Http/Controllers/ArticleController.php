<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\ArticleRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Service\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function all(): JsonResponse
    {
        return Response::json(Article::all());
    }

    public function byId(Request $request, string $article_id): JsonResponse
    {
        return Response::json(Article::find($article_id));
    }

    public function byCategory(Request $request, string $category_key): JsonResponse
    {
        if (!ArticleCategory::exists($category_key, 'key'))
            return Response::error('category_not_found');

        return Response::json(Article::query()->where(Article::CATEGORY_KEY, $category_key)->get());
    }

    public function inPriceRange(Request $request, float $min_price, float $max_price): JsonResponse
    {
        if ($min_price < 0 || $max_price <= 0 || $max_price >= $min_price)
            return Response::error('price_range_invalid');

        return Response::json(Article::query()->whereBetween(Article::PRICE, [$min_price, $max_price])->get());
    }

    public function byTitle(Request $request, string $title): JsonResponse
    {
        return Response::json(Article::query()->where(Article::TITLE, $title)->get());
    }

    public function create(ArticleRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $article = Article::create([
                Article::ID => Str::uuid(),
                Article::TITLE => $data[Article::TITLE],
                Article::DESCRIPTION => $data[Article::DESCRIPTION],
                Article::CATEGORY_KEY => $data[Article::CATEGORY_KEY],
                Article::PRICE => $data[Article::PRICE],
                Article::VAT_PERCENT => $data[Article::VAT_PERCENT]
            ]);
        } catch (Exception $e) {
            report($e);
            return Response::error('article_create');
        }

        return Response::json(Article::find($article));
    }

    public function patch(ArticleRequest $request, string $article_id): JsonResponse
    {
        if (Article::find($article_id) == null)
            return Response::error('null_query');

        $data = $request->validated();

        try {
            Article::query()
                ->where(Article::ID, $article_id)
                ->update([
                    Article::TITLE => $data[Article::TITLE],
                    Article::DESCRIPTION => $data[Article::DESCRIPTION],
                    Article::CATEGORY_KEY => $data[Article::CATEGORY_KEY],
                    Article::PRICE => $data[Article::PRICE],
                    Article::VAT_PERCENT => $data[Article::VAT_PERCENT],
                ]);
        } catch (Exception $e) {
            report($e);
            return Response::error('article_patch');
        }

        return Response::success();
    }

    public function delete(Request $request, string $article_id): JsonResponse
    {
        if (Article::find($article_id) == null)
            return Response::error('null_query');

        try {
            Article::query()
                ->where(Article::ID, $article_id)
                ->limit(1)->delete();
        } catch (Exception $e) {
            report($e);
            return Response::error('article_delete');
        }

        return Response::success();
    }
}
