<?php

namespace App\Services;

use Illuminate\Http\Request;

class RequestService
{

    function trimRequest(Request $request) {

        foreach ($request->all() as $key => $value) {
            $request[$key] = trim($value);
        }

        return $request;

    }

}
