<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\recurso;
use App\Models\idioma;
use App\Models\dificultad;
use App\Models\vocabulario;
use App\Models\familiaRecurso;
use App\Models\dificultadRecurso;


class AdminController extends Controller
{
    public function getPalabras(){
        
        $recursos = recurso::paginate(10);
        $idioma = idioma::all();
        $dificultad = dificultad::all();
        
        return view('business.home.envios.pendientesPago',['recursos' => $recursos,'idiomas' => $idioma,'dificultades' => $dificultad,'errorMessage' => '']);
    }

    public function buscar(Request $request){
      

        $text = $request->t;

        Log::info('buscar',array($text));
       
        //$recursos = recurso::where([['texto', 'like', '%' . $text . '%']])->paginate(10);
        if(is_null($text)){
            $recursos = recurso::paginate(10);
        }else{
            $recursos = recurso::select('recursos.*')
            ->join('idiomas','recursos.idioma_id','=','idiomas.id')
            ->join('vocabularios','recursos.vocabulario_id','=','vocabularios.id')
            ->join('familia_recursos','vocabularios.familia_id','=','familia_recursos.id')
            ->where(function($query) use ($text){
                $query->where([['texto', 'like', '%' . $text . '%']])
                ->orWhere([['idiomas.nombre', 'like', '%' . $text . '%']])
                ->orWhere([['vocabularios.nombre', 'like', '%' . $text . '%']])
                ->orWhere([['familia_recursos.nombre', 'like', '%' . $text . '%']]);
            })->paginate(10);
        }
      
        
        return view('business.home.envios.tablePalabras',['recursos' => $recursos]);
    }

    public function crearPalabra(Request $request){
        Log::info('request');

        $recurso = recurso::where('texto','=',$request->recurso)->where('idioma_id','=',$request->idioma)->get()->first();
        if($recurso){
            return back()->with(['errorMessage' => 'Ya existe dicho recurso.']);
        }else{

            $familia = familiaRecurso::where('nombre','=',$request->familia)->get()->first();
            if($familia){

            }else{
                $familia = new familiaRecurso();
                $familia->nombre = $request->familia;
                $familia->save();
            }

            $vocabulario = vocabulario::where('nombre','=',$request->vocabulario)->where('familia_id','=',$familia->id)->get()->first();
            if($vocabulario){
                
            }else{

                $vocabulario = new vocabulario();
                $vocabulario->nombre =   $request->vocabulario;
                $vocabulario->familia_id = $familia->id;
                $vocabulario->save();

            }

            $recurso = recurso::where('texto','=',$request->recurso)->where('idioma_id','=',$request->idioma)->where('vocabulario_id','=',$vocabulario->id)->get()->first();
            if($recurso){
                return back()->with(['errorMessage' => 'Ya existe dicho recurso con estos datos.']);
            }else{
                $newRecurso = new recurso();

                $newRecurso->tipo_recurso = 'Palabra';
                $newRecurso->texto = $request->recurso;
                $newRecurso->orden = 1;
                $newRecurso->vocabulario_id = $vocabulario->id;
                $newRecurso->idioma_id = $request->idioma;
                $newRecurso->save();

                $dificultadRecurso = new dificultadRecurso();
                $dificultadRecurso->recurso_id = $newRecurso->id;
                $dificultadRecurso->dificultad_id = $request->dificultad;
                $dificultadRecurso->save();
            }

        }

        
        $recursos = recurso::paginate(10);
        $idioma = idioma::all();
        $dificultad = dificultad::all();
        
        return back()->with(['success' => 'Palabra creada correctamente']);


       
    }
}
