<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;

class HomeController extends Controller
{

    private $businessRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(BusinessRepository $businessRepository) {
        $this->middleware('auth.business');
        $this->businessRepository = $businessRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBadges() {



        return response()->json([
            'ok'
        ]);

    }

}
