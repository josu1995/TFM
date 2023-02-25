<?php

namespace App\Repositories\Business;

// Helpers
use App\Helpers\ModelHelper;

use App\Models\TiposRecogidaBusiness;
use DB;
// Servicios
use App\Services\CalcularPrecio;

// Modelos
use App\Models\Usuario;
use App\Models\Envio;
use App\Models\Punto;
use App\Models\Cobertura;
use App\Models\Estado;
use App\Models\Metodo;
use App\Models\Ruta;
use Illuminate\Http\Request;

class EnvioRepository
{

    public $mensajesCreacionEnvio = [
        'referencia_pedido.max' => 'El número de referencia no puede contener más de 255 caracteres.',
        'referencia_pedido.unique' => 'El número de pedido introducido ha sido usado anteriormente.',
        'nombre_producto.*.required' => 'El nombre de producto es obligatorio.',
        'nombre_producto.*.max' => 'El nombre de producto no puede contener más de 255 caracteres.',
        'num_productos.*.required' => 'El número de productos es obligatorio.',
        'num_productos.*.numeric' => 'El número de productos debe ser numérico.',
        'num_productos.*.min' => 'El número de productos debe ser mayor que 0.',
        'peso_producto.*.required' => 'El peso de producto es obligatorio.',
        'peso_producto.*.regex' => 'El peso de producto debe ser numérico.',
        'peso_producto.*.sum_pesos_min' => 'La suma de pesos de los productos debe ser mayor que 0.10Kg.',
        'peso_producto.*.sum_pesos' => 'La suma de pesos de los productos no puede superar los 20kg.',
        'embalaje.required' => 'El embalaje es obligatorio.',
        'embalaje.numeric' => 'El embalaje debe ser numérico.',
        'embalaje.embalaje_business' => 'El embalaje seleccionado no existe entre tus embalajes.',
        'alto.required' => 'El alto del embalaje es obligatorio.',
        'alto.numeric' => 'El alto del embalaje debe ser numérico.',
        'alto.sum_medidas_store' => 'La suma de las medidas del paquete no pueden superar los 150cm',
        'alto.sum_medidas_domicilio' => 'La suma de las medidas del paquete no pueden superar los 250cm',
        'alto.max_medidas' => 'El alto no puede superar los 120cm',
        'ancho.required' => 'El ancho del embalaje es obligatorio.',
        'ancho.numeric' => 'El ancho del embalaje debe ser numérico.',
        'ancho.max_medidas' => 'El ancho no puede superar los 120cm',
        'largo.required' => 'El largo del embalaje es obligatorio.',
        'largo.numeric' => 'El largo del embalaje debe ser numérico.',
        'largo.max_medidas' => 'El largo no puede superar los 120cm',
        'cp_origen_id.numeric' => 'Es necesario seleccionar un código postal de origen del desplegable.',
        'cp_origen_id.exists' => 'El código postal de origen introducido no existe en el sistema.',
        'punto_origen_id.required_if' => 'Es necesario seleccionar un punto de origen.',
        'tipo_recogida_id.required' => 'El tipo de recogida en origen es obligatorio.',
        'tipo_recogida_id.numeric' => 'Es necesario seleccionar un tipo de recogida en origen del desplegable.',
        'tipo_recogida_id.exists' => 'El tipo de recogida en origen seleccionado no existe en el sistema.',
        'nombre.required' => 'El nombre del destinatario es obligatorio.',
        'nombre.min' => 'El nombre del destinatario debe tener 2 caracteres como mínimo.',
        'nombre.max' => 'El nombre del destinatario debe tener 32 caracteres como máximo.',
        'apellidos.required' => 'Los apellidos del destinatario son obligatorios.',
        'apellidos.min' => 'Los apellidos del destinatario debe tener 2 caracteres como mínimo.',
        'apellidos.max' => 'Los apellidos del destinatario debe tener 32 caracteres como máximo.',
        'email.required' => 'El email del destinatario es obligatorio.',
        'email.min' => 'El email del destinatario debe tener 7 caracteres como mínimo.',
        'email.max' => 'El email del destinatario debe tener 70 caracteres como máximo.',
        'email.email' => 'El email del destinatario no tiene un formato correcto.',
        'prefijo.required' => 'El prefijo telefónico es obligatorio.',
        'prefijo.exists' => 'El prefijo indicado no es correcto.',
        'telefono.required' => 'El teléfono del destinatario es obligatorio.',
        'telefono.numeric' => 'El teléfono del destinatario debe ser numérico.',
        'telefono.business_phone' => 'El teléfono del destinatario no tiene un formato correcto para el país indicado.',
        'pais_destino_id.required' => 'El país de destino es obligatorio.',
        'pais_destino_id.numeric' => 'Es necesario seleccionar un país de destino del dedsplegable.',
        'pais_destino_id.exists' => 'El país de destino seleccionado no existe en el sistema.',
        'cp_destino_id.required' => 'El código postal de destino es obligatorio.',
        'cp_destino_id.numeric' => 'Es necesario seleccionar un código postal de destino del desplegable.',
        'cp_destino_id.exists' => 'El código postal de destino seleccionado no existe en el sistema.',
        'tipo_entrega_destino_id.required' => 'El tipo de entrega en destino es obligatorio.',
        'tipo_entrega_destino_id.numeric' => 'Es necesario seleccionar un tipo de entrega en destino del desplegable.',
        'tipo_entrega_destino_id.exists' => 'El tipo de entrega en destino seleccionado no existe en el sistema.',
        'direccion_destino.required_if' => 'La dirección de destino es obligatoria.',
        'direccion_destino.min' => 'La dirección de destino debe ser mayor que 2 caracteres.',
        'direccion_destino.max' => 'La dirección de destino debe ser menor que 64 caracteres.',
    ];

    // Mensajes de devolución de validación
    public $mensajesImportacionExcel = [
        '*.referecia_pedido.max' => 'La referencia debe tener 40 caracteres como máximo.',
        '*.referecia_pedido.unique' => 'La referencia introducida se encuentra en uso.',
        '*.cp_origen.required' => 'El código postal de origen es obligatorio.',
        '*.cp_origen.numeric' => 'El código postal de origen debe ser un número.',
        '*.cp_origen.exists' => 'El código postal de origen no existe en el sistema.',
        '*.tipo_recogida.required' => 'El tipo de recogida es obligatorio.',
        '*.tipo_recogida.in' => 'El tipo de recogida debe contener los valores S (recogida en Store) o D (recogida a domicilio).',
        '*.store_origen.numeric' => 'El ID de Store de origen debe ser un número.',
        '*.store_origen.store_exists_array' => 'El ID de Store de origen no existe en el sistema.',
        '*.direccion_origen.max' => 'La dirección de origen debe contener menos de 64 caracteres.',
        '*.nombre_destinatario.required' => 'El nombre de destinatario es obligatorio.',
        '*.nombre_destinatario.min' => 'El nombre del destinatario debe tener 2 caracteres como mínimo.',
        '*.nombre_destinatario.max' => 'El nombre del destinatario debe tener 32 caracteres como máximo.',
        '*.apellido_destinatario.required' => 'El apellido de destinatario es obligatorio.',
        '*.apellido_destinatario.min' => 'Los apellidos del destinatario debe tener 2 caracteres como mínimo.',
        '*.apellido_destinatario.max' => 'Los apellidos del destinatario debe tener 32 caracteres como máximo.',
        '*.email_destinatario.required' => 'El email de destinatario es obligatorio.',
        '*.email_destinatario.min' => 'El email del destinatario debe tener 7 caracteres como mínimo.',
        '*.email_destinatario.max' => 'El email del destinatario debe tener 70 caracteres como máximo.',
        '*.email_destinatario.email' => 'El email de destinatario no tiene un formato correcto.',
        '*.telefono_destinatario.required' => 'El teléfono de destinatario es obligatorio.',
        '*.telefono_destinatario.numeric' => 'El teléfono de destinatario debe ser numérico.',
        '*.pais_destino.required' => 'El país de destino es obligatorio.',
        '*.pais_destino.max' => 'El país de destino debe tener menos de 255 caracteres.',
        '*.pais_destino.exists' => 'El país de destino introducido no existe en el sistema.',
        '*.cp_destino.required' => 'El código postal de destino es obligatorio.',
        '*.cp_destino.numeric' => 'El código postal de destino debe ser un número.',
        '*.cp_destino.exists' => 'El código postal de destino no existe en el sistema.',
        '*.tipo_entrega.required' => 'El tipo de entrega es obligatorio.',
        '*.tipo_entrega.in' => 'El tipo de entrega debe contener los valores S (recogida en Store) o D (recogida a domicilio).',
        '*.store_destino.numeric' => 'El ID de Store de destino debe ser un número.',
        '*.store_destino.store_exists_array' => 'El ID de Store de destino no existe en el sistema.',
        '*.direccion_destino.min' => 'La dirección de destino debe contener más de 2 caracteres.',
        '*.direccion_destino.max' => 'La dirección de destino debe contener menos de 64 caracteres.',
        '*.embalaje.required' => 'El embalaje es obligatorio.',
        '*.embalaje.max' => 'El embalaje debe tener menos de 255 caracteres.',
        '*.largo.required_with' => 'El largo del embalaje es obligatorio si el ancho o el alto están especificados.',
        '*.largo.numeric' => 'El largo del embalaje debe ser un número.',
        '*.largo.min' => 'El largo del embalaje debe ser mayor que 0.',
        '*.largo.sum_medidas_array' => 'La suma de las medidas del embalaje no pueden superar los 150cm.',
        '*.largo.sum_medidas_dom_es_array' => 'La suma de las medidas del embalaje no pueden superar los 250cm.',
        '*.largo.max_medidas_array' => 'El largo no puede superar los 120cm.',
        '*.alto.required_with' => 'El alto del embalaje es obligatorio si el ancho o el largo están especificados.',
        '*.alto.numeric' => 'El alto del embalaje debe ser un número.',
        '*.alto.min' => 'El alto del embalaje debe ser mayor que 0.',
        '*.alto.max_medidas_array' => 'El alto no puede superar los 120cm.',
        '*.ancho.required_with' => 'El ancho del embalaje es obligatorio si el alto o el largo están especificados.',
        '*.ancho.numeric' => 'El ancho del embalaje debe ser un número.',
        '*.ancho.min' => 'El ancho del embalaje debe ser mayor que 0.',
        '*.ancho.max_medidas_array' => 'El ancho no puede superar los 120cm.',
        '*.productos.*.producto.required' => 'El nombre de producto es obligatorio.',
        '*.productos.*.producto.max' => 'El nombre de producto debe tener menos de 255 caracteres.',
        '*.productos.*.cantidad.required' => 'La cantidad de un producto es obligatoria.',
        '*.productos.*.cantidad.numeric' => 'La cantidad de un producto debe un número.',
        '*.productos.*.cantidad.min' => 'La cantidad de un producto debe ser mayor que 0.',
        '*.productos.*.peso.required' => 'El peso de un producto es obligatorio.',
        '*.productos.*.peso.regex' => 'El peso de un producto debe un número.',
        '*.productos.*.peso.min' => 'El peso de un producto debe ser mayor que 0.',
    ];

    public $mensajesPostEnvioApiBusiness = [
        'orderReference.max' => 'El número de referencia no puede contener más de 255 caracteres.',
        'orderReference.uniqueOrderId' => 'El número de pedido introducido ha sido usado anteriormente.',
        'products.present' => 'La solicitud debe contener al menos un producto.',
        'products.array' => 'Los productos no tienen un formato correcto.',
        'products.*.name.required' => 'El nombre de producto es obligatorio.',
        'products.*.name.max' => 'El nombre de producto no puede contener más de 255 caracteres.',
        'products.*.quantity.required' => 'El número de productos es obligatorio.',
        'products.*.quantity.numeric' => 'El número de productos debe ser numérico.',
        'products.*.quantity.min' => 'El número de productos debe ser mayor que 0.',
        'products.*.weight.required' => 'El peso de producto es obligatorio.',
        'products.*.weight.regex' => 'El peso de producto debe ser numérico.',
        'products.*.weight.sum_pesos_min' => 'La suma de pesos de los productos debe ser mayor que 0.10Kg.',
        'products.*.weight.sum_pesos' => 'La suma de pesos de los productos no puede superar los 20kg.',
        'package.present' => 'El paquete es obligatorio.',
        'package.array' => 'El formato del paquete no es correcto.',
        'package.height.required' => 'El alto del embalaje es obligatorio.',
        'package.height.numeric' => 'El alto del embalaje debe ser numérico.',
        'package.height.sum_medidas_store_api' => 'La suma de las medidas del paquete no pueden superar los 150cm.',
        'package.height.sum_medidas_domicilio_api' => 'La suma de las medidas del paquete no pueden superar los 250cm.',
        'package.height.max_medidas' => 'El alto del embalaje no puede superar los 120cm.',
        'package.height.min' => 'El alto debe ser mayor que 0.',
        'package.width.required' => 'El ancho del embalaje es obligatorio.',
        'package.width.numeric' => 'El ancho del embalaje debe ser numérico.',
        'package.width.max_medidas' => 'El ancho del embalaje no puede superar los 120cm',
        'package.width.min' => 'El ancho del embalaje debe ser mayor que 0.',
        'package.depth.required' => 'El largo del embalaje es obligatorio.',
        'package.depth.numeric' => 'El largo del embalaje debe ser numérico.',
        'package.depth.max_medidas' => 'El largo del embalaje no puede superar los 120cm',
        'package.depth.min' => 'El largo del embalaje debe ser mayor que 0.',
        'customer.present' => 'El destinatario es obligatorio.',
        'customer.array' => 'El formato del destinatario no es correcto.',
        'customer.firstName.required' => 'El nombre del destinatario es obligatorio.',
        'customer.firstName.min' => 'El nombre del destinatario debe tener 2 caracteres como mínimo.',
        'customer.firstName.max' => 'El nombre del destinatario debe tener 32 caracteres como máximo.',
        'customer.lastName.required' => 'Los apellidos del destinatario son obligatorios.',
        'customer.lastName.min' => 'Los apellidos del destinatario debe tener 2 caracteres como mínimo.',
        'customer.lastName.max' => 'Los apellidos del destinatario debe tener 32 caracteres como máximo.',
        'customer.email.required' => 'El email del destinatario es obligatorio.',
        'customer.email.min' => 'El email del destinatario debe tener 7 caracteres como mínimo.',
        'customer.email.max' => 'El email del destinatario debe tener 70 caracteres como máximo.',
        'customer.email.email' => 'El email del destinatario no tiene un formato correcto.',
        'customer.phone.required' => 'El teléfono del destinatario es obligatorio.',
        'customer.phone.numeric' => 'El teléfono del destinatario debe ser numérico.',
        'customer.phone.business_api_phone' =>
            'El teléfono del destinatario no tiene un formato correcto para el país indicado.',
        'destination.present' => 'El destino es obligatorio.',
        'destination.array' => 'El formato del destino no es correcto.',
        'destination.country.required' => 'El país de destino es obligatorio.',
        'destination.country.numeric' => 'Es necesario seleccionar un país de destino del dedsplegable.',
        'destination.country.exists' => 'El país de destino seleccionado no existe en el sistema.',
        'destination.postcode.required' => 'El código postal de destino es obligatorio.',
        'destination.postcode.numeric' => 'Es necesario seleccionar un código postal de destino del desplegable.',
        'destination.postcode.exists' => 'El código postal de destino seleccionado no existe en el sistema.',
        'destination.address1.required_if' => 'La dirección de destino es obligatoria.',
        'destination.address1.min' => 'La dirección de destino debe ser mayor que 2 caracteres.',
        'destination.address1.max' => 'La dirección de destino debe ser menor que 64 caracteres.',
        'destination.address2.required_if' => 'La dirección de destino es obligatoria.',
        'destination.address2.min' => 'La dirección de destino debe ser mayor que 2 caracteres.',
        'destination.address2.max' => 'La dirección de destino debe ser menor que 64 caracteres.',
        'destination.storeId.id_store_exists' => 'El store indicado no existe en el sistema.'
    ];

    public function getApiBusinessPostEnvioRules(Request $request)
    {
        $ecommerce = $request->get('ecommerce');

        $preferencia = $ecommerce->preferenciaRecogida;

        $jsonRequest = json_decode($request->getContent());

        if ($preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO
            && (!isset($jsonRequest->destination->storeId) || !$jsonRequest->destination->storeId)
            && (isset($jsonRequest->destination)
                && isset($jsonRequest->destination->country)
                && $jsonRequest->destination->country == 'ES')
        ) {
            $maxMedidasValidation = '';
        } else {
            $maxMedidasValidation = '|max_medidas';
        }

        if (isset($jsonRequest->destination) && isset($jsonRequest->destination->country)) {
            $phoneValidation = '|business_api_phone:' . $jsonRequest->destination->country;
        } else {
            $phoneValidation = '';
        }

        return [
            'orderReference' => 'sometimes' .
                '|nullable' .
                '|max:255' .
                '|uniqueOrderId:' . $ecommerce->id,
//                '|unique:pedidos_business,num_pedido,null,null,deleted_at,null,configuracion_business_id,'
//                . $ecommerce->id,
            'products' => 'present|array',
            'products.*.name' => 'required|max:255',
            'products.*.quantity' => 'required|numeric|integer|min:1',
            'products.*.weight' => 'bail' .
                '|required' .
                '|regex:/^[0-9]\d*(\.\d+)?$/i' .
                '|sum_pesos_min:0,weight,quantity' .
                '|sum_pesos:0,weight,quantity',
            'package' => 'present|array',
//            'package.height' => 'required' .
//                '|numeric' .
//                '|sum_medidas_store_api:' . $ecommerce->preferenciaRecogida->tipo_recogida_id .
//                '|sum_medidas_domicilio_api:' . $ecommerce->preferenciaRecogida->tipo_recogida_id .
//                '|min:0.1'
//                . $maxMedidasValidation,
//            'package.width' => 'required|numeric|min:0.1' . $maxMedidasValidation,
//            'package.depth' => 'required|numeric|min:0.1' . $maxMedidasValidation,
            'customer' => 'present|array',
            'customer.firstName' => 'required|min:2|max:30',
            'customer.lastName' => 'required|min:2|max:30',
            'customer.email' => 'required|email|min:7|max:70',
            'customer.phone' => 'required|numeric' . $phoneValidation,
            'destination' => 'present|array',
            'destination.country' => 'required|exists:paises,iso2',
            'destination.postcode' => 'required|numeric|exists:codigos_postales,codigo_postal',
            'destination.address1' => 'required|min:2|max:50',
            'destination.address2' => 'sometimes|nullable|min:2|max:14',
            'destination.storeId' => (isset($jsonRequest->destination) && isset($jsonRequest->destination->country)) ?
                'sometimes' .
                '|nullable' .
                '|id_store_exists:' .
                $jsonRequest->destination->country :
                'sometimes' .
                '|nullable',
        ];
    }

    /**
     * Get envío creados (sin pagar) por Usuario
     *
     * @return Collection
     */
    public function enviosPendientesPorUsuario(Usuario $usuario)
    {
        $envios = $usuario->envios;
        $enviosPendientes = [];

        foreach ($envios as $envio) {
            if ($envio->estado_id < 3) {
                $enviosPendientes[] = $envio;
            }
        }

        return $enviosPendientes;
    }


    public function eliminarEnvio($seguimiento)
    {
        $envio = Envio::where('codigo_seguimiento', $seguimiento)->first();

        if ($envio->estado_id < 3) {
            $envio->delete();
        }

        return $envio;
    }

    public function getEnviosEnPunto(Punto $puntoOrigen, Punto $puntoDestino)
    {

        $envios =
            // Envíos concordantes con puntos
            Envio::where(function ($query) use ($puntoOrigen, $puntoDestino) {
                $query->where('punto_entrega_id', $puntoOrigen->id)
                    ->where('punto_recogida_id', $puntoDestino->id)
                    ->where('estado_id', Estado::ENTREGA);
            })
                ->orWhere(function ($query) use ($puntoOrigen, $puntoDestino) {
                    $query->where('punto_recogida_id', $puntoDestino->id)
                        ->whereHas('posiciones', function ($query) use ($puntoOrigen) {
                            $query->where('punto_destino_id', $puntoOrigen->id);
                        })
                        ->where('estado_id', Estado::INTERMEDIO);
                })
                ->with('paquete')
                ->orderBy('envios.fecha_almacen')
                ->get();

        return $envios;
    }

    public function getIntermediosEnOrigen(Punto $puntoOrigen, Punto $puntoDestino)
    {

        $localidades = Ruta::where('localidad_inicio_id', $puntoOrigen->localidad->id)
            ->where('localidad_intermedia_id', $puntoDestino->localidad->id)
            ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        $envios =
            // Envíos concordantes con puntos
            Envio::
            // Envios entre punto origen y localidad intermedia
            where(function ($query) use ($puntoOrigen, $puntoDestino, $puntos) {
//                if($puntoDestino->completo == 0) {
                $query->where('punto_entrega_id', $puntoOrigen->id)
                    ->whereIn('punto_recogida_id', $puntos)
                    ->where('estado_id', Estado::ENTREGA);
//                }
            })
                ->with('paquete')
                ->orderBy('envios.fecha_almacen')
                ->get();

        return $envios;
    }

    public function getIntermediosEnPuntoIntermedio(Punto $puntoOrigen, Punto $puntoDestino)
    {

        $localidades = Ruta::where('localidad_inicio_id', $puntoOrigen->localidad->id)
            ->where('localidad_intermedia_id', $puntoDestino->localidad->id)
            ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        $envios =
            // Envíos concordantes con puntos
            Envio::
            // Envio que se encuentra en punto intermedio
            // La posición tiene como punto intermedio el punto que se recibe como origen de viaje
            // El punto intermedio es el último que se ha creado
            where(function ($query) use ($puntoOrigen, $puntoDestino) {
                $query->where('punto_recogida_id', $puntoDestino->id)
                    ->whereHas('posiciones', function ($query) use ($puntoOrigen) {
                        $query->where('punto_destino_id', $puntoOrigen->id);
                    })
                    ->where('estado_id', Estado::INTERMEDIO);
            })
                ->with('paquete')
                ->orderBy('envios.fecha_almacen')
                ->get();

        return $envios;
    }

    // Consultas de envíos para crear viajes
    public function getEnviosPorPunto(Punto $puntoOrigen, Punto $puntoDestino)
    {
        // Consulta SQL equivalente
        // "select * from `envios` inner join `puntos` on `envios`.`punto_entrega_id` = `puntos`.`id`
        // where ((`envios`.`punto_entrega_id` = ? and `envios`.`punto_recogida_id` = ? and `envios`.`estado_id` = ?)
        // or (`envios`.`punto_entrega_id` = ? and 0 = 1 and `estado_id` = ?)
        // or (`envios`.`punto_recogida_id` = ?
        // and exists (select * from `posiciones` where `posiciones`.`envio_id` = `envios`.`id` and `punto_id` = ? and `posiciones`.`deleted_at` is null)
        // and `estado_id` = ?)) and `envios`.`deleted_at` is null order by `envios`.`created_at` asc"

        // Cálculo de ciudad intermedias para puntos origen y destino
        $localidades = Ruta::where('localidad_inicio_id', $puntoOrigen->localidad->id)
            ->where('localidad_intermedia_id', $puntoDestino->localidad->id)
            ->get(['localidad_fin_id'])->pluck('localidad_fin_id')->toArray();

        $puntos = Punto::whereIn('localidad_id', $localidades)->pluck('id')->toArray();

        $envios =
            // Envíos concordantes con puntos
            Envio::where(function ($query) use ($puntoOrigen, $puntoDestino) {
                $query->where('punto_entrega_id', $puntoOrigen->id)
                    ->where('punto_recogida_id', $puntoDestino->id)
                    ->where('estado_id', Estado::ENTREGA);
            })
                // Envios entre punto origen y localidad intermedia
                ->orWhere(function ($query) use ($puntoOrigen, $puntoDestino, $puntos) {
//                    if($puntoDestino->completo == 0) {
                    $query->where('punto_entrega_id', $puntoOrigen->id)
                        ->whereIn('punto_recogida_id', $puntos)
                        ->where('estado_id', Estado::ENTREGA);
//                    }
                })
                // Envio que se encuentra en punto intermedio
                // La posición tiene como punto intermedio el punto que se recibe como origen de viaje
                // El punto intermedio es el último que se ha creado
                ->orWhere(function ($query) use ($puntoOrigen, $puntoDestino) {
                    $query->where('punto_recogida_id', $puntoDestino->id)
                        ->whereHas('posiciones', function ($query) use ($puntoOrigen) {
                            $query->where('punto_destino_id', $puntoOrigen->id);
                        })
                        ->where('estado_id', Estado::INTERMEDIO);
                })
                ->with('paquete')
                ->orderBy('envios.fecha_almacen')
                ->get();

        /*
        No hay búsqueda entre pto. intermedio y pto. intermedio para evitar círculos cerrados de transporte
        Sólo origen-destino, origen-punto intermedio, punto intermedio-destino
         */

        return $envios;
    }
}
