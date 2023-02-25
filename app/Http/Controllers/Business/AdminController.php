<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\recurso;


class AdminController extends Controller
{
    public function getPalabras(){
        
        $recursos = recurso::paginate(10);
        
        return view('business.home.envios.pendientesPago',['recursos' => $recursos]);
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
}
