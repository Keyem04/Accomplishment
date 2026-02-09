<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccomplishmentPrintController;

Route::get('/login/authentication', function (Request $request) {
    $username = $request->query('username');
    $password = $request->query('password'); // MD5 from URL

    $user = User::where('UserName', $username)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Unsafe MD5 comparison
    if ($password !== $user->UserPassword) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    Auth::login($user);
    return redirect()->to('/accomplishment-headers');
});

Route::get('accomplishments/print', [AccomplishmentPrintController::class, 'print']);
