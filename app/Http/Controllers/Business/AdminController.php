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
use App\Models\Usuario;
use App\Models\configuracion;


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
        Log::info('crear palabra');

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

    public function editarPalabra(Request $request,$id){
        Log::info('editar palabra');

        $recurso = recurso::where('texto','=',$request->recurso)->where('idioma_id','=',$request->idioma)->where('id','!=',$id)->get()->first();
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

            $recurso = recurso::where('texto','=',$request->recurso)->where('idioma_id','=',$request->idioma)->where('vocabulario_id','=',$vocabulario->id)->where('id','!=',$id)->get()->first();
            if($recurso){
                return back()->with(['errorMessage' => 'Ya existe dicho recurso con estos datos.']);
            }else{

                $rec = recurso::where('id','=',$id)->get()->first();

                $rec->tipo_recurso = 'Palabra';
                $rec->texto = $request->recurso;
                $rec->orden = 1;
                $rec->vocabulario_id = $vocabulario->id;
                $rec->idioma_id = $request->idioma;
                $rec->save();

            }

        }


        $recursos = recurso::paginate(10);
        $idioma = idioma::all();
        $dificultad = dificultad::all();
        
        return back()->with(['success' => 'Palabra editada correctamente']);
    }

    public function eliminarPalabra(Request $request){
        Log::info('eliminar');

        $ids = $request->ids;

        if(is_null($ids)){
                
            return 'ok';
        }else{
            if($ids == -1){
                $recursos = recurso::all();
                foreach($recursos as $r){
                    $recuro = recurso::where('id','=',$r->id)->get()->first();
                    $recurso->delete();
                    $dificultadRecursos = dificultadRecurso::where('recurso_id','=',$r->id)->get();
                    foreach($dificultadRecursos as $dif){
                        $dif->delete();
                    }
                }
            }else{
                foreach($ids as $id){
                    if(!is_null($id)){
                        foreach($id as $i){
                            $recurso = recurso::where('id','=',$i)->get()->first();
                            $recurso->delete();
                            $dificultadRecursos = dificultadRecurso::where('recurso_id','=',$i)->get();
                            foreach($dificultadRecursos as $dif){
                                $dif->delete();
                            }
                        }
                    }
                    
                }
            }
        }
        return back()->with(['success' => 'Palabras eliminada correctamente']);
    }

    public function getUsuarios(){
        $usuarios = Usuario::paginate(10);
        $idioma = idioma::all();
        $dificultad = dificultad::all();

        return view('business.home.envios.adminUsuario',['usuarios' => $usuarios,'idiomas' => $idioma,'dificultades' => $dificultad,'errorMessage' => '']);
    }

    public function editConfiguracion(Request $request,$id){

        $configuracion = configuracion::where('id','=',$id)->get()->first();
        $configuracion->idioma_id = $request->idioma;
        $configuracion->dificultad_id = $request->dificultad;
        $configuracion->save();

        return back()->with(['success' => 'Configuracion modificada correctamente']);
    }

    public function deleteConfiguracion(Request $request){
        $ids = $request->ids;

        if(is_null($ids)){
                
            return 'ok';
        }else{
            if($ids == -1){
                $configuraciones = configuracion::all();
                foreach($configuraciones as $c){
                    $configuracion = configuracion::where('id','=',$c->id)->get()->first();
                    $configuracion->delete();
                   
                }
            }else{
                foreach($ids as $id){
                    if(!is_null($id)){
                        foreach($id as $i){
                            $configuracion = configuracion::where('id','=',$i)->get()->first();
                            $configuracion->delete();
                           
                        }
                    }
                    
                }
            }
        }
        return back()->with(['success' => 'Configuracion eliminada correctamente']);
    }

    public function buscarUsuario(Request $request){
        $text = $request->t;

        Log::info('buscar',array($text));
       
        
        if(is_null($text)){
            $usuarios = Usuario::paginate(10);
        }else{
            $usuarios = Usuario::Where([['nombre', 'like', '%' . $text . '%']])
            ->orWhere([['email', 'like', '%' . $text . '%']])
            ->paginate(10);
        }
      
        
        return view('business.home.envios.tableUsuarios',['usuarios' => $usuarios]);
    }
}
