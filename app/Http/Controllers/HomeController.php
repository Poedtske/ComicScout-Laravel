<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

class HomeController extends Controller
{
    //controller methods= actions
    public function home() {
        $posts=Post::all();
        return view('home',['posts'=>$posts]);
    }

    public function faq() {
        $categories=Category::all();
        return view('praktischeInfo.faq',['categories'=>$categories]);
    }

    public function about() {
        return view('about');
    }

    public function sponsors() {
        return view('sponsors');
    }

    public function jeugd() {
        return view('jeugd');
    }

    public function kalender() {
        $posts=Post::all();
        return view('kalender',['posts'=>$posts]);
    }

    public function members(){
        $users=User::all();
        return view('members',['users'=>$users]);
    }

    public function bestuur(){
        return view('fanfare.bestuur');
    }

    public function dirigent(){
        return view('fanfare.dirigent');
    }

    public function geschiedenis(){
        return view('fanfare.geschiedenis');
    }

    public function instrumenten(){
        return view('fanfare.instrumenten');
    }

    public function belangrijkeDocumenten(){
        return view('praktischeInfo.belangrijkeDocumenten');
    }

    public function privacyverklaring(){
        return view('praktischeInfo.privacyverklaring');
    }

    public function profile(){
        return view('profile');
    }

}
/**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     return view('home');
    // }
