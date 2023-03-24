<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use App\Models\dificultad;
use App\Models\idioma;
use App\Models\Usuario;
use App\Models\configuracion;
use App\Models\estudia;
use App\Models\recurso;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class BusinessController extends Controller
{

    

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware('auth.business', ['except' => ['getRegistro', 'postRegistro', 'getRegistroInformacion', 'postRegistroInformacion']]);
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        return view('business.index');
    }

    public function getRegistro() {
        Log::info('Acceder a registro');

        $dificultad = dificultad::all();
        $idioma = idioma::all();


        return view('business.register', ['dificultades' => $dificultad,'idiomas' => $idioma]);

    }

    public function postRegistro(Request $request) {

       
        

        return redirect()->route('business_register_informacion', ['state' => $state]);
    }

    public function getRegistroInformacion(Request $request) {

        $state = $request->get('state');

        if(!$state) {
            abort(404);
        }

        try {
            $state = \Crypt::decrypt($state);
        } catch (DecryptException $e) {
            abort(404);
        }

        return view('business.register2', ['state' => $request->get('state')]);
    }

    public function postRegistroInformacion(Request $request) {
       
        Log::info('Creando registro');

        $validator = Validator::make($request->all(), [
               
            'nuevo' => 'required|min:6|confirmed',
            'nuevo_confirmation' => 'required',
            'nombre' => 'required|min:3|max:32',
            'apellido' => 'required|min:2|max:32',
            'email' => ['required','email','max:255','min:5',
                Rule::unique('usuarios', 'email')
            ]
        ], [

            'nuevo.required' => 'La contraseña es obligatoria',
            'nuevo.min' => 'La contraseña debe tener como mínimo 6 caracteres',
            'nuevo.confirmed' => 'La confirmación de la contraseña no es correcta',
            'nuevo_confirmation.required' => 'La confirmación de la contraseña es obligatoria',
            'email.unique' => 'El email introducido ya está siendo usado',
            'email.required' => 'El email es obligatorio',
            'email.min' => 'El email debe tener como minímo 5 caracteres',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener como minímo 3 caracteres',
            'apellido.required' => 'El apellido es obligatorio',
            'apellido.min' => 'El apellido debe tener como minímo 2 caracteres',

        ]);

        if ($validator->fails()) {
            
            return redirect()->back()->withErrors($validator, 'datos')->withInput();

        }

        //Creamos usuario
        $usuario = new Usuario();

        $usuario->nombre = $request->nombre;
        $usuario->apellidos = $request->apellido;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->nuevo);
        $usuario->rol = 2;

        $usuario->save();

        //Creamos configuracion

        $configuracion = new configuracion();

        $configuracion->usuario_id = $usuario->id;
        $configuracion->dificultad_id = $request->dificultad;
        $configuracion->idioma_id = $request->idioma;

        $configuracion->save();


        //asigamos recursos a el usuario en base a dificultad
        $recursos = recurso::select('recursos.id')
        ->join('dificultad_recursos','recursos.id','=','dificultad_recursos.recurso_id')
        ->where('dificultad_recursos.dificultad_id','=',$request->dificultad)
        ->where('idioma_id','=',$request->idioma)
        ->get();


        foreach($recursos as $recurso){
            $estudia = new estudia();
            $estudia->recurso_id = $recurso->id;
            $estudia->usuario_id = $usuario->id;
            $estudia->nivel = 1;
            $estudia->fecha_ultima_repeticion = Carbon::now();
            $estudia->save();

        }

        

        return view('business.registerSuccess');
    }

}
