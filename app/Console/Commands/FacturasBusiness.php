<?php

namespace App\Console\Commands;

use App\Models\ConfiguracionBusiness;
use App\Models\Estado;
use App\Models\TipoDevolucionBusiness;
use App\Services\Business\MailService;
use App\Repositories\OpcionRepository;
use App\Services\MondialRelayService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Business\FacturaService;

/**
 * Class FacturasBusiness
 * @package App\Console\Commands
 */
class FacturasBusiness extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturas:business';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar facturas para business.';

    /**
     * @var MondialRelayService
     */
    protected $mondialRelayService;
    /**
     * @var OpcionRepository
     */
    protected $opcionRepository;
    /**
     * @var MailService
     */
    protected $mailService;
    /**
     * @var FacturaService
     */
    protected $facturaService;

    /**
     * FacturasBusiness constructor.
     * @param MondialRelayService $mondialRelayService
     * @param OpcionRepository $opcionRepository
     * @param MailService $mailService
     * @param FacturaService $facturaService
     */
    public function __construct(
        MondialRelayService $mondialRelayService,
        OpcionRepository $opcionRepository,
        MailService $mailService,
        FacturaService $facturaService
    ) {
        parent::__construct();
        $this->mondialRelayService = $mondialRelayService;
        $this->opcionRepository = $opcionRepository;
        $this->mailService = $mailService;
        $this->facturaService = $facturaService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday();

        foreach (ConfiguracionBusiness::all() as $ecommerce) {
            $envios = $ecommerce->envios()->where(function (Builder $query) use ($yesterday) {
                $query->where(function (Builder $query) use ($yesterday) {
                    $query->where([
                        ['estado_id', '>', Estado::VALIDADO],
                        [DB::raw('MONTH(fecha_pago)'), $yesterday->month],
                        [DB::raw('YEAR(fecha_pago)'), $yesterday->year]
                    ])->whereDoesntHave('devolucionAsDevolucion');
                });
                $query->orWhere(function (Builder $query) use ($yesterday) {
                    $query->where([
                        ['estado_id', '>', Estado::VALIDADO],
                        [DB::raw('MONTH(fecha_origen)'), $yesterday->month],
                        [DB::raw('YEAR(fecha_origen)'), $yesterday->year]
                    ]);
                    $query->whereHas('devolucionAsDevolucion', function (Builder $query) {
                        $query->where('tipo_devolucion_id', TipoDevolucionBusiness::DEVOLUCION);
                        $query->where('finalizado', 1);
                        $query->whereDoesntHave('envio', function (Builder $query) {
                            $query->where('coste_cliente_devolucion', 1);
                        });
                    });
                });
                $query->orWhere(function (Builder $query) use ($yesterday) {
                    $query->where([
                        [DB::raw('MONTH(fecha_pago)'), $yesterday->month],
                        [DB::raw('YEAR(fecha_pago)'), $yesterday->year]
                    ]);
                    $query->whereHas('devolucionAsDevolucion', function (Builder $query) {
                        $query->where('tipo_devolucion_id', TipoDevolucionBusiness::RETORNO);
                        $query->where('finalizado', 1);
                    });
                });
                $query->orWhere(function (Builder $query) use ($yesterday) {
                    $query->where([
                        ['estado_id', Estado::CANCELADO],
                        [DB::raw('MONTH(fecha_finalizacion)'), $yesterday->month],
                        [DB::raw('YEAR(fecha_finalizacion)'), $yesterday->year]
                    ]);
                });
                $query->orWhereHas('reembolso', function (Builder $query) use ($yesterday) {
                    $query->where([
                        [DB::raw('MONTH(created_at)'), $yesterday->month],
                        [DB::raw('YEAR(created_at)'), $yesterday->year]
                    ]);
                });
            })->get();
            if (count($envios)) {
                $this->facturaService->createFacturaBusiness($ecommerce, $yesterday, $yesterday, $envios);
            }
        }
    }
}
