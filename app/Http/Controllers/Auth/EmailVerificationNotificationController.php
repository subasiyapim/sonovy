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
    /**
     * Verify the user's email using the provided code.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6', // Kod uzunluğunu ihtiyacınıza göre ayarlayın
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = $request->user();

        $code = UserCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('type', 'email')
            ->first();

        if (!$code) {
            return back()->withErrors(['code' => __('auth.invalid_code')])->withInput();
        }

        DB::beginTransaction();

        try {
            $code->delete();

            $user->update(['email_verified_at' => now()]);

            $settings = Setting::whereIn('key', ['email_verification', 'otp_verification'])
                ->pluck('value', 'key');

            if (
                isset($settings['otp_verification']) &&
                $settings['otp_verification'] == 1 &&
                $user->is_verified == 0
            ) {
                DB::commit();
                return redirect()->route('verification.phone');
            }

            DB::commit();

            return redirect()->intended(route('control.dashboard', ['absolute' => false]));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Email verification failed for user ID '.$user->id.': '.$e->getMessage());

            return back()->withErrors(['error' => __('auth.verification_failed')])->withInput();
        }
    }
}