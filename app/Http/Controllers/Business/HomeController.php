<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Repositories\BusinessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Usuario;

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

        Log::info('veamos los datos',array(Auth::user()));

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










    public function getBadges() {
        return response()->json([
            'ok'
        ]);
    }

}
