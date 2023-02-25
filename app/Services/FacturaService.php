<?php

namespace App\Services;

// Modelos
use App\Models\Factura;
use App\Models\FacturaStore;
use App\Models\FacturaTransportista;
use App\Models\Pedido;
use App\Repositories\OpcionRepository;
use DB;

class FacturaService
{
    protected $opciones;
    protected $calcularPrecio;
    protected $opcionRepository;

    public function __construct(OpcionRepository $opciones, CalcularPrecio $calcularPrecio, OpcionRepository $opcionRepository)
    {
        $this->opciones = $opciones;
        $this->calcularPrecio = $calcularPrecio;
        $this->opcionRepository = $opcionRepository;
    }

    public function getFacturaData(Pedido $pedido, Factura $factura) {

        $coberturas = array();
        $gastosGestion = array();
        $embalajes = array();
        $subtotal = 0;
        $ivaTotal = 0;
        $descuento = 0;

        foreach ($pedido->envios as $envio) {

            // Cogemos cobertura del envio
            if($envio->cobertura && $envio->cobertura->valor != 0) {
                if(array_key_exists($envio->cobertura->id, $coberturas)) {
                    // Si existe incrementamos el contador
                    $coberturas[$envio->cobertura->id]['unidades']++;
                    $subtotal += $envio->cobertura->valor;
                } else {
                    $coberturas[$envio->cobertura->id] = ['nombre' => $envio->cobertura->descripcion_factura, 'unidades' => 1, 'precio' => number_format($envio->cobertura->valor, 2)];
                    $subtotal += $envio->cobertura->valor;
                }
            }

            if(empty($gastosGestion)) {
                $gastosGestion['nombre'] = 'Gastos de gestión';
                $gastosGestion['unidades'] = 1;
                $precioGestion = number_format($envio->precio * $factura->comision / 100, 2);
                $gastosGestion['precio'] = $precioGestion;
                $subtotal += $precioGestion;
                $ivaTotal += $this->calcularPrecio->calcularIvaFromPrecio($precioGestion, $factura->impuestos);
            } else {
                $gastosGestion['unidades']++;
                $precioGestion = number_format($envio->precio * $factura->comision / 100, 2);
                $gastosGestion['precio'] += $precioGestion;
                $subtotal += $precioGestion;
                $ivaTotal += $this->calcularPrecio->calcularIvaFromPrecio($precioGestion, $factura->impuestos);
            }

            if($envio->embalaje && $envio->embalaje->precio != 0) {
                if(array_key_exists($envio->embalaje->id, $embalajes)) {
                    // Si existe incrementamos el contador
                    $embalajes[$envio->embalaje->id]['unidades']++;
                    $subtotal += $envio->embalaje->precio*100/121;
                    $ivaTotal += $envio->embalaje->precio*21/121;
                } else {
                    $embalajes[$envio->embalaje->id] = ['nombre' => $envio->embalaje->descripcion_factura, 'unidades' => 1, 'precio' => $envio->embalaje->precio];
                    $subtotal += $envio->embalaje->precio*100/121;
                    $ivaTotal += $envio->embalaje->precio*21/121;
                }
            }

        }

        // Miramos si hay que aplicar descuento
        if($pedido->descuento != 0) {
            $envioDescuento = $this->getEnvioDescuento($pedido->envios);
            if($envioDescuento) {
                $gestionDescuento = number_format($envioDescuento->precio * $factura->comision / 100, 2);
                $precioEnvio = number_format($envioDescuento->precio + $gestionDescuento + $this->calcularPrecio->calcularIvaFromPrecio($gestionDescuento, $factura->impuestos) + $envioDescuento->cobertura->valor + $envioDescuento->embalaje->precio, 2);
                if ($precioEnvio == $pedido->descuento) {
                    $descuento = number_format($precioEnvio - $envioDescuento->precio, 2);
                } else if($precioEnvio > $pedido->descuento) {
                    $descuento = number_format(abs($pedido->descuento - $envioDescuento->precio), 2);
                }
            }
        }

        return array('coberturas' => $coberturas, 'gestion' => $gastosGestion, 'embalajes' => $embalajes, 'subtotal' => $subtotal, 'ivaTotal' => $ivaTotal, 'descuento' => $descuento);

    }

    public function getEnvioDescuento($envios) {

        $seleccionado = null;
        foreach ($envios as $envio) {
            if($seleccionado == null) {
                $seleccionado = $envio;
            } elseif ($seleccionado->precio < $envio->precio) {
                $seleccionado = $envio;
            }
        }
        return $seleccionado;
    }

    public function generarFactura($pedido) {
        $newFactura = new Factura();
        $year = substr(\Carbon::parse($pedido->created_at)->year, 2);
        $numeracion = Factura::where('num_factura', 'like', $year . '-%')->count();
        $newFactura->num_factura = $year . '-' . ($numeracion + 1);
        $newFactura->pedido_id = $pedido->id;
        $newFactura->impuestos = $this->opcionRepository->getImpuestos();
        $newFactura->comision = $this->opcionRepository->getComisionPlataforma();
        $newFactura->created_at = $pedido->created_at;
        $newFactura->updated_at = $pedido->created_at;
        $newFactura->save();

        return $newFactura;
    }

    public function generarFacturaStore($storeId, $fecha) {
        $newFactura = new FacturaStore();
        $year = substr($fecha->year, 2);
        $numeracion = FacturaStore::where([['num_factura', 'like', $year . '-%'], ['store_id', $storeId]])->count();
        $newFactura->num_factura = $year . '-' . ($numeracion + 1);
        $newFactura->store_id = $storeId;
        $newFactura->impuestos = $this->opcionRepository->getImpuestos();
        $newFactura->irpf = $this->opcionRepository->getRetencionIRPF();
        $newFactura->comision = $this->opcionRepository->getComisionPunto();
        $newFactura->created_at = $fecha;
        $newFactura->updated_at = $fecha;
        $newFactura->save();

        return $newFactura;
    }

    public function generarFacturaTransportista($viaje) {
        $newFactura = new FacturaTransportista();
        $year = substr(\Carbon::parse($viaje->fecha_finalizacion)->year, 2);
        $numeracion = FacturaTransportista::where('num_factura', 'like', $year . '-%')->count();
        $newFactura->num_factura = $year . '-' . ($numeracion + 1);
        $newFactura->viaje_id = $viaje->id;
        $newFactura->created_at = $viaje->fecha_finalizacion;
        $newFactura->updated_at = $viaje->fecha_finalizacion;
        $newFactura->save();

        return $newFactura;
    }

}
