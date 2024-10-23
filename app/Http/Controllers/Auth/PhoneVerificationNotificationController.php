<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PhoneVerificationNotificationController extends Controller
{
    /**
     * Verify the user's phone using the provided code.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6', // Kodun 6 haneli olduğunu varsayıyoruz
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>
                __('auth.invalid_code')
            ]);
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['auth' => __('auth.please_login')]);
        }

        $code = UserCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('type', 'phone')
            ->first();

        if (!$code) {
            return response()->json([
                "success" => false,
                "message" =>
                __('auth.invalid_code')
            ]);
        }

        DB::beginTransaction();

        try {
            $code->delete();

            $user->update(['is_verified' => true]);

            DB::commit();
            return response()->json([
                "success" => true,
                "message" =>
                __('auth.phone_verified_successfully'),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();


            return response()->json([
                "success" => false,
                "message" =>
                __('auth.phone_verification_failed')
            ]);
        }
    }
}
