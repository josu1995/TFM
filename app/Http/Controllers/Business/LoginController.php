<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/business/envios/pendientes-pago';
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        
        $redirectTo = route('business_envios_pendientes_pago');
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        Log::info('[Loggin Business]');
        Session::put('menu',1);
        if(Session::get('locale') == null){
            Session::put('locale','es');
        }
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            //Auth::guard('web')->user()->roles()->whereIn('id', [Rol::ADMINISTRADOR])->first()
            Log::info('Auth',array(Auth::user()));
            Auth::guard('business')->login(Auth::guard('web')->user());
            if(Auth::user()->rol == 1) {

                return $this->sendLoginResponse($request);
                
            }else{

                return redirect()->route('usuario_get_estudios');

            }
            
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function validarLogin(Request $request)
    {
        if(Auth::once(['email' => $request->get('email'), 'password' => $request->get('password')])) {
            //Auth::user()->hasRole(Rol::ADMINISTRADOR)
            if(true) {
                return response()->json('Credenciales válidas', 200);
            } else {
                return response()->json(Lang::get('auth.error.permisos.business'),403);
            }
        }

        return response()->json('Credenciales inválidas',401);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect()->route('business_landing_index');
    }

}
