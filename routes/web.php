<?php

use App\Models\Contact;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ScanlatorController;
use App\Http\Controllers\ScraperController;

Route::get('/', [HomeController::class, 'home'])->name('home2');

Route::get('/about',[HomeController::class, 'about'])->name('about');

Route::get('/sponsors',[HomeController::class, 'sponsors'])->name('sponsors');

Route::get('/kalender',[HomeController::class, 'kalender'])->name('kalender');

Route::get('/jeugd',[HomeController::class, 'jeugd'])->name('jeugd');

//Route::resource('series', SerieController::class);

Route::get('/series/builder',[ScraperController::class,'Builder'])->name('builder');

Route::name('fanfare.')->prefix('fanfare')->group(function(){
    Route::get('/bestuur',[HomeController::class, 'bestuur'])->name('bestuur');
    Route::get('/dirigent',[HomeController::class, 'dirigent'])->name('dirigent');
    Route::get('/geschiedenis',[HomeController::class, 'geschiedenis'])->name('geschiedenis');
    Route::get('/instrumenten',[HomeController::class, 'instrumenten'])->name('instrumenten');
});

Route::name('praktischeInfo.')->prefix('praktischeInfo')->group(function(){
    Route::get('/belangrijkeDocumenten',[HomeController::class, 'belangrijkeDocumenten'])->name('belangrijkeDocumenten');
    Route::get('/privacyverklaring',[HomeController::class, 'privacyverklaring'])->name('privacyverklaring');
    Route::get('/faq',[HomeController::class, 'faq'])->name('faq');
});

Route::get('/members', [HomeController::class, 'members'])->name('members');

Route::resource('scanlator',ScanlatorController::class);

Route::resource('series',SerieController::class);

Route::resource('posts',PostController::class)
->except(['index'])
->middleware(('admin'));

Route::resource('categories',CategoryController::class)
->except(['index'])
->middleware(('admin'));

Route::resource('questions',QuestionController::class)
->except(['index'])
->middleware(('admin'));

Route::get('contact/create',[ContactController::class,'create'])->name('contact.create')->middleware(('guest'));
Route::post('contact/store',[ContactController::class,'store'])->name('contact.store')->middleware(('guest'));
Route::delete('contact/{contact}',[ContactController::class,'destroy'])->name('contact.destroy')->middleware(('admin'));

Route::match(['get','post'],'/register',[RegisterController::class,'register'])->name('register')
->middleware(('guest'));

Route::match(['get','post'],'/login',[LoginController::class,'login'])->name('login')
->middleware(('guest'));

Route::get('/logout',[AuthenticatedSessionController::class, 'destroy'])->name('logout')
->middleware(('auth'));

Route::get('/dashboard', function () {
    $messages=Contact::all();
    return view('dashboard',['messages'=>$messages]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware(['auth']);
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware(['auth']);
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware(['auth']);
});
Route::get('/profile/{user_id}', [ProfileController::class, 'showProfile'])->name('profile.show');
Route::post('/profile/pro{user_id}', [ProfileController::class, 'promote'])->name('profile.promote')->middleware(['admin']);
Route::post('/profile/dem{user_id}', [ProfileController::class, 'demote'])->name('profile.demote')->middleware(['admin']);

require __DIR__.'/auth.php';

//Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;
// use App\Models\User;

// class AuthController extends Controller
// {
//     public function register(Request $request){
//         if($request->isMethod(('get'))){
//             return view('auth.register');
//         }

//         $request->validate([
//             'name'=>'required',
//             'email'=>'required|email|unique:users',
//             'password'=>'required|min:6',
//         ]);

//         User::create([
//             'name'=>$request->input('name'),
//             'email'=>$request->input('email'),
//             'password'=>Hash::make($request->input('password')),
//         ]);

//         return redirect()
//         ->route('login')
//         ->with('success','Your account has been created! You can now login.');
//     }
//     public function login(Request $request){
//         if($request->isMethod(('get'))){
//             return view('auth.login');
//         }

//         $credentials=$request->validate([
//             'email'=>'required',
//             'password'=>'required',
//         ]);

//         if(Auth::attempt($credentials)){
//             return redirect()
//                 ->route('home2')
//                 ->with('success','You have successfully logged in');
//         }

//         return redirect()
//             ->route('login')
//             ->withErrors('Provided login information is not valid');

//     }
//     public function logout(Request $request){
//         Auth::logout();
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();
//         return redirect()
//             ->route('home2')
//             ->with('success','You have successfully logged out');
//     }
// }
