<?php

namespace App\Console\Commands;

use App\Models\Envio;
use App\Models\EnvioBusiness;
use App\Models\Estado;
use App\Services\MailService;
use \App\Services\Business\MailService as BusinessMailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

/**
 * Class AvisoDestinatario
 * @package App\Console\Commands
 */
class AvisoDestinatario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destinatario:aviso';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aviso a destinatario 7 días después de que su paquete se encuentre en punto de destino';

    /**
     * @var MailService
     */
    protected $mailService;
    /**
     * @var BusinessMailService
     */
    protected $businessMailService;

    /**
     * Create a new command instance.
     *
     * @param MailService $mailService
     * @param BusinessMailService $businessMailService
     * @return void
     */
    public function __construct(MailService $mailService, BusinessMailService $businessMailService)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->businessMailService = $businessMailService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $aWeekAgo = Carbon::today()->subDays(7);
        $envios7Dias = Envio::where([
            ['estado_id', Estado::RECOGIDA],
            [DB::raw('DATE(fecha_destino)'), '=', $aWeekAgo]
        ])->get();

        foreach ($envios7Dias as $envio) {
            $this->mailService->avisoDestinatario($envio);
        }

        $enviosBusiness7Dias = EnvioBusiness::where([
            ['estado_id', Estado::RECOGIDA],
            [DB::raw('DATE(fecha_destino)'), '=', $aWeekAgo]
        ])->get();

        foreach ($enviosBusiness7Dias as $envio) {
            $this->businessMailService->avisoDestinatario($envio);
        }
    }
}
