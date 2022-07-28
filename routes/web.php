<?php

use App\Hail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', ArticleController::class);

Route::get('/auth/oauth', function () {
    $hail = new Hail;
    return $hail->authorise();
})->name('oauth');

Route::get('/auth/callback', function (\Illuminate\Http\Request $request) {
        $hail = new Hail;
        $hail->getAccessToken($request->get('code'));
        return redirect('/');
})->name('oauth.callback');