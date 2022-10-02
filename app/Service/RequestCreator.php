<?php

namespace App\Service;


use App\Models\ChangeRequest;
use App\Models\TmpAddress;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;

class RequestCreator
{
    public static function changeAddr(Collection $data): false|ChangeRequest
    {
        try {
            $tmp_id = TmpAddress::query()->create([
                TmpAddress::COUNTRY => $data[TmpAddress::COUNTRY],
                TmpAddress::CITY => $data[TmpAddress::CITY],
                TmpAddress::STREET => $data[TmpAddress::STREET],
                TmpAddress::ZIP => $data[TmpAddress::ZIP],
                TmpAddress::HOUSE_NUMBER => $data[TmpAddress::HOUSE_NUMBER],
                TmpAddress::USER_ID => Auth::user()->id,
            ])->id;
        } catch (Exception $e) {
            return false;
        }

        try {
            $request_id = ChangeRequest::query()->create([
                ChangeRequest::USER_ID => Auth::user()->id,
                ChangeRequest::TOKEN => self::generateToken(),
                ChangeRequest::TYPE => 'ADDR'
            ])->id;
        } catch (Exception $e) {
            TmpAddress::query()
                ->where(TmpAddress::ID, $tmp_id)
                ->limit(1)->delete();

            return false;
        }

        return ChangeRequest::find($request_id);
    }

    public static function changePw(): false|ChangeRequest
    {
        try {
            $request_id = ChangeRequest::query()->create([
                ChangeRequest::USER_ID => Auth::user()->id,
                ChangeRequest::TOKEN => self::generateToken(),
                ChangeRequest::TYPE => 'PW',
            ]);
        } catch (Exception $e) {
            return false;
        }

        return ChangeRequest::find($request_id);
    }

    public function changeMail(): bool
    {
        return true;
    }

    public static function createConfirmURL(ChangeRequest $change_request): string
    {
        $app_url = Env::get('APP_URL');

        $url = match ($change_request->type) {
            'ADDR' => $app_url . 'change_addr_data?t=',
            'PW' => $app_url . 'change_pw?t=',
            'MAIL' => $app_url . 'change_mail?t='
        };

        return $url . $change_request->token;
    }

    private static function generateToken(): string
    {
        return md5(md5(rand(100, 1000000) . rand(100, 1000000)) . substr(md5(rand(100, 1000000)), rand(0, 5)), true);
    }
}
