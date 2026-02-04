<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Jobs\SendEmailVerificationCodeJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        try {
            SendEmailVerificationCodeJob::dispatch($user->id)->afterResponse();
        } catch (\Throwable $e) {
            Log::warning('No se pudo encolar el correo de verificación tras el registro.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $e->getMessage(),
            ]);
        }

        return $user;
    }
}
