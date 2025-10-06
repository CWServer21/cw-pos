<?php



namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Cache;
use DB;


class LoginController extends Controller

{



    use AuthenticatesUsers;



    protected $redirectTo = null;

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('guest')->except('logout');

    }

    public function showLoginForm()
    {
        //getting theme
        if(isset($_COOKIE['theme']))
            $theme = $_COOKIE['theme'];
        else
            $theme = 'light';
        //get general setting value
        $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
            return DB::table('general_settings')->latest()->first();
        });

        $numberOfUserAccount = \App\Models\User::where('is_active', true)->count();
        return view('backend.auth.login', compact('theme', 'general_setting', 'numberOfUserAccount'));
    }

    public function login(Request $request)
    {
        ///////////////////////
        if (!env('USER_VERIFIED') && $request->has('demo_db_name')) {
            session(['database' => $request->query('demo_db_name')]);
        }
        ///////////////////////

        $input = $request->all();

        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->name, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if(auth()->attempt(array($fieldType => $input['name'], 'password' => $input['password'])))
        {
            setcookie('login_now', 1, time() + (86400 * 1), "/");
            //return redirect('/dashboard');
            return redirect()->intended('/dashboard');
        }
        else {
            return redirect()->route('login')->with('error', __('db.Username And Password Are Wrong.'));
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // Replace with your desired URL
    }
}
