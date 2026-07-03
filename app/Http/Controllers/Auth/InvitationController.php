<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InvitationController extends Controller
{
    public function show(string $token)
    {
        $user = User::where('invitation_token', hash('sha256', $token))
            ->where('invitation_expires_at', '>', now())
            ->where('is_active', false)
            ->firstOrFail();

        return view('auth.invitation', compact('user', 'token'));
    }

    public function accept(Request $request, string $token)
    {
        $user = User::where('invitation_token', hash('sha256', $token))
            ->where('invitation_expires_at', '>', now())
            ->where('is_active', false)
            ->firstOrFail();

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user->update([
            'password'               => Hash::make($request->password),
            'invitation_token'       => null,
            'invitation_expires_at'  => null,
            'is_active'              => true,
            'email_verified_at'      => now(),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Bienvenue ! Votre compte est maintenant activé.');
    }
}
