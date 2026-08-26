<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DisplayController;
use App\Http\Controllers\Api\HelpdeskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/display', [DisplayController::class, 'index']);

Route::get('/image/{filename}', function ($filename) {
    $path = public_path('images/' . $filename);

    if (!File::exists($path)) {
        return response()->json([
            'message' => 'Image not found'
        ], 404);
    }

    return Response::file($path);
});

// -------------------------------------------------------
// Hotel Helpdesk API (future Flutter IPTV integration)
// -------------------------------------------------------
Route::prefix('helpdesk')->group(function () {
    // List active departments (for Flutter department picker)
    Route::get('/departments', [HelpdeskController::class, 'departments']);

    // Create a new helpdesk chat (guest submits from TV)
    Route::post('/conversations', [HelpdeskController::class, 'createConversation']);

    // Get chat detail + messages (guest tracks chat)
    Route::get('/conversations/{id}', [HelpdeskController::class, 'showConversation']);

    // Guest sends a follow-up message
    Route::post('/conversations/{id}/messages', [HelpdeskController::class, 'sendMessage']);
});

// Serve helpdesk attachments
Route::get('/image/helpdesk/{filename}', function ($filename) {
    $path = public_path('images/helpdesk/' . $filename);

    if (!File::exists($path)) {
        return response()->json([
            'message' => 'File not found'
        ], 404);
    }

    return Response::file($path);
});
