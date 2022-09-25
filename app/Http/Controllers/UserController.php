<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserCreateRequest;
use App\Models\User;
use App\Service\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function all(Request $request): JsonResponse
    {
        return Response::json(User::all());
    }

    public function create(UserCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $user_id = User::query()->create([
                User::ID => Str::uuid(),
                User::NAME => $data[User::NAME],
                User::EMAIL => $data[User::EMAIL],
                User::PASSWORD => User::hashPassword($data[User::PASSWORD]),
                User::ROLE => $data[User::ROLE]
            ])->id;
        } catch (Exception $e) {
            return Response::error('user_create');
        }

        return Response::json(User::find($user_id));
    }

    public function delete(Request $request, string $user_id): JsonResponse
    {
        $delete_response = User::query()->where(User::ID, $user_id)->limit(1)->delete();

        if (!$delete_response)
            return Response::success('user_delete');

        return Response::success();
    }

    public function getById(Request $request, string $user_id): JsonResponse
    {
        return Response::json(User::find($user_id));
    }

    public function getByRole(Request $request, string $role): JsonResponse
    {
        return Response::json(User::query()->where(User::ROLE, $role)->get());
    }

    public function getBlocked(Request $request): JsonResponse
    {
        return Response::json(User::query()->where(User::BLOCKED, true)->get());
    }

    public function getCreatedInTime(Request $request, string $start_date, string $end_date): JsonResponse
    {
        return Response::json(
            User::query()
                ->whereBetween(User::CREATED_AT, [$start_date, $end_date])
                ->get()
        );
    }
}
