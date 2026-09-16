<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Si SMTP falla, Laravel 8 deja un 500. Devolver error usable
     * y apuntar al cambio de contraseña desde el perfil.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        try {
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo al enviar correo de restablecimiento de contraseña', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
            ]);

            return $this->sendResetLinkFailedResponse(
                $request,
                'passwords.mail_failed'
            );
        }

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}
