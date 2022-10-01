<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserPostRequest;
use App\Models\Address;
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

    public function create(UserPostRequest $request): JsonResponse
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

        try {
            $address_id = Address::query()->create([
                Address::COUNTRY => $data[Address::COUNTRY],
                Address::CITY => $data[Address::CITY],
                Address::STREET => $data[Address::STREET],
                Address::ZIP => $data[Address::ZIP],
                Address::HOUSE_NUMBER => $data[Address::HOUSE_NUMBER],
                Address::USER_ID => $user_id
            ]);
        } catch (Exception $e) {
            User::query()
                ->where(User::ID, $user_id)
                ->limit(1)->delete();

            return Response::error('user_create');
        }

        return Response::json([
            User::find($user_id),
            Address::find($address_id)
        ]);
    }

    public function createPwResetRequest(Request $request): JsonResponse
    {

    }

    public function delete(Request $request, string $user_id): JsonResponse
    {
        $delete_response = User::query()
            ->where(User::ID, $user_id)
            ->limit(1)->delete();

        if (!$delete_response)
            return Response::error('user_delete');

        $delete_response = Address::query()
            ->where(Address::USER_ID, $user_id)
            ->limit(1)->delete();

        if (!$delete_response)
            return Response::error('user_delete');

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
