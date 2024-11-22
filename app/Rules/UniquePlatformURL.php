<?php

namespace App\Rules;

use App\Models\Artist;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UniquePlatformURL implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $userId = Auth::id();
        foreach ($value as $platform) {
            $lastSegment = parse_url($platform['url'], PHP_URL_PATH);
            $lastSegment = trim(basename($lastSegment), '/');

            $exists = Artist::where('created_by', $userId)
                ->whereHas('platforms', function ($query) use ($lastSegment) {
                    $query->where('artist_platform.url', 'like', '%'.$lastSegment);
                })
                ->exists();

            if ($exists) {
                $this->errorKey = 'similar_record';
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string[]
     */
    public function message()
    {
        return [
            $this->errorKey => __('control.artist.fields.similar_record'),
        ];
    }
}