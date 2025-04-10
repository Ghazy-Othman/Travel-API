<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Admin  : 5|p08YYCMy6YtCR5KDDIqQUMcoO96iENv5byZUXfyR5717b300
// Editor : 4|yofnz1SzM8O2H57CkRtYOIvE3pfCo4kUxt6Fh0Yn505c0494

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        //
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect',
            ], 422);
        }

        //
        $user->tokens()->delete();

        //
        $device = substr($request->userAgent() ?? '', 0, 255);

        //
        return response()->json([
            'token' => $user->createToken($device)->plainTextToken,
        ], 200);
    }
}
