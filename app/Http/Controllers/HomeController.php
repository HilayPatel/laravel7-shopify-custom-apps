<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
//         $shop = Auth::user();
//         $domain = $shop->getDomain()->toNative();
//         $shopApi = $shop->api()->rest('GET', '/admin/shop.json')['body']['shop'];
        
//         file_put_contents("/var/log/webhook_error.log", "Shop {$domain}'s object:" . json_encode($shop) ."\n",FILE_APPEND);
//         file_put_contents("/var/log/webhook_error.log", "Shop {$domain}'s API objct:" . json_encode($shopApi) ."\n",FILE_APPEND);
        
        
        return view('home.index');
    }
}
