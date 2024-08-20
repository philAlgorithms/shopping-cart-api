<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Users\{Admin, Buyer, LogisticsPersonnel, Vendor};
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Password};
use Illuminate\Support\{Str};
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
	public function forgot(Request $request)
	{
		$request->validate(['email' => 'required|email']);

		$status = Password::broker(getBroker($request->email, 'email') ?? 'buyer')->sendResetLink(
			$request->only('email')
		);

		if ($status === Password::RESET_LINK_SENT)
			return response(['message' => __($status)]);
		throw ValidationException::withMessages(['email' => __($status)]);
	}

	public function reset(Request $request)
	{
		$request->validate([
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:8|confirmed',
		]);

		$status = Password::broker(getBroker($request->email, 'email') ?? 'buyer')->reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function (Admin|Vendor|Buyer|LogisticsPersonnel $user, string $password) {
				$user->forceFill([
					'password' => Hash::make($password)
				])->setRememberToken(Str::random(60));

				$user->save();

				event(new PasswordReset($user));
			}
		);

		if ($status === Password::PASSWORD_RESET)
			return response(['message' => __($status)]);
		throw ValidationException::withMessages(['email' => __($status)]);
	}
}
