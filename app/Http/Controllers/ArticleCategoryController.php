<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleCategories\ACRequest;
use App\Models\ArticleCategory;
use App\Service\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    public function all(): JsonResponse
    {
        return Response::json(ArticleCategory::all());
    }

    public function byName(Request $request, string $name): JsonResponse
    {
        return Response::json(ArticleCategory::query()->where(ArticleCategory::NAME, $name)->get()->first());
    }

    public function byKey(Request $request, string $key): JsonResponse
    {
        return Response::json(ArticleCategory::query()->where(ArticleCategory::KEY, $key)->get()->first());
    }

    public function create(ACRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $ac_id = ArticleCategory::create([
                ArticleCategory::KEY => $data[ArticleCategory::KEY],
                ArticleCategory::NAME => $data[ArticleCategory::NAME],
            ])->id;
        } catch (Exception $e) {
            report($e);
            return Response::error('category_create');
        }

        return Response::json(ArticleCategory::find($ac_id));
    }

    public function patch(ACRequest $request, int $ac_id): JsonResponse
    {
        $data = $request->validated();

        try {
            ArticleCategory::query()
                ->where(ArticleCategory::ID, $ac_id)
                ->update([
                    ArticleCategory::KEY => $data[ArticleCategory::KEY],
                    ArticleCategory::NAME => $data[ArticleCategory::NAME],
                ]);
        } catch (Exception $e) {
            report($e);
            return Response::error('category_added');
        }

        return Response::success();
    }

    public function delete(Request $request, int $ac_id): JsonResponse
    {
        try {
            ArticleCategory::query()
                ->where(ArticleCategory::ID, $ac_id)
                ->limit(1)->delete();
        } catch (Exception $e) {
            report($e);
            return Response::error('ac_delete');
        }

        return Response::success();
    }
}
