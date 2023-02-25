<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\GeneratorService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mail;
use Validator;

// UUID
use Uuid;
use Redirect;

// Login facebook
use SocialAuth;

// Modelos
use App\Models\Usuario;
use App\Models\Configuracion;
use App\Models\Rol;


use DB;
use Crypt;

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
    protected $redirectTo = '/inicio';
    
    protected $generatorService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GeneratorService $generatorService)
    {
        $this->middleware('guest');
        $this->generatorService = $generatorService;
    }

    public function redirectTo() {
        return $this->redirectTo;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data,
            [
                'nombre'    => 'required|max:255',
                'ciudad'    => 'required|max:255',
                'email'     => 'required|email|max:255|unique_role:' . Rol::CLIENTE_POTENCIAL,
                'password'  => 'required|min:8',
            ]);
    }

    public function register(Request $request) {
        Log::info('piues');
        $usu = new Usuario();
        $usu->password = '1212';
        $usu->email = 'jo';
        $usu->save();

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Usuario
     */
    protected function create(array $data)
    {
        // Creamos usuario
        $usuario = new Usuario();
        $usuario->email = $data['email'];
        $usuario->password = bcrypt($data['password']);
        $usuario->identificador = Uuid::generate();

        $usuario->save();

        // Le asignamos el rol de usuario
        $usuario->roles()->attach(Rol::USUARIO);
        $usuario->roles()->attach(Rol::CLIENTE_POTENCIAL);

        // Creamos configuración para usuario (vacía) y la asignamos
        $configuracion = new Configuracion();
        $nombreSplit = explode(' ', $data['nombre']);
        if (sizeof($nombreSplit) > 0) {
            $configuracion->nombre = ucfirst($nombreSplit[0]);
            for ($i = 1; $i < sizeof($nombreSplit); $i++) {
                if (is_null($configuracion->apellidos)) {
                    $configuracion->apellidos = ucfirst($nombreSplit[$i]);
                } else {
                    $configuracion->apellidos = $configuracion->apellidos . ' ' . ucfirst($nombreSplit[$i]);
                }
            }
        }
        $configuracion->ciudad = ucfirst($data['ciudad']);
        $usuario->configuracion()->save($configuracion);

        // Enviamos mail de bienvenida
        $token = $usuario->identificador;
        $ruta = route('validacion_email', ['codigo' => $token]);

        Mail::send('email.bienvenida', ['usuario' => $usuario, 'ruta' => $ruta], function ($m) use ($usuario) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($usuario->email, $usuario->configuracion->nombre)->subject('Por favor, confirma tu email');
        });

        return $usuario;
    }

    public function getFacebookLead(Request $request) {

        $mode = $request->get('hub_mode');
        $challenge = $request->get('hub_challenge');
        $token = $request->get('hub_verify_token');

        Log::info('Mode.', ['mode' => $mode  ]);
        Log::info('Challenge.', ['challenge' => $challenge  ]);
        Log::info('Token.', ['token' => $token  ]);

        if($mode == 'subscribe' && $token == env('FACEBOOK_LEAD_VERIFY')) {
            return response($challenge, 200);
        }

    }

    public function postFacebookLead(Request $request) {

        Log::info('Request.', ['request' => $request]);

        $signature = '';
        //Get request header signature
        if($request->hasHeader('x-hub-signature')) {
            $signature = str_replace('sha1=', '', $request->header('x-hub-signature'));
        }
        Log::info('X-Hub-Signature', ['signature' => $signature]);
        $generatedSig = hash_hmac('sha1', $request->getContent(), env('FACEBOOK_SECRET'));
        Log::info('Generated Signature', ['signature' => $generatedSig]);

        if($signature == $generatedSig) {

            $entries = $request->get('entry');

            foreach ($entries as $entry) {

                if (isset($entry['changes']) && sizeof($entry['changes']) > 0) {

                    $ad_id = $entry['changes'][0]['value']['ad_id'];

                    $leadgenid = $entry['changes'][0]['value']['leadgen_id'];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.8/' . $leadgenid . '?access_token=' . env('FACEBOOK_LEADS_ACCESS_TOKEN'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $output = curl_exec($ch);

                    curl_close($ch);

                    $lead = json_decode($output);

                    Log::info('Lead.', ['lead' => $lead]);

                    if (isset($lead->field_data)) {

                        $formData = $lead->field_data;

                        $user = new Usuario();
                        $configuration = new Configuracion();
                        $lead = new Lead();

                        foreach ($formData as $item) {
                            if ($item->name == 'full_name') {
                                $lead->full_name = $item->values[0];
                                $names = explode(' ', $item->values[0]);
                                if (sizeof($names) == 1) {
                                    $configuration->nombre = $names[0];
                                    $user->usuario = $names[0];
                                } elseif (sizeof($names) == 2) {
                                    $configuration->nombre = $names[0];
                                    $configuration->apellidos = $names[1];
                                    $user->usuario = $names[0] . '.' . $names[1];
                                } elseif (sizeof($names) >= 3) {
                                    $configuration->nombre = $names[0];
                                    $configuration->apellidos = $names[1] . ' ' . $names[2];
                                    $user->usuario = $names[0] . '.' . $names[1] . '.' . $names[2];
                                }
                            } elseif ($item->name == 'email') {
                                $lead->email = $item->values[0];
                                $user->email = $item->values[0];
                            } elseif ($item->name == 'city') {
                                $lead->city = $item->values[0];
                                $configuration->ciudad = $item->values[0];
                            }
                        }

                        if (!is_null(Usuario::where('email', $user->email)->first())) {
                            Log::info('Email duplicado');
                            return response(200);
                        }

                        Log::info('Email correcto');

                        if (sizeof($user->usuario) > 16) {
                            $user->usuario = substr($user->usuario, 0, 13);
                        }

                        while (!is_null(Usuario::where('usuario', $user->usuario)->first())) {
                            $user->usuario = $user->usuario . $this->generatorService->quickRandom(mt_rand(1, 3));
                        }

                        Log::info('Usuario.', ['usuario' => $user->usuario]);

                        if (sizeof($configuration->nombre) > 255 || sizeof($configuration->apellidos) > 255 || sizeof($configuration->ciudad) > 255 || sizeof($user->email) > 255 || sizeof($user->usuario) > 16) {
                            return response(200);
                        }

                        $user->identificador = Uuid::generate();

                        //Generamos password
                        $password = $this->generatorService->generatePassword(mt_rand(8, 8));
                        $user->password = bcrypt($password);

                        Log::info('Usuario generado');

                        // Guardamos usuario
                        $user->save();
                        $user->configuracion()->save($configuration);

                        $user->roles()->attach(Rol::USUARIO);

                        Log::info('AD ID.', ['ad_id' => $ad_id]);

                        if($ad_id == env('FACEBOOK_LEAD_ID_TRANSPORTISTA')) {
                            $user->roles()->attach(Rol::VIAJERO_POTENCIAL);
                        } else if($ad_id == env('FACEBOOK_LEAD_ID_CLIENTE')) {
                            $user->roles()->attach(Rol::CLIENTE_POTENCIAL);
                        }

                        Log::info('Usuario y configuracion guardados');

                        // Enviamos mail de bienvenida
                        Mail::send('email.bienvenidaLeads', ['usuario' => $user, 'password' => $password], function ($m) use ($user) {
                            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                            $m->to($user->email, $user->configuracion->nombre)->subject('Bienvenido a Transporter');
                        });

                        Log::info('Mail enviado. OK');

                        // Guardamos lead
                        $lead->logged = 0;
                        $lead->user_id = $user->id;
                        $lead->created_at = Carbon::now();
                        $lead->updated_at = Carbon::now();
                        $lead->save();

                        Log::info('Lead guardado');

                        Log::info('Usuario.', ['usuario' => $user]);
                        Log::info('Configuracion.', ['configuracion' => $configuration]);
                    }
                }
            }
        }

        return response(200);
    }



}
