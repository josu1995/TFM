<?php

namespace App\Console\Commands;

use App\Models\Envio;
use App\Models\Estado;
use App\Models\TrackingResponseTrace;
use App\Services\MailService;
use App\Services\MondialRelayService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Log;

class TrazasEstadoTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracking:trace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprobar trazas estado tracking mondial relay.';

    protected $mondialRelayService;

    /**
     * Create a new command instance.
     *
     * @param MailService $mailService
     * @return void
     */
    public function __construct(MondialRelayService $mondialRelayService)
    {
        parent::__construct();
        $this->mondialRelayService = $mondialRelayService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
//        $numExps = ['96045752'];
//
//        foreach ($numExps as $numExp) {
//
//            $params = [
//                'Enseigne' => env('MONDIAL_RELAY_ID'),
//                'Expedition' => $numExp,
//                'Langue' => 'ES'
//            ];
//
//            $result = $this->mondialRelayService->getTracking($params);
//
//            Log::info(json_encode($result));
//
//            $trace = new TrackingResponseTrace();
//            $trace->num_exp = $numExp;
//            $trace->created_at = Carbon::now();
//            $trace->stat = $result['STAT'];
//            $trace->response = json_encode($result);
//            $trace->save();
//
//        }

//        $resp = '<WSI2_TracingColisDetailleResponse xmlns="http://www.mondialrelay.fr/webservice/">
//         <WSI2_TracingColisDetailleResult>
//            <STAT>82</STAT>
//            <Libelle01>Entrega a domicilio</Libelle01>
//            <Relais_Libelle/>
//            <Relais_Num/>
//            <Libelle02/>
//            <Tracing>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle>Recogido en agencia Punto Pack</Libelle>
//                  <Date>22/10/18</Date>
//                  <Heure>16:16</Heure>
//                  <Emplacement>ESPAGNE CORREOS EXPRESS</Emplacement>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle>En reparto</Libelle>
//                  <Date>23/10/18</Date>
//                  <Heure>07:24</Heure>
//                  <Emplacement>ESPAGNE CORREOS EXPRESS</Emplacement>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle>Liquidado en Ensena</Libelle>
//                  <Date>24/10/18</Date>
//                  <Heure>10:47</Heure>
//                  <Emplacement>ESPAGNE CORREOS EXPRESS</Emplacement>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//               <ret_WSI2_sub_TracingColisDetaille>
//                  <Libelle/>
//                  <Date/>
//                  <Heure/>
//                  <Emplacement/>
//                  <Relais_Num/>
//                  <Relais_Pays/>
//               </ret_WSI2_sub_TracingColisDetaille>
//            </Tracing>
//         </WSI2_TracingColisDetailleResult>
//      </WSI2_TracingColisDetailleResponse>';
////        $json = json_decode($resp);
//
//        $xml = simplexml_load_string($resp);
//
//        $json = json_encode($xml);

//        $numExp = '00013866';
//
//        $params = [
//            'Enseigne' => env('MONDIAL_RELAY_ID'),
//            'Expedition' => $numExp,
//            'Langue' => 'ES'
//        ];

//        $res = $this->mondialRelayService->getTracking($params);




    }
}
