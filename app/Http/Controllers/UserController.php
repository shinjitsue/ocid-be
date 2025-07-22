<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->isActive()) {
            return response()->json([
                'message' => 'Unauthorized or inactive user.'
            ], 403);
        }

        return response()->json($user->getAttribute('profile'));

    }
}
