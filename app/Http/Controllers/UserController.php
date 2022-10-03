<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\AddrChangeRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\PwChangeRequest;
use App\Http\Requests\User\UserPostRequest;
use App\Models\Address;
use App\Models\ChangeRequest;
use App\Models\TmpAddress;
use App\Models\User;
use App\Service\RequestCreator;
use App\Service\Response;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            Address::query()->create([
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

        return Response::json($this->mergeUserAddr($user_id));
    }

    public function requestAddressChange(AddrChangeRequest $request): JsonResponse
    {
        if (!$request->authorize())
            return Response::error('unauthorized');

        $data = $request->validated();

        $change_request = RequestCreator::changeAddr($data);

        if ($change_request === false)
            return Response::error();

        // TODO: SEND CHANGE MAIL

        return Response::success();
    }

    public function processAddrChange(Request $request): JsonResponse
    {
        $change_request = ChangeRequest::query()
            ->where(ChangeRequest::TOKEN, $request->input('t'))
            ->get()->first();

        if ($change_request == null)
            return Response::error();

        $tmp_data = TmpAddress::query()
            ->where(TmpAddress::USER_ID, $change_request->user_id)
            ->get()->first();

        if ($tmp_data == null)
            return Response::error();

        try {
            Address::query()
                ->where(Address::USER_ID, $change_request->user_id)
                ->update([
                    Address::COUNTRY => $tmp_data->country,
                    Address::CITY => $tmp_data->city,
                    Address::STREET => $tmp_data->street,
                    Address::ZIP => $tmp_data->zip,
                    Address::HOUSE_NUMBER => $tmp_data->house_number,
                ]);
        } catch (Exception $e) {
            return Response::error();
        }

        TmpAddress::query()
            ->where(TmpAddress::ID, $tmp_data->id)
            ->limit(1)->delete();

        return Response::success();
    }

    public function requestPwChange(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user == null)
            return Response::error();

        $change_request = RequestCreator::changePw();

        // TODO: SEND MAIL

        return Response::success();
    }

    public function processPwChange(PwChangeRequest $request): JsonResponse
    {
        if (!$request->authorize())
            return Response::error();

        $data = $request->validated();

        if (!isset($data['t']))
            return Response::error();

        if ($data['old_pw'] == $data['new_pw'])
            return Response::error('old_new_pw_same');

        if ($data['new_pw'] != $data['new_pw_repeat'])
            return Response::error('new_pw_repeat');

        $change_request = ChangeRequest::query()
            ->where(ChangeRequest::TOKEN, $data['t'])
            ->get()->first();

        if ($change_request == null)
            return Response::error();

        try {
            User::query()
                ->where(User::ID, $change_request->user_id)
                ->update([
                    User::PASSWORD => User::hashPassword($data['new_pw'])
                ]);
        } catch (Exception $e) {
            return Response::error('pw_change');
        }

        return Response::success();
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
        if (!$this->wAddrData($request))
            return Response::json(User::find($user_id));

        return Response::json($this->mergeUserAddr($user_id));
    }

    public function getByRole(Request $request, string $role): JsonResponse
    {
        $users = User::query()->where(User::ROLE, $role)->get();

        if (!$this->wAddrData($request))
            return Response::json($users);

        return Response::json($this->responseFromUsers($users));
    }

    public function getBlocked(Request $request): JsonResponse
    {
        $users = User::query()
            ->where(User::BLOCKED, true)
            ->get();

        if ($this->wAddrData($request))
            return Response::json($users);

        return Response::json($this->responseFromUsers($users));
    }

    public function getCreatedInTime(Request $request, string $start_date, string $end_date): JsonResponse
    {
        $users = User::query()
            ->whereBetween(User::CREATED_AT, [$start_date, $end_date])
            ->get();

        if ($this->wAddrData($request))
            return Response::json($users);

        return Response::json($this->responseFromUsers($users));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()
            ->where(User::EMAIL, $data['user'])
            ->get()->first();

        if ($user == null)
            return Response::error('login');

        $pw_parts = explode('$', $user->password);

        $input_hash = User::hash($data['password'], $pw_parts[1]);

        if ($input_hash != $pw_parts[0])
            return Response::error('login');

        Auth::login($user);

        return Response::success();
    }

    public function logout(Request $request): JsonResponse
    {
        if (Auth::user() == null)
            return Response::error();

        Auth::logout();

        return Response::success();
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();

        if ($user == null)
            return Response::error();

        return Response::json($user->getAttributes());
    }

    private function wAddrData($request): bool
    {
        return ($request->has('address_data') && !!$request->input('address_data'));
    }

    private function responseFromUsers(array|Collection $users): Collection
    {
        $response = array();

        foreach ($users as $user) {
            $response[] = $this->mergeUserAddr($user['id']);
        }

        return new Collection($response);
    }

    private function mergeUserAddr(string $user_id): array
    {
        $user = User::find($user_id)->getAttributes();
        $address = Address::query()
            ->where(Address::USER_ID, $user['id'])
            ->get()->first()->getAttributes();

        $address['addr_id'] = $address['id'];

        unset($address['id']);
        unset($address['user_id']);

        return $user + $address;
    }
}
