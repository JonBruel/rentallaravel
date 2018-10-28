<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Customer;
use App\Models\VerifyUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;
use ValidationAttributes;
use Illuminate\Support\Facades\Input;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $this->validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customer',
            'password' => 'required|string|min:6|confirmed',
        ]);
        return $this->validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);


        $verifyUser = VerifyUser::create([
            'customer_id' => $user->id,
            'token' => str_random(40)
        ]);

        Mail::to($user->email)->send(new VerifyMail($user));

        return $user;
    }

    /*
     * Take from: https://www.5balloons.info/user-email-verification-and-account-activation-in-laravel-5-5/
     */
    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now login.";
            }else{
                $status = "Your e-mail is already verified. You can now login.";
            }
        }
        else
        {
            return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }
        return redirect('/login')->with('success', $status)->with('isRedirected',1);
    }

    /**
     * The user has been registered. Overwriting the existing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $this->redirectTo = $request->session()->get('redirectTo', $this->redirectTo);
        Log::notice('Redirecting to: '.$this->redirectTo);

        //Other customer-related settings:
        $user->ownerid = config('app.restrictcustomerstoowner', 0);
        $user->save();
        session()->flash('warning', __('Wellcome, please check you mail box and confirm the mail address. Please fill in the missing information under "My account"'));
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Show the application registration form. Overwritten.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $vattr = new ValidationAttributes(new Customer());
        return view('auth.register', ['vattr' => $vattr, 'name' => '', 'email' => session('email', '')])->withHeader('Cache-Control', 'no-cache, must-revalidate');
    }

    /**
     * Handle a registration request for the application. Overwritten as the original version
     * does not work on smartphones.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        //Redirects back if not validated
        if ($this->validator($request->all())->passes())
        {
            event(new Registered($user = $this->create($request->all())));

            $this->guard()->login($user);

            return $this->registered($request, $user)
                ?: redirect($this->redirectPath());
        }

        $vattr = new ValidationAttributes(new Customer());
        return view('auth.register', ['vattr' => $vattr, 'name' => Input::get('name'), 'email' => Input::get('email')])->withHeader('Cache-Control', 'no-cache, must-revalidate')->withErrors($this->validator);


    }
}
