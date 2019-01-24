<?php

namespace mathewparet\LaravelInvites\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use mathewparet\LaravelInvites\Facades\LaravelInvites;

class LaravelInvitesController extends Controller
{
    public function __construct()
    {
        $this->middleware('signed');
    }

    public function accept(Request $request)
    {
        LaravelInvites::check($request->query('code'), $request->query('email'));
        
        $request->session()->put('_old_input.'.config('laravelinvites.fields.code'), $request->query('code'));
        $request->session()->put('_old_input.'.config('laravelinvites.fields.email'), $request->query('email'));

        return redirect()->route(config('laravelinvites.routes.register'));
    }
}
