<?php

namespace App\Services\Business;

// Utilidades
use App\Models\FacturaBusiness;
use App\Models\FacturaDevolucionBusiness;
use App\Repositories\OpcionRepository;
use App\Services\MondialRelayService;
use Mail;
use Auth;

class FacturaService
{

    protected $mondialRelayService;
    protected $opcionRepository;
    protected $mailService;

    /**
     * Create a new command instance.
     *
     * @param MailService $mailService
     * @return void
     */
    public function __construct(MondialRelayService $mondialRelayService, OpcionRepository $opcionRepository)
    {
        $this->mondialRelayService = $mondialRelayService;
        $this->opcionRepository = $opcionRepository;
    }

    public function createFacturaBusiness($ecommerce, $today, $lastMonth, $envios)
    {
        $factura = new FacturaBusiness();

        $factura->configuracion_business_id = $ecommerce->id;
        $factura->fecha = $today;
        $factura->num_factura = $this->generateNumFacturaBusiness($lastMonth->format('y'), $lastMonth->month);
        $factura->impuestos = $this->opcionRepository->getImpuestos();
        $precioTotal = 0;
        foreach ($envios as $envio) {
            $precioTotal += $envio->precio;
        }
        $factura->importe = $precioTotal;
        $factura->save();
    }


    private function generateNumFacturaBusiness($year, $month)
    {

        $lastFactura = FacturaBusiness::where('num_factura', 'like', '%-' . $year . '%')
            ->orderBy('id', 'desc')->first();

        if (!$lastFactura) {
            return 'TB-' . $year . '/1';
        } else {
            $newNum = intval(explode('/', $lastFactura->num_factura)[1]) + 1;
            return 'TB-' . $year . '/' . $newNum;
        }
    }

    public function createFacturaDevolucion($devolucion, $envioDevolucion)
    {

        $today = \Carbon::today();

        $factura = new FacturaDevolucionBusiness();

        $factura->devolucion_id = $devolucion->id;
        $factura->fecha = $today;
        $factura->num_factura = $this->generateNumFacturaDevolucion($today->format('y'), $today->month);
        $factura->impuestos = $this->opcionRepository->getImpuestos();
        $precioTotal = number_format($envioDevolucion->precio * 100 / (100 + $factura->impuestos), 2);
        $factura->importe = $precioTotal;
        $factura->save();
    }

    private function generateNumFacturaDevolucion($year, $month)
    {

        $lastFactura = FacturaDevolucionBusiness::where('num_factura', 'like', '%-' . $year . '%')->orderBy('num_factura', 'desc')->first();

        if (!$lastFactura) {
            return 'TD-' . $year . '/1';
        } else {
            $newNum = intval(explode('/', $lastFactura->num_factura)[1]) + 1;
            return 'TD-' . $year . '/' . $newNum;
        }
    }

}
