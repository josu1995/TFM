<?php

namespace App\Console\Commands;

use App\Models\EstadosPago;
use App\Models\Factura;
use App\Models\FacturaStore;
use App\Models\Pedido;
use App\Models\Punto;
use App\Models\Viaje;
use App\Models\EstadoViaje;
use App\Services\FacturaService;
use App\Models\FacturaTransportista;
use App\Repositories\OpcionRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use ZipArchive;
use App;
use View;
use Storage;
use Mail;

class EmailFacturas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:facturas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envío de email con facturas de envíos y stores del mes';

    protected $facturaService;
    protected $opcionRepository;

    /**
     * Create a new command instance.
     * @param FacturaService $facturaService
     *
     * @return void
     */
    public function __construct(FacturaService $facturaService, OpcionRepository $opcionRepository)
    {
        parent::__construct();
        $this->facturaService = $facturaService;
        $this->opcionRepository = $opcionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        setlocale(LC_TIME, 'es_ES.utf8');

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $year = $yesterday->year;
        $month = $yesterday->month;

        /** FACTURAS PEDIDOS **/

        $pedidosMes = Pedido::where([['estado_pago_id', '>=', EstadosPago::PAGADO], [DB::raw('MONTH(created_at)'), $month], [DB::raw('YEAR(created_at)'), $year]])->get();
        if(count($pedidosMes)) {
            $zipPedidos = new ZipArchive();
            $zipName = storage_path() . '/app/docs/facturas/pedidos/Facturas-' . Carbon::now()->toDateString() . '.zip';
            Storage::makeDirectory('docs/facturas/pedidos');
            if ($zipPedidos->open($zipName, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
                exit("cannot open <$zipName>\n");
            }
            // Recolectamos pedidos del mes
            foreach ($pedidosMes as $pedido) {
                if ($pedido->envios()->count() > 0 && $pedido->embalajes + $pedido->coberturas + $pedido->gestion) {
                    $pdf = App::make('dompdf.wrapper');

                    // Comprobamos la numeracion de la factura
                    $storedFactura = Factura::where('pedido_id', $pedido->id)->first();
                    $numeracion = null;
                    if (!$storedFactura) {
                        $storedFactura = $this->facturaService->generarFactura($pedido);
                    }

                    // Generamos factura
                    $usuario = $pedido->usuario;
                    $factura = $this->facturaService->getFacturaData($pedido, $storedFactura);
                    $coberturas = $factura['coberturas'];
                    $gestion = $factura['gestion'];
                    $embalajes = $factura['embalajes'];
                    $subtotal = $factura['subtotal'];
                    $ivaTotal = $factura['ivaTotal'];
                    $descuento = $factura['descuento'];


                    $numeracion = $storedFactura->num_factura;

                    $iva = $storedFactura->impuestos;

                    $view = View::make('admin.files.factura', compact('usuario', 'pedido', 'today', 'coberturas', 'gestion', 'embalajes', 'subtotal', 'ivaTotal', 'descuento', 'numeracion', 'iva'))->render();
                    $pdf->loadHTML($view);
                    Storage::disk('local')->put('docs/facturas/pedidos/pdf/Factura-' . $numeracion . '.pdf', $pdf->output());
                    $zipPedidos->addFile(storage_path() . '/app/docs/facturas/pedidos/pdf/Factura-' . $numeracion . '.pdf', 'Factura-' . $numeracion . '.pdf');
                }
            }

            $zipPedidos->close();
            Storage::disk('local')->deleteDirectory('docs/facturas/pedidos/pdf');


            Mail::send('admin.emails.templates.facturas', [], function ($m) {
                $m->attach(storage_path() . '/app/docs/facturas/pedidos/Facturas-' . Carbon::now()->toDateString() . '.zip');
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to(env('MAIL_FACTURACION'), 'Facturas')->subject('Facturas Emitidas ' . ucfirst(Carbon::yesterday()->formatLocalized('%B')) . ' ' . Carbon::yesterday()->formatLocalized('%Y'));
            });

            Storage::disk('local')->deleteDirectory('docs/facturas/pedidos');
        }

        /** FACTURAS STORES **/

        $zipStores = new ZipArchive();
        $zipStoresName = storage_path().'/app/docs/facturas/stores/Facturas-Stores-'.Carbon::now()->toDateString().'.zip';
        Storage::makeDirectory('docs/facturas/stores');
        if ($zipStores->open($zipStoresName, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$zipStoresName>\n");
        }
        // Recolectamos pedidos del mes
        $stores = Punto::all();
        $fechaFacturacion = Carbon::createFromDate($today->year,$today->month,1)->addMonth();
        $iva = $this->opcionRepository->getImpuestos();
        $irpf = $this->opcionRepository->getRetencionIRPF();
        $precioComision = $this->opcionRepository->getComisionPunto();
        $tarifaMinima = 1;
        $mes = ucfirst(Carbon::yesterday()->formatLocalized('%B'));
        foreach ($stores as $store) {
            $unidades = $store->comisiones()->where([[DB::raw('MONTH(created_at)'), $month],[DB::raw('YEAR(created_at)'), $year]])->count();
            $pdf = App::make('dompdf.wrapper');
            if($unidades > 0 && $unidades * $precioComision >= 1) {

                // Comprobamos la numeracion de la factura
                $storedFactura = FacturaStore::where([['store_id', $store->id], [DB::raw('MONTH(created_at)'), $fechaFacturacion->month]])->first();
                $numeracion = null;
                if (!$storedFactura) {
                    $storedFactura = $this->facturaService->generarFacturaStore($store->id, $fechaFacturacion);
                    $iva = $storedFactura->impuestos;
                    $irpf = $storedFactura->irpf;
                    $precioComision = $storedFactura->comision;
                }

                $numeracion = $storedFactura->num_factura;


                $view = View::make('admin.files.facturaStores', compact( 'store', 'fechaFacturacion', 'numeracion', 'iva', 'irpf', 'unidades', 'mes', 'precioComision' ))->render();
                $pdf->loadHTML($view);
                Storage::disk('local')->put('docs/facturas/stores/pdf/Factura-Store-' . $store->nombre . '-' . $numeracion . '.pdf', $pdf->output());
                $zipStores->addFile(storage_path() . '/app/docs/facturas/stores/pdf/Factura-Store-' . $store->nombre . '-' . $numeracion . '.pdf', 'Factura-Store-' . $store->nombre . '-' . $numeracion . '.pdf');
            } else {
                // Se genera factura con tarifa mínima de 1 euro
                // Comprobamos la numeracion de la factura
                $storedFactura = FacturaStore::where([['store_id', $store->id], [DB::raw('MONTH(created_at)'), $fechaFacturacion->month]])->first();
                $numeracion = null;
                if (!$storedFactura) {
                    $storedFactura = $this->facturaService->generarFacturaStore($store->id, $fechaFacturacion);
                    $iva = $storedFactura->impuestos;
                    $irpf = $storedFactura->irpf;
                    $precioComision = $storedFactura->comision;
                }

                $numeracion = $storedFactura->num_factura;

                $view = View::make('admin.files.facturaStores', compact( 'store', 'fechaFacturacion', 'numeracion', 'iva', 'irpf', 'tarifaMinima', 'mes', 'precioComision' ))->render();
                $pdf->loadHTML($view);
                Storage::disk('local')->put('docs/facturas/stores/pdf/Factura-Store-' . $store->nombre . '-' . $numeracion . '.pdf', $pdf->output());
                $zipStores->addFile(storage_path() . '/app/docs/facturas/stores/pdf/Factura-Store-' . $store->nombre . '-' . $numeracion . '.pdf', 'Factura-Store-' . $store->nombre . '-' . $numeracion . '.pdf');
            }
        }

        $zipStores->close();
        Storage::disk('local')->deleteDirectory('docs/facturas/stores/pdf');

        if(file_exists(storage_path().'/app/docs/facturas/stores/Facturas-Stores-'.Carbon::now()->toDateString().'.zip')) {
            Mail::send('admin.emails.templates.facturas', [], function ($m) {
                $m->attach(storage_path() . '/app/docs/facturas/stores/Facturas-Stores-' . Carbon::now()->toDateString() . '.zip');
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to(env('MAIL_FACTURACION'), 'Facturas')->subject('Facturas Stores ' . ucfirst(Carbon::yesterday()->formatLocalized('%B')) . ' ' . Carbon::yesterday()->formatLocalized('%Y'));
            });
        }

        Storage::disk('local')->deleteDirectory('docs/facturas/stores');


        /** FACTURAS TRANSPORTERS **/
        $viajes = Viaje::where([['estado_id', EstadoViaje::FINALIZADO], [DB::raw('MONTH(fecha_finalizacion)'), $month],[DB::raw('YEAR(fecha_finalizacion)'), $year]])->get();
        if(count($viajes)) {
            $zipTransporters = new ZipArchive();
            $zipTransportersName = storage_path() . '/app/docs/facturas/transporters/Facturas-Transportistas-' . Carbon::now()->toDateString() . '.zip';
            Storage::makeDirectory('docs/facturas/transporters');
            if ($zipTransporters->open($zipTransportersName, ZipArchive::CREATE) !== TRUE) {
                exit("cannot open <$zipTransportersName>\n");
            }
            // Recolectamos viajes del mes
            foreach ($viajes as $viaje) {
                $unidades = $viaje->envios()->count();
                if ($unidades > 0 && $viaje->gestion + $viaje->impuestos) {
                    $pdf = App::make('dompdf.wrapper');

                    // Comprobamos la numeracion de la factura
                    $storedFactura = FacturaTransportista::where('viaje_id', $viaje->id)->first();
                    $numeracion = null;
                    if (!$storedFactura) {
                        $storedFactura = $this->facturaService->generarFacturaTransportista($viaje);
                    }

                    $numeracion = $storedFactura->num_factura;

                    $view = View::make('admin.files.facturaTransportista', compact('viaje', 'today', 'numeracion', 'unidades'))->render();
                    $pdf->loadHTML($view);
                    Storage::disk('local')->put('docs/facturas/transporters/pdf/Factura-Transportista-' . $viaje->codigo . '-' . $numeracion . '.pdf', $pdf->output());
                    $zipTransporters->addFile(storage_path() . '/app/docs/facturas/transporters/pdf/Factura-Transportista-' . $viaje->codigo . '-' . $numeracion . '.pdf', 'Factura-Transportista-' . $viaje->codigo . '-' . $numeracion . '.pdf');
                }
            }

            $zipTransporters->close();
            Storage::disk('local')->deleteDirectory('docs/facturas/transporters/pdf');


            Mail::send('admin.emails.templates.facturas', [], function ($m) {
                $m->attach(storage_path() . '/app/docs/facturas/transporters/Facturas-Transportistas-' . Carbon::now()->toDateString() . '.zip');
                $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
                $m->to(env('MAIL_FACTURACION'), 'Facturas')->subject('Facturas Transportistas ' . ucfirst(Carbon::yesterday()->formatLocalized('%B')) . ' ' . Carbon::yesterday()->formatLocalized('%Y'));
            });

            Storage::disk('local')->deleteDirectory('docs/facturas/transporters');
        }
    }
}
