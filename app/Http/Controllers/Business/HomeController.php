<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Usuario;
use App\Models\configuracion;
use App\Models\dificultad;
use App\Models\idioma;
use App\Models\recurso;
use App\Models\estudia;

use Auth;
use Validator;
use Hash;

class HomeController extends Controller
{

    private $businessRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(BusinessRepository $businessRepository) {
        $this->middleware('auth.business');
        $this->businessRepository = $businessRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

     public function getDatosUsuario(Request $request) {

        Log::info('veamos los datos',array(Auth::user()->rol));

        return view('business.home.envios.datosUsuario', ['usuario' => Auth::user(), 'message' => $request->session()->get('message')]);
    }

    public function postPerfil(Request $request){
        Log::info('editar datos');
        $usuario = Auth::user();

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|min:3|max:32',
            'apellido' => 'required|min:2|max:32',
            'email' => ['required','email','max:255','min:5',
                Rule::unique('usuarios', 'email')->ignore($usuario->id)
            ]
            
        ], [
            'email.unique' => 'El email introducido ya está siendo usado',
            'email.required' => 'El email es obligatorio',
            'email.min' => 'El email debe tener como minímo 5 caracteres',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener como minímo 3 caracteres',
            'apellido.required' => 'El apellido es obligatorio',
            'apellido.min' => 'El apellido debe tener como minímo 2 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'datos')
                ->withInput();
        }


        $usuario->nombre = $request->nombre;
        $usuario->apellidos = $request->apellido;
        $usuario->email = $request->email;
        $usuario->save();

        $request->session()->flash('message', 'Tus datos han sido actualizados.');
        return redirect()->route('usuario_get_datos');
    }

    public function postContraseña(Request $request){
        Log::info('cambiar pass');

        $usuario = Auth::user();

        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'nuevo' => 'required|min:6|confirmed',
            'nuevo_confirmation' => 'required',
        ], [
            'password.required' => 'La contraseña actual es obligatoria',
            'nuevo.required' => 'La nueva contraseña es obligatoria',
            'nuevo.min' => 'La nueva contraseña debe tener como mínimo 6 caracteres',
            'nuevo.confirmed' => 'La confirmación de la nueva contraseña no es correcta',
            'nuevo_confirmation.required' => 'La confirmación de la nueva contraseña es obligatoria',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'password')
                ->withInput();
        }

        $datos_password = $request->only('password', 'nuevo');
        $return = $this->actualizarPassword($usuario, $datos_password);
        

        if(!$return) {
            return redirect()->back()->withErrors(['password' => 'La contraseña actual no es correcta'], 'password');
        } else {
            $request->session()->flash('message', 'Tu contraseña ha sido actualizada.');
            return redirect()->route('usuario_get_datos');
        }
    }


    public function actualizarPassword(Usuario $usuario, $datos)
    {
        // Contraseña actual coincide con la enviada
        if(Hash::check($datos['password'], $usuario->password)) {
            $usuario->password = Hash::make($datos['nuevo']);
            $usuario->save();
            return true;
        } else {
            return false;
        }
    }

    public function getEstudios(){

        Log::info('por aqui no?');

        $usuario = Auth::user();

        $configuraciones = configuracion::where('usuario_id','=',$usuario->id)->get();
        $dificultades = dificultad::all();
        $idiomas = idioma::all();

        return view('business.home.envios.estudios', ["configuraciones" => $configuraciones,'dificultades' => $dificultades, 'idiomas' => $idiomas]);

    }


    public function crearNuevaConfiguracion(Request $request){
        Log::info('crear configuracion');
        $usuario = Auth::user();
        $idioma = $request->idioma;
        $dificultad = $request->dificultad;

        $configuracion = configuracion::where('usuario_id','=',$usuario->id)
        ->where('idioma_id','=',$idioma)
        ->where('dificultad_id','=',$dificultad)
        ->get()->first();

        if($configuracion){
            return redirect()->back()->withErrors(['configuracion' => 'Ya tienes unos estudios con estas opciones.'], 'configuracion');
        }else{
            $newConfiguracion = new configuracion();
            $newConfiguracion->usuario_id = $usuario->id;
            $newConfiguracion->idioma_id = $idioma;
            $newConfiguracion->dificultad_id = $dificultad;
            $newConfiguracion->save();
        }   

        $request->session()->flash('message', 'Nueva configuración creada correctamente.');
        return redirect()->route('usuario_get_estudios');
        
    }

    public function jugar(Request $request){
        //hay que mandar un recurso que es el que se va a adivinar
        //¿como se dice te? --> mandamos el recurso en español Te
    }

    public function comprobar(Request $request){
        //Recivimos un id, que es el del recurso a adivinar
        //Recivimos una respuesta
        //EJEMPLO
        //---------
        //¿como se dice te?
        //Te
        $usuario = Auth::user();
        $respuesta = $request->repuesta;
        $idioma = $request->idioma;

        $recurso = recurso::where('id','=',$request->id)->get()->first();

        if($recurso->tipo = 'Palabra'){
            //Comprobamos las palabras
            $adivinar = recurso::where('idioma_id','=',$idioma)
            ->where('vocabulario_id','=',$recurso->vocabulario_id)
            ->get()->first();

            $estudio = estudia::where('usuario_id','=',$usuario->id)
            ->where('recurso_id','=',$adivinar->id)
            ->get()->first();

            if($adivinar == $respuesta){
                if($estudio->nivel < 10 ){
                    $estudio->nivel = $estudio->nivel + 1;
                   
                }
            }else{
                if($estudio->nivel > 1){
                    $estudio->nivel = $estudio->nivel - 1;
                }
            }

            $estudio->updated_at = Carbon::now();
            $estudio->save();

        }

        return 'ok';
    }



    public function getBadges() {
        return response()->json([
            'ok'
        ]);
    }

}
