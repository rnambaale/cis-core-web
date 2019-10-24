<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cookie;
use App\Repositories\SessionTokenRepository;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $userToken = Cookie::get('user_token');

        // dd(unserialize($userToken));

        // ...

        // $tokenRepo = new SessionTokenRepository();

        // $userToken = $tokenRepo->retrieve();

        // dd($userToken);

        \Illuminate\Support\Facades\Log::debug('token', (array) session('token'));

        // dd(session('token'));

        return view('home');
    }
}
