<?php

/*
|--------------------------------------------------------------------------
| API STORES
|--------------------------------------------------------------------------
*/

// Versión 1
Route::group(['prefix' => 'v1'], function () {

    // Auth
    Route::get('/version', ['as' => 'api_version', 'uses' => 'LoginController@getVersion']);

});