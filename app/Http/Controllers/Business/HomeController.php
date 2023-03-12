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
use App\Models\vocabulario;

use Auth;
use Validator;
use Hash;
use Carbon;

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

            $recursos = recurso::join('dificultad_recursos','recursos.id','=','dificultad_recursos.recurso_id')
            ->where('idioma_id','=',$idioma)
            ->where('dificultad_id','=',$dificultad)
            ->get();

            foreach($recursos as $recurso){
                $estudia = new estudia();
                $estudia->usuario_id = $usuario->id;
                $estudia->recurso_id = $recurso->recurso_id;
                $estudia->nivel = 1;
                $estudia->fecha_ultima_repeticion = Carbon::now()->subDays(30);
                $estudia->save();

            }
            
        }   

        $request->session()->flash('message', 'Nueva configuración creada correctamente.');
        return redirect()->route('usuario_get_estudios');
        
    }

    public function jugar(Request $request,$id){
        Log::info('jugar');
        $usuario = Auth::user();
        $configuracion = configuracion::where('id','=',$id)->get()->first();

        $juego = [];

        $recurso = recurso::select('recursos.*')
        ->join('dificultad_recursos','recursos.id','=','dificultad_recursos.recurso_id')
        ->join('estudias','recursos.id','=','estudias.recurso_id')
        ->where('dificultad_recursos.dificultad_id','=',$configuracion->dificultad_id)
        ->where('recursos.idioma_id','=',$configuracion->idioma_id)
        ->where('estudias.usuario_id','=',$usuario->id)
        ->where('fecha_ultima_repeticion','!=',Carbon::now()->format('Y-m-d'))
        ->orderBy('fecha_ultima_repeticion','ASC')
        ->orderBy('orden','ASC')
        ->get()->first();
        Log::info('recurso',array($recurso));
        if($recurso){

        
            $traduccion = $recurso->vocabulario->nombre;
            array_push($juego,$recurso->id);
            
            $vocabularios = vocabulario::where('familia_id','=',$recurso->vocabulario->familia_id)
            ->where('id','!=',$recurso->vocabulario->id)
            ->inRandomOrder()->limit(2)->get();

            $vocs = [];
            foreach($vocabularios as $v){
                array_push($vocs,$v->id);
            }

            $recursosFalsos = recurso::whereIn('vocabulario_id',$vocs)
            ->where('idioma_id','=',$configuracion->idioma_id)
            ->get();

            foreach($recursosFalsos as $r){
                array_push($juego,$r->id);
            }

            $recursosJuego = recurso::whereIn('id',$juego)->inRandomOrder()->get();
        }else{
            $recursosJuego = recurso::where('id','=',0)->get();
        }
        

        return view('business.home.envios.juego', ['traduccion' => $recurso,'recursos' => $recursosJuego]);
        
    }

    public function comprobar(Request $request){
        //Recivimos un id, que es el del recurso a adivinar
        //Recivimos una respuesta
        //EJEMPLO
        //---------
        //¿como se dice te?
        //Te
        $usuario = Auth::user();
        $correcto = $request->correcto;
        $respuesta = $request->recurso;
        $resultado = 'false';
        $recurso = recurso::where('id','=',$correcto)->get()->first();
        if($recurso->tipo_recurso = 'Palabra'){
            //Comprobamos las palabras
            $estudio = estudia::where('usuario_id','=',$usuario->id)
            ->where('recurso_id','=',$correcto)
            ->get()->first();

            if($correcto == $respuesta){
                if($estudio->nivel < 10 ){
                    $estudio->nivel = $estudio->nivel + 1;
                }
                $resultado = 'true';
            }else{
                if($estudio->nivel > 1){
                    $estudio->nivel = $estudio->nivel - 1;
                }
                $resultado = 'false';
            }

            $estudio->fecha_ultima_repeticion = Carbon::now();
            $estudio->save();

        }

        return $resultado;
    }



    public function getBadges() {
        return response()->json([
            'ok'
        ]);
    }

}
