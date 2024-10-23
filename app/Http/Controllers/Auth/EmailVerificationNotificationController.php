<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;

// Ayarlar modelini ekleyin

class EmailVerificationNotificationController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6', // Kod uzunluğunu ihtiyacınıza göre ayarlayın
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>
                __('auth.invalid_code'),
            ]);
            // return back()->withErrors($validator)->withInput();
        }

        $user = $request->user();

        $code = UserCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('type', 'email')
            ->first();

        if (!$code) {
            return response()->json([
                "success" => false,
                "message" =>
                __('auth.invalid_code'),
            ]);
            // return back()->withErrors(['code' => __('auth.invalid_code')])->withInput();
        }

        DB::beginTransaction();

        try {
            $code->delete();

            $user->update(['email_verified_at' => now()]);

            DB::commit();
            return response()->json([
                "success" => true,
                "message" =>
                __('auth.email_verified_succesfully')
            ]);
            // return redirect()->intended(route('control.dashboard', ['absolute' => false]));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Email verification failed for user ID ' . $user->id . ': ' . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" =>
                __('auth.email_verified_succesfully')
            ]);
            // return back()->withErrors(['error' => __('auth.verification_failed')])->withInput();
        }
    }
}
