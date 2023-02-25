<?php

namespace App\Services;

// Modelos
use App\Models\Envio;
use App\Models\Rango;
use App\Models\UsoCodigo;
use App\Repositories\OpcionRepository;

class CalcularPrecio
{
    protected $opciones;

    public function __construct(OpcionRepository $opciones)
    {
        $this->opciones = $opciones;
    }

    // Precio por defecto
    public function getDefault()
    {
        return floatval($this->opciones->getPrecioPorPeso());
    }

    /* Cálculo de precio de cada envío*/
    public function calcularPrecio(Envio $envio)
    {
        $paquete = $envio->paquete;
        $espacio = 0;
        $peso = 0;

        $espacio = $paquete->alto * $paquete->largo * $paquete->ancho;
        $peso = $paquete->peso;

        // Cálculo de precio por peso
        $precioPeso = Rango::where([
            ['minimo', '<=', $peso],
            ['maximo', '>=', $peso]
        ])->first();

        $precioPeso = $precioPeso ? $precioPeso->valor : floatval($this->opciones->getPrecioPorPeso());


        $peso = number_format(floatval($precioPeso),2);

        return $peso;
    }

    /* Cálculo de extra de cada envío*/
    public function calcularExtra(Envio $envio)
    {
        //Calculamos el porcentaje de comision
        $precio = $this->calcularPrecio($envio);
        $comision = $this->opciones->getComisionPlataforma();
        return number_format($precio * $comision / 100, 2);
    }

    /* Cálculo de IVA de cada envío*/
    public function calcularIVA(Envio $envio)
    {
//        $precio = $this->calcularPrecio($envio);
        $precio = $this->calcularExtra($envio);

        $total = ($precio * $this->opciones->getImpuestos()/100);
        return number_format($total, 2);
    }

    /* Cálculo de total de envío individual */
    public function calcularTotal(Envio $envio)
    {
        $precio = $this->calcularPrecio($envio);
        $extra = $this->calcularExtra($envio);
        $IVA = $this->calcularIVA($envio);

        return number_format($precio + $extra + $IVA, 2);
    }

    public function calcularTotalMasCobertura(Envio $envio)
    {
        $precio = $this->calcularPrecio($envio);
        $extra = $this->calcularExtra($envio);
        $IVA = $this->calcularIVA($envio);

        return number_format($precio + $extra + $IVA + $envio->cobertura->valor, 2);
    }

    public function calcularTotalMasCoberturaAndEmbalaje(Envio $envio)
    {
        $precio = $this->calcularPrecio($envio);
        $extra = $this->calcularExtra($envio);
        $IVA = $this->calcularIVA($envio);

        return number_format($precio + $extra + $IVA + $envio->cobertura->valor + $envio->embalaje->precio, 2);
    }

    /*
    *
    * Calculo de precios por array de envíos
    *
    */

    /* Cálculo de total precio base de cada envío */
    public function calcularPrecioBase($envios)
    {
        $precioTotal = 0;

        foreach ($envios as $envio) {
            $precioTotal += $this->calcularPrecio($envio);
        }

        return number_format($precioTotal,2);
    }

    /* Cálculo de precio de extras de gestión de array de envíos */
    public function calcularExtrasTotal($envios)
    {
        $precioExtras = 0;

        foreach ($envios as $envio) {
            $precioExtras += $this->calcularExtra($envio);
        }

        $iva = (($precioExtras) * $this->opciones->getImpuestos()) / 100;

        return number_format($precioExtras + $iva,2);
    }

    /* Cálculo de IVA de array de envíos */
    public function calcularIVATotal($envios)
    {
        $precioTotal = $this->calcularPrecioBase($envios);
        $extraTotal = $this->calcularExtrasTotal($envios);
        $iva = (($precioTotal + $extraTotal) * $this->opciones->getImpuestos()) / 100;

        return number_format($iva, 2);
    }

    /* Cálculo total de array de envíos */
    public function calcularPedido($envios)
    {
        $precioTotal = $this->calcularPrecioBase($envios);
        $extraTotal = $this->calcularExtrasTotal($envios);
        $coberturaTotal = $this->calcularTotalCoberturas($envios);
        $embalajeTotal = $this->calcularTotalEmbalajes($envios);
        #$IVATotal = $this->calcularIVATotal($envios);

        // Descuento

        $seleccionado = null;
        foreach ($envios as $envio) {
            if($seleccionado == null) {
                $seleccionado = $envio;
            } elseif ($this->calcularPrecio($seleccionado) < $this->calcularPrecio($envio)) {
                $seleccionado = $envio;
            }
        }
        if(!is_null($seleccionado)) {
            $uso = UsoCodigo::where('envio_id', $seleccionado->id)->first();
            if (!is_null($uso)) {
                $descuento = $uso->codigo->valor;
                $precioSeleccionado = $this->calcularTotalMasCoberturaAndEmbalaje($seleccionado);
                if ($precioSeleccionado < $uso->codigo->valor) {
                    $descuento = $precioSeleccionado;
                }
                return number_format(abs($precioTotal + $extraTotal + $coberturaTotal + $embalajeTotal - $descuento), 2);
            }
        }

        return number_format($precioTotal + $extraTotal + $coberturaTotal + $embalajeTotal, 2);
    }

    public function calcularPedidoSinDescuento($envios)
    {
        $precioTotal = $this->calcularPrecioBase($envios);
        $extraTotal = $this->calcularExtrasTotal($envios);
        $coberturaTotal = $this->calcularTotalCoberturas($envios);
        $embalajeTotal = $this->calcularTotalEmbalajes($envios);
        #$IVATotal = $this->calcularIVATotal($envios);

        return number_format($precioTotal + $extraTotal + $coberturaTotal + $embalajeTotal, 2);
    }

    public function calcularPedidoSinDescuentoMasIVA($envios)
    {
        $precioTotal = $this->calcularPrecioBase($envios);
        $extraTotal = $this->calcularExtrasTotal($envios);
        $coberturaTotal = $this->calcularTotalCoberturas($envios);
        $embalajeTotal = $this->calcularTotalEmbalajes($envios);

        $total = $precioTotal + $extraTotal + $coberturaTotal + $embalajeTotal;
        $iva = $this->calcularIvaFromPrecio($total);

        return number_format($total+$iva, 2);
    }

    public function calcularCobertura($envio) {
//        $iva = $envio->cobertura->valor * $this->opciones->getImpuestos() / 100;
        return number_format($envio->cobertura->valor, 2);
    }

    public function calcularTotalCoberturas($envios) {

        $totalCobertura = 0;

        foreach($envios as $envio) {
            $totalCobertura += floatval($envio->cobertura->valor);
        }

//        $iva = $totalCobertura * $this->opciones->getImpuestos() / 100;

        return number_format($totalCobertura, 2);

    }

    public function calcularEmbalaje($envio) {
        return number_format($envio->embalaje->precio, 2);
    }

    public function calcularTotalEmbalajes($envios) {

        $totalEmbalaje = 0;

        foreach($envios as $envio) {
            $totalEmbalaje += floatval($envio->embalaje->precio);
        }

        return number_format($totalEmbalaje, 2);

    }

    public function calcularIvaFromPrecio($precio, $impuestos = null) {
        if($impuestos) {
            return $precio * $impuestos / 100;
        } else {
            return $precio * $this->opciones->getImpuestos() / 100;
        }
    }

    public function calcularDescuentoPedido($envios) {
        $descuento = 0;

        $seleccionado = null;
        foreach ($envios as $envio) {
            if($seleccionado == null) {
                $seleccionado = $envio;
            } elseif ($this->calcularPrecio($seleccionado) < $this->calcularPrecio($envio)) {
                $seleccionado = $envio;
            }
        }
        if(!is_null($seleccionado)) {
            $uso = UsoCodigo::where('envio_id', $seleccionado->id)->first();
            if (!is_null($uso)) {
                $descuento = $uso->codigo->valor;
                $precioSeleccionado = $this->calcularTotalMasCoberturaAndEmbalaje($seleccionado);
                if ($precioSeleccionado < $uso->codigo->valor) {
                    $descuento = $precioSeleccionado;
                }
            }
        }
        return number_format($descuento, 2);
    }

}
