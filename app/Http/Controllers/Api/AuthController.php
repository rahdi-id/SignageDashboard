<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Display;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $code = $request->input('code');
        $display = Display::where('code', $code)->first();

        if ($display) {
            return [
                'status' => true,
                'message' => 'Success'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Wrong code!',
            ];
        }
    }
}
