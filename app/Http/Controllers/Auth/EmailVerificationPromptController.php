<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();
        return $user->hasVerifiedEmail()
                    ? redirect()->intended($this->targetRoute($user))
                    : view('auth.verify-email');
    }

    private function targetRoute(\App\Models\User $user): string
    {
        return match(true) {
            $user->isGuruPiket() => route('piket.dashboard', absolute: false),
            $user->isTeacher()   => route('teacher.dashboard', absolute: false),
            default               => route('dashboard', absolute: false),
        };
    }
}
