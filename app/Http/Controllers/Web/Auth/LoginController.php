<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Imagen;
use App\Models\Rol;
use App\Models\Usuario;
use Doctrine\DBAL\Driver\PDOException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use SocialAuth;
use SocialNorm\Exceptions\ApplicationRejectedException;
use SocialNorm\Exceptions\InvalidAuthorizationCodeException;
use Uuid;
use Mail;
use Log;
use Redirect;
use Auth;
use Exception;

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
    protected $redirectTo = '/inicio';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if(Auth::guard('web')->user()->roles()->whereIn('id', [Rol::VIAJERO_POTENCIAL, Rol::VIAJERO])->first()) {
                Auth::guard('driver')->login(Auth::guard('web')->user());
            }
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function getFacebook()
    {
        try {
            SocialAuth::login('facebook', function ($usuario, $detalles) {
                Log::info($detalles->raw);
                // Usuario
                if(is_null($usuario->usuario)) {
                    $nickname = $detalles->nickname ? substr(str_slug($detalles->nickname, '.'), 0, 16) : $detalles->raw()['first_name'].'_'.str_random(3);

                    $savedUser = Usuario::where('usuario', $nickname)->first();

                    if($savedUser) {
                        $nickname = $detalles->raw()['first_name'].'_'.str_random(5);
                    }

                    if($detalles->email) {
                        $usuario->email = $detalles->email;
                    }

                    $usuario->usuario = $nickname;
                    $usuario->identificador = Uuid::generate();

                    if(!$usuario->configuracion) {
                        $configuracion = new Configuracion();
                    } else {
                        $configuracion = $usuario->configuracion;
                    }

                    // Configuracion
                    $configuracion->nombre = $detalles->raw()['first_name'];
                    $configuracion->apellidos = $detalles->raw()['last_name'];

                    if(array_key_exists('birthday', $detalles->raw)) {
                        $configuracion->fecha_nacimiento = date('Y-m-d', strtotime($detalles->raw()['birthday']));
                    }

                    // Imagen
                    $imagen = new Imagen();
                    $imagen->path = $detalles->avatar;
                    $imagen->usuario()->associate($usuario);

                    $imagen->save();
                    $imagen->usuario()->associate($usuario);
                    $usuario->save();
                    $usuario->configuracion()->save($configuracion);

                    $usuario->roles()->attach(Rol::USUARIO);
                    $usuario->roles()->attach(Rol::CLIENTE_POTENCIAL);

                    // Enviamos email de bienvenida si tenemos el permiso del email
                    if(!is_null($usuario->email)) {

                        $ruta = route('validacion_email', ['codigo' => $usuario->identificador]);
                        $usuario = Usuario::find($usuario->id);
                        Mail::send('email.bienvenida', ['usuario' => $usuario, 'ruta' => $ruta], function ($m) use ($usuario) {
                            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                            if(!is_null($usuario->configuracion)) {
                                $m->to($usuario->email, $usuario->configuracion->nombre)->subject('Por favor, confirma tu email');
                            } else {
                                $m->to($usuario->email)->subject('Por favor, confirma tu email');
                            }
                        });
                    }

                } else {
                    $savedUser = Usuario::where('usuario', $detalles->nickname ? substr(str_slug($detalles->nickname, '.'), 0, 16) : $detalles->raw()['first_name'].'_'.str_random(3))->first();
                    if($savedUser && !$savedUser->email && $detalles->email) {
                        $usuario->email = $detalles->email;
                        $usuario->save();
                    }
                    if(!$savedUser->hasRole(Rol::CLIENTE_POTENCIAL)) {
                        $savedUser->roles()->attach(Rol::CLIENTE_POTENCIAL);
                    }
                    Log::info('Detalles', ['raw' => $detalles->raw]);
                    Log::info('Fecha actual usuario', ['fecha_actual' => $usuario->configuracion->fecha_nacimiento]);
                    if(array_key_exists('birthday', $detalles->raw) && $usuario->configuracion->fecha_nacimiento == '0000-00-00') {
                        Log::info('Tiene birthday');
                        $usuario->configuracion->fecha_nacimiento = date('Y-m-d', strtotime($detalles->raw()['birthday']));
                        $usuario->configuracion()->save($usuario->configuracion);
                    }
                }

                Auth::guard('web')->login($usuario);

            });
        } catch (ApplicationRejectedException $e) {
            abort(403, 'Aplicación no autorizada');
        } catch (InvalidAuthorizationCodeException $e) {
            abort(403, 'Código de autorización no válido');
        } catch (\OAuthException $e) {
            abort(403, 'Código de autorización no válido');
        } catch (RequestException $e) {
            abort(403, 'Código de autorización no válido');
        } catch(Exception  $e) {
            // Si el usuario ya esta registrado
            // 1. Miramos si la excepción es un integrity constraint por duplicado
            Log::info('Usuario registrado anteriormente de forma tradicional. Código de error '.$e->getCode());
            if($e->getCode() == 23000) {
                // 2. Recuperamos el email de los bindings
                Log::info('Bindings', ['bindings' => $e->getBindings()]);
                $mail = $e->getBindings()[0];
                // 3. Volvemos a comprobar si está duplicado
                if(!is_null(Usuario::where('email', $mail)->first())) {
                    // 4. Redireccionamos a página de error
                    return view('errors.facebookUserExists');
                }
            }
            abort(401, 'Se ha producido un error');
        }


        return redirect()->route('inicio');
    }

    public function getLinkedin()
    {
        try {
            SocialAuth::login('linkedin', function ($usuario, $detalles) {



            });
        } catch (ApplicationRejectedException $e) {
            abort(403, 'Aplicación no autorizada');
        } catch (InvalidAuthorizationCodeException $e) {
            abort(403, 'Código de autorización no válido');
        } catch(QueryException $e) {
            abort(401, 'Se ha producido un error');
        }

        return Redirect::intended();
    }

}
