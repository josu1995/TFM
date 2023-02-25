<?php

namespace App\Services\Business;

// Utilidades
use App\Events\CambiosEstadoBusiness;
use App\Models\EnvioBusiness;
use App\Models\Estado;
use App\Models\Punto;
use App\Models\TipoDevolucionBusiness;
use App\Models\TipoOrigenBusiness;
use App\Models\TiposRecogidaBusiness;
use App\Models\TrazaBusinessMondialRelay;
use App\Services\MondialRelayService;
use Geocoder;
use Event;

class TrackingService
{

    protected $mondialRelayService;

    /**
     * Create a new controller instance.
     */
    public function __construct(MondialRelayService $mondialRelayService) {
        $this->mondialRelayService = $mondialRelayService;
    }

    public function getEstadoActual($envio) {
        $estado = [];
        $estado['buttonClass'] = 'btn-corporativo';

        if($envio->estado_id <= Estado::PAGADO) {
            $estado['titulo'] = 'Tu pedido está siendo preparado';
            $estado['subtitulo'] = 'Envío pendiente de recepción en nuestras instalaciones.';
            $estado['icon'] = '<i class="fas fa-box"></i>';
        } else {

            switch ($envio->estado_id) {
                case Estado::ENTREGA:
                    $estado['titulo'] = 'Tu pedido ha sido enviado';
                    $estado['subtitulo'] = 'Ya se encuentra en nuestro store de origen.';
                    $estado['icon'] = '<i class="material-icons store">store</i>';
                    $estado['buttonClass'] = 'btn-origen';
                    break;
                case Estado::RUTA:
                    $estado['titulo'] = 'Tu pedido se encuentra en ruta';
                    $estado['subtitulo'] = 'El transporter ha recogido tu paquete. Puedes seguir su avance en el mapa.';
                    $estado['icon'] = '<i class="material-icons truck">local_shipping</i>';
                    break;
                case Estado::INTERMEDIO:
                    $estado['titulo'] = 'Tu pedido está en nuestras instalaciones';
                    $estado['subtitulo'] = 'En breve estará camino de tu ciudad.';
                    $estado['icon'] = '<i class="material-icons store">store</i>';
                    break;
                case Estado::RECOGIDA:
                    $estado['titulo'] = 'Tu pedido ya está en destino';
                    $estado['subtitulo'] = 'Puedes recogerlo en tu store más cercano cuando desees.';
                    $estado['icon'] = '<i class="material-icons store">store</i>';
                    $estado['buttonClass'] = 'btn-destino';
                    break;
                case Estado::SELECCIONADO:
                    if(!count($envio->viajesFinalizados)) {
                        $estado['titulo'] = 'Tu pedido ha sido enviado';
                        $estado['subtitulo'] = 'Ya se encuentra en nuestro store de origen.';
                        $estado['icon'] = '<i class="material-icons store">store</i>';
                        $estado['buttonClass'] = 'btn-origen';
                    } else {
                        $estado['titulo'] = 'Tu pedido está en nuestras instalaciones';
                        $estado['subtitulo'] = 'En breve estará camino de tu ciudad.';
                        $estado['icon'] = '<i class="material-icons store">store</i>';
                    }
                    break;
                case Estado::FINALIZADO:
                    $estado['titulo'] = 'Pedido finalizado';
                    $estado['subtitulo'] = 'Disfruta de tu compra. ¡Gracias por confiar en nosotros!';
                    $estado['icon'] = '<i class="material-icons check">check</i>';
                    break;
                case Estado::DEVUELTO:
                    $estado['titulo'] = 'Pedido devuelto';
                    $estado['subtitulo'] = 'Tu paquete ha sido devuelto al remitente por superar los intentos de entrega y/o el tiempo máximo para su recogida.';
                    $estado['icon'] = '<i class="material-icons devolucion">settings_backup_restore</i>';
                    $estado['buttonClass'] = 'btn-dark';
                    break;
                case Estado::REPARTO:
                    $estado['titulo'] = 'Tu pedido se encuentra en reparto';
                    $estado['subtitulo'] = 'El repartidor ha salido con tu pedido. En breve dispondrás de tu compra.';
                    $estado['icon'] = '<i class="fas fa-car-alt"></i>';
                    $estado['buttonClass'] = 'btn-corporativo';
                    break;
            }
        }

        return $estado;
    }

    public function getProximoEstado($envio) {
        $estado = [];
        $estado['buttonClass'] = 'btn-corporativo';

        $tipoOrigen = null;

        if($envio->tipo_origen_id == \App\Models\TipoOrigenBusiness::NUEVO) {
            $tipoOrigen = $envio->origen->tipo_recogida_id;
            if($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $origenLocalidad = $envio->origen->codigoPostal->ciudad;
            } else {
                if($envio->origen->mondialRelayStore) {
                    $origenLocalidad = $envio->origen->mondialRelayStore->codigoPostal->ciudad;
                } else {
                    $origenLocalidad = $envio->origen->store->codigoPostal->ciudad;
                }
            }
        } else {
            $preferencia = $envio->preferenciaRecogida;
            $tipoOrigen = $preferencia->tipo_recogida_id;
            if($preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $origenLocalidad = $preferencia->codigoPostal->ciudad;
            } else {
                if($preferencia->mondialRelayStore) {
                    $origenLocalidad = $preferencia->mondialRelayStore->codigoPostal->ciudad;
                } else {
                    $origenLocalidad = $preferencia->store->codigoPostal->ciudad;
                }
            }
        }

        if($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
            $destinoLocalidad = $envio->destino->codigoPostal->ciudad;
        } else {
            if($envio->destino->mondialRelayStore) {
                $destinoLocalidad = $envio->destino->mondialRelayStore->codigoPostal->ciudad;
            } else {
                $destinoLocalidad = $envio->destino->store->codigoPostal->ciudad;
            }
        }

        switch ($envio->estado_id) {
            case Estado::PAGADO:
                $texto = 'Store de origen';
                return ['id' => Estado::ENTREGA, 'titulo' => $texto,
                    'direccion' => $origenLocalidad,
                    'icon' => '<i class="material-icons store">store</i>',
                    'buttonClass' => 'btn-origen'];
                break;
            case Estado::ENTREGA:
                return ['id' => Estado::RUTA, 'titulo' => 'En ruta',
                    'direccion' => $origenLocalidad . ' - ' . $destinoLocalidad,
                    'icon' => '<i class="material-icons truck">local_shipping</i>',
                    'buttonClass' => 'btn-corporativo'];
                break;
            case Estado::RUTA:
                $texto = 'Store de destino';
                if($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::STORE) {
                    return ['id' => Estado::RECOGIDA, 'titulo' => $texto,
                        'direccion' => $destinoLocalidad,
                        'icon' => '<i class="material-icons store">store</i>',
                        'buttonClass' => 'btn-destino'];
                } else {
                    return ['id' => Estado::RECOGIDA, 'titulo' => $texto,
                        'direccion' => $destinoLocalidad,
                        'icon' => '<i class="material-icons store">store</i>',
                        'buttonClass' => 'btn-destino'];
//                    return ['id' => Estado::REPARTO, 'titulo' => 'En reparto',
//                        'direccion' => $envio->destino->codigoPostal->ciudad,
//                        'icon' => '<i class="fas fa-car-alt"></i>',
//                        'buttonClass' => 'btn-corporativo'];
                }
                break;
            case Estado::RECOGIDA:
                return ['id' => Estado::FINALIZADO, 'titulo' => 'Finalizado',
                    'direccion' => '',
                    'icon' => '<i class="material-icons check">check</i>',
                    'buttonClass' => 'btn-corporativo'];
                break;
            case Estado::REPARTO:
                return ['id' => Estado::FINALIZADO, 'titulo' => 'Finalizado',
                    'direccion' => '',
                    'icon' => '<i class="material-icons check">check</i>',
                    'buttonClass' => 'btn-corporativo'];
                break;
        }

        return null;
    }

    public function getMarkersInfo($envio) {
        $markers = [];

        if($envio->estado_id <= Estado::PAGADO) {
            if($envio->tipo_origen_id == \App\Models\TipoOrigenBusiness::NUEVO && $envio->origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
//                $params = array(
//                    'Enseigne' => env('MONDIAL_RELAY_ID'),
//                    'Pays' => $envio->origen->codigoPostal->codigo_pais,
//                    'CP' => $envio->origen->codigoPostal->codigo_postal,
//                    'NombreResultats' => 1,
//                );
//                $punto = $this->mondialRelayService->getPuntoCercano($params);
                $address = Geocoder::geocode($envio->origen->direccion . ' ' . $envio->origen->codigoPostal->codigo_postal . ' ' . $envio->origen->codigoPostal->ciudad . ' ' . $envio->origen->codigoPostal->codigo_pais)->get()->first();
                $punto = new Punto();
                $punto->latitud = $address->getCoordinates()->getLatitude();
                $punto->longitud = $address->getCoordinates()->getLongitude();
            } elseif ($envio->tipo_origen_id == \App\Models\TipoOrigenBusiness::NUEVO && $envio->origen->tipo_recogida_id == TiposRecogidaBusiness::STORE) {
                $punto = $envio->origen->mondialRelayStore ? $envio->origen->mondialRelayStore : $envio->origen->store;
            } else {
                $preferencia = $envio->preferenciaRecogida;
                if($preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
//                    $params = array(
//                        'Enseigne' => env('MONDIAL_RELAY_ID'),
//                        'Pays' => $preferencia->codigoPostal->codigo_pais,
//                        'CP' => $preferencia->codigoPostal->codigo_postal,
//                        'NombreResultats' => 1,
//                    );
//                    $punto = $this->mondialRelayService->getPuntoCercano($params);
                    $address = Geocoder::geocode($preferencia->direccion . ' ' . $preferencia->codigoPostal->codigo_postal . ' ' . $preferencia->codigoPostal->ciudad . ' ' . $preferencia->codigoPostal->codigo_pais)->get()->first();
                    $punto = new Punto();
                    $punto->latitud = $address->getCoordinates()->getLatitude();
                    $punto->longitud = $address->getCoordinates()->getLongitude();
                } else {
                    $punto = $preferencia->mondialRelayStore ? $preferencia->mondialRelayStore : $preferencia->store;
                }
            }
            array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-grey.png']);
        } else {
            $origenImage = null;
            $origenLatitud = null;
            $origenLongitud = null;
            $destinoLatitud = null;
            $destinoLongitud = null;
            if($envio->tipo_origen_id == \App\Models\TipoOrigenBusiness::NUEVO && $envio->origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
//                $address = Geocoder::geocode($envio->origen->direccion . ' ' . $envio->origen->codigoPostal->codigo_postal . ' ' . $envio->origen->codigoPostal->ciudad . ' ' . $envio->origen->codigoPostal->codigo_pais)->get()->first();
//                $origenLatitud = $address->getCoordinates()->getLatitude();
//                $origenLongitud = $address->getCoordinates()->getLongitude();
                $params = array(
                    'Enseigne' => env('MONDIAL_RELAY_ID'),
                    'Pays' => $envio->origen->codigoPostal->codigo_pais,
                    'CP' => $envio->origen->codigoPostal->codigo_postal,
                    'RayonRecherche' => 14,
                    'NombreResultats' => 1,
                );
                $punto = $this->mondialRelayService->getPuntoCercano($params);
                if($punto) {
                    $origenLatitud = $punto->latitud;
                    $origenLongitud = $punto->longitud;
                }
            } elseif ($envio->tipo_origen_id == \App\Models\TipoOrigenBusiness::NUEVO && $envio->origen->tipo_recogida_id == TiposRecogidaBusiness::STORE) {
                if($envio->origen->mondialRelayStore) {
                    $origenLatitud = $envio->origen->mondialRelayStore->latitud;
                    $origenLongitud = $envio->origen->mondialRelayStore->longitud;
                } else {
                    $origenLatitud = $envio->origen->store->latitud;
                    $origenLongitud = $envio->origen->store->longitud;
                }
            } else {
                $preferencia = $envio->preferenciaRecogida;
                if($preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
//                    $address = Geocoder::geocode($preferencia->direccion . ' ' . $preferencia->codigoPostal->codigo_postal . ' ' . $preferencia->codigoPostal->ciudad . ' ' . $preferencia->codigoPostal->codigo_pais)->get()->first();
//                    $origenLatitud = $address->getCoordinates()->getLatitude();
//                    $origenLongitud = $address->getCoordinates()->getLongitude();
                    $params = array(
                        'Enseigne' => env('MONDIAL_RELAY_ID'),
                        'Pays' => $preferencia->codigoPostal->codigo_pais,
                        'CP' => $preferencia->codigoPostal->codigo_postal,
                        'RayonRecherche' => 14,
                        'NombreResultats' => 1,
                    );
                    $punto = $this->mondialRelayService->getPuntoCercano($params);
                    if($punto) {
                        $origenLatitud = $punto->latitud;
                        $origenLongitud = $punto->longitud;
                    }
                } else {
                    if($preferencia->mondialRelayStore) {
                        $origenLatitud = $preferencia->mondialRelayStore->latitud;
                        $origenLongitud = $preferencia->mondialRelayStore->longitud;
                    } else {
                        $origenLatitud = $preferencia->store->latitud;
                        $origenLongitud = $preferencia->store->longitud;
                    }
                }
            }

            if($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
//                $address = Geocoder::geocode($envio->destino->direccion . ' ' . $envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
//                $destinoLatitud = $address->getCoordinates()->getLatitude();
//                $destinoLongitud = $address->getCoordinates()->getLongitude();
                $params = array(
                    'Enseigne' => env('MONDIAL_RELAY_ID'),
                    'Pays' => $envio->destino->codigoPostal->codigo_pais,
                    'CP' => $envio->destino->codigoPostal->codigo_postal,
                    'RayonRecherche' => 14,
                    'NombreResultats' => 1,
                );
                $punto = $this->mondialRelayService->getPuntoCercano($params);
                if($punto) {
                    $destinoLatitud = $punto->latitud;
                    $destinoLongitud = $punto->longitud;
                }
            } else {
                if($envio->destino->mondialRelayStore) {
                    $destinoLatitud = $envio->destino->mondialRelayStore->latitud;
                    $destinoLongitud = $envio->destino->mondialRelayStore->longitud;
                } else {
                    $destinoLatitud = $envio->destino->store->latitud;
                    $destinoLongitud = $envio->destino->store->longitud;
                }
            }

            switch ($envio->estado_id) {
                case Estado::ENTREGA:
                    if(!$origenLatitud || !$origenLongitud) {
                        if($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
                            $origen = $envio->preferenciaRecogida;
                        } else {
                            $origen = $envio->origen;
                        }
                        $address = Geocoder::geocode($origen->codigoPostal->codigo_postal . ' ' . $origen->codigoPostal->ciudad . ' ' . $origen->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    } else {
                        array_push($markers, ['latitud' => $origenLatitud, 'longitud' => $origenLongitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    }
                    break;
                case Estado::RUTA:
                    if(!$origenLatitud || !$origenLongitud) {
                        if($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
                            $origen = $envio->preferenciaRecogida;
                        } else {
                            $origen = $envio->origen;
                        }
                        $address = Geocoder::geocode($origen->codigoPostal->codigo_postal . ' ' . $origen->codigoPostal->ciudad . ' ' . $origen->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    } else {
                        array_push($markers, ['latitud' => $origenLatitud, 'longitud' => $origenLongitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    }
                    if(!$destinoLatitud || !$destinoLongitud) {
                        $address = Geocoder::geocode($envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    } else {
                        array_push($markers, ['latitud' => $destinoLatitud, 'longitud' => $destinoLongitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    }
                    break;
                case Estado::RECOGIDA:
                    if(!$destinoLatitud || !$destinoLongitud) {
                        $address = Geocoder::geocode($envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    } else {
                        array_push($markers, ['latitud' => $destinoLatitud, 'longitud' => $destinoLongitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    }
                    break;
                case Estado::FINALIZADO:
                    if(!$origenLatitud || !$origenLongitud) {
                        if($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
                            $origen = $envio->preferenciaRecogida;
                        } else {
                            $origen = $envio->origen;
                        }
                        $address = Geocoder::geocode($origen->codigoPostal->codigo_postal . ' ' . $origen->codigoPostal->ciudad . ' ' . $origen->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    } else {
                        array_push($markers, ['latitud' => $origenLatitud, 'longitud' => $origenLongitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    }
                    if(!$destinoLatitud || !$destinoLongitud) {
                        $address = Geocoder::geocode($envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    } else {
                        array_push($markers, ['latitud' => $destinoLatitud, 'longitud' => $destinoLongitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    }
                    break;
                case Estado::REPARTO:
                    $params = array(
                        'Enseigne' => env('MONDIAL_RELAY_ID'),
                        'Pays' => $envio->destino->codigoPostal->codigo_pais,
                        'CP' => $envio->destino->codigoPostal->codigo_postal,
                        'RayonRecherche' => 14,
                        'NombreResultats' => 1,
                    );
                    $punto = $this->mondialRelayService->getPuntoCercano($params);
                    if(!$punto) {
                        $address = Geocoder::geocode($envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    } else {
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-destino.png']);
                    }
                    $address = Geocoder::geocode($envio->destino->direccion . ' ' . $envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
                    $destinoLatitud = $address->getCoordinates()->getLatitude();
                    $destinoLongitud = $address->getCoordinates()->getLongitude();
                    array_push($markers, ['latitud' => $destinoLatitud, 'longitud' => $destinoLongitud, 'imagen' => '/img/maps/transporter-business-marker.png']);
                    break;

                case Estado::DEVUELTO:
                    if(!$origenLatitud || !$origenLongitud) {
                        if($envio->tipo_origen_id == TipoOrigenBusiness::PREFERENCIA) {
                            $origen = $envio->preferenciaRecogida;
                        } else {
                            $origen = $envio->origen;
                        }
                        $address = Geocoder::geocode($origen->codigoPostal->codigo_postal . ' ' . $origen->codigoPostal->ciudad . ' ' . $origen->codigoPostal->codigo_pais)->get()->first();
                        $punto = new Punto();
                        $punto->latitud = $address->getCoordinates()->getLatitude();
                        $punto->longitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $punto->latitud, 'longitud' => $punto->longitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    } else {
                        array_push($markers, ['latitud' => $origenLatitud, 'longitud' => $origenLongitud, 'imagen' => '/img/maps/transporter-marker-origen.png']);
                    }
                    if($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                        $address = Geocoder::geocode($envio->destino->direccion . ' ' . $envio->destino->codigoPostal->codigo_postal . ' ' . $envio->destino->codigoPostal->ciudad . ' ' . $envio->destino->codigoPostal->codigo_pais)->get()->first();
                        $destinoLatitud = $address->getCoordinates()->getLatitude();
                        $destinoLongitud = $address->getCoordinates()->getLongitude();
                        array_push($markers, ['latitud' => $destinoLatitud, 'longitud' => $destinoLongitud, 'imagen' => '/img/maps/transporter-marker-grey.png']);
                    } else {
                        array_push($markers, ['latitud' => $destinoLatitud, 'longitud' => $destinoLongitud, 'imagen' => '/img/maps/transporter-marker-grey.png']);
                    }
                    break;
            }
        }
        return $markers;
    }

    public function getInfoEstados($envio) {
        $result = [];

        $tipoOrigen = null;

        if($envio->tipo_origen_id == \App\Models\TipoOrigenBusiness::NUEVO) {
            $tipoOrigen = $envio->origen->tipo_recogida_id;
            if($envio->origen->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $origenLocalidad = $envio->origen->codigoPostal->ciudad;
            } else {
                if($envio->origen->mondialRelayStore) {
                    $origenLocalidad = $envio->origen->mondialRelayStore->codigoPostal->ciudad;
                } else {
                    $origenLocalidad = $envio->origen->store->codigoPostal->ciudad;
                }
            }
        } else {
            $preferencia = $envio->preferenciaRecogida;
            $tipoOrigen = $preferencia->tipo_recogida_id;
            if($preferencia->tipo_recogida_id == TiposRecogidaBusiness::DOMICILIO) {
                $origenLocalidad = $preferencia->codigoPostal->ciudad;
            } else {
                if($preferencia->mondialRelayStore) {
                    $origenLocalidad = $preferencia->mondialRelayStore->codigoPostal->ciudad;
                } else {
                    $origenLocalidad = $preferencia->store->codigoPostal->ciudad;
                }
            }
        }

        if($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
            $destinoLocalidad = $envio->destino->codigoPostal->ciudad;
        } else {
            if($envio->destino->mondialRelayStore) {
                $destinoLocalidad = $envio->destino->mondialRelayStore->codigoPostal->ciudad;
            } else {
                $destinoLocalidad = $envio->destino->store->codigoPostal->ciudad;
            }
        }

        $estadoActual = $envio->estado_id;
        if($estadoActual == Estado::REPARTO) {
            $estadoActual = Estado::RECOGIDA;
        }

        for($i = Estado::PAGADO ; $i <= $estadoActual ; $i++) {
            switch ($i) {
                case Estado::PAGADO:
                    array_push($result, ['id' => $i, 'titulo' => 'Pdte. de expedición',
                        'fecha' => date('Y/m/d H:i', strtotime($envio->fecha_pago)),
                        'direccion' => $origenLocalidad,
                        'icon' => '<i class="fas fa-box"></i>',
                        'buttonClass' => 'btn-corporativo'
                    ]);
                    break;
                case Estado::ENTREGA:
                    $texto = 'Store de origen';
                    array_push($result, ['id' => $i, 'titulo' => $texto,
                        'fecha' => $envio->fecha_origen ? date('Y/m/d H:i', strtotime($envio->fecha_origen)) : '',
                        'direccion' => $origenLocalidad,
                        'icon' => '<i class="material-icons store">store</i>',
                        'buttonClass' => 'btn-origen'
                    ]);
                    break;
                case Estado::RUTA:
                    if($envio->estado_id != Estado::SELECCIONADO) {
                        array_push($result, ['id' => Estado::RUTA, 'titulo' => 'En ruta',
                            'fecha' => $envio->fecha_ruta ? date('Y/m/d H:i', strtotime($envio->fecha_ruta)) : '',
                            'direccion' => $origenLocalidad . ' - ' . $destinoLocalidad,
                            'icon' => '<i class="material-icons truck">local_shipping</i>',
                            'buttonClass' => 'btn-corporativo'
                        ]);
                    }
                    break;
                case Estado::RECOGIDA:
                    if($envio->destino->tipo_entrega_id == TiposRecogidaBusiness::STORE) {
                        if ($envio->estado_id != Estado::SELECCIONADO) {
                            $texto = $envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO ? 'Dirección de destino' : 'Store de destino';
                            array_push($result, ['id' => $i, 'titulo' => $texto,
                                'fecha' => $envio->fecha_destino ? date('Y/m/d H:i', strtotime($envio->fecha_destino)) : '',
                                'direccion' => $destinoLocalidad,
                                'icon' => '<i class="material-icons store">store</i>',
                                'buttonClass' => 'btn-destino'
                            ]);
                        }
                    } else {

                        array_push($result, ['id' => $i, 'titulo' => 'Store de destino',
                            'fecha' => $envio->fecha_destino ? date('Y/m/d H:i', strtotime($envio->fecha_destino)) : '',
                            'direccion' => $destinoLocalidad,
                            'icon' => '<i class="material-icons store">store</i>',
                            'buttonClass' => 'btn-destino'
                        ]);

                        array_push($result, ['id' => $i, 'titulo' => 'En reparto',
                            'fecha' => $envio->fecha_destino ? date('Y/m/d H:i', strtotime($envio->fecha_destino)) : '',
                            'direccion' => $destinoLocalidad,
                            'icon' => '<i class="fas fa-car-alt"></i>',
                            'buttonClass' => 'btn-corporativo'
                        ]);
                    }
                    break;
                case Estado::FINALIZADO:
                    if($envio->estado_id != Estado::SELECCIONADO && $envio->estado_id != Estado::DEVUELTO) {
                        array_push($result, ['id' => $i, 'titulo' => 'Finalizado',
                            'fecha' => date('Y/m/d H:i', strtotime($envio->fecha_finalizacion)),
                            'direccion' => '',
                            'icon' => '<i class="material-icons check">check</i>',
                            'buttonClass' => 'btn-corporativo'
                        ]);
                    }
                    break;
                case Estado::DEVUELTO:
                    array_push($result, ['id' => $i, 'titulo' => 'Devuelto',
                        'fecha' => date('Y/m/d H:i', strtotime($envio->fecha_finalizacion)),
                        'direccion' => $destinoLocalidad . ' - ' . $origenLocalidad,
                        'icon' => '<i class="material-icons devolucion">settings_backup_restore</i>',
                        'buttonClass' => 'btn-dark'
                    ]);
                    break;
            }
        }

        return $result;
    }

    public function actualizarEstados($envio) {

        $params = [
            'Enseigne' => env('MONDIAL_RELAY_ID'),
            'Expedition' => substr($envio->localizador, 1),
            'Langue' => 'ES'
        ];

        $tracking = $this->mondialRelayService->getTracking($params);

        if($tracking) {
            foreach ($tracking as $key => $trace) {
                $traceStr = (string)$trace['Libelle'];
                $trazaMR = TrazaBusinessMondialRelay::where('traza', $traceStr)->first();

                if ($trazaMR) {

                    switch ($trazaMR->estado_id) {
                        case Estado::ENTREGA:
                            if (!$envio->fecha_origen && $envio->estado_id < Estado::ENTREGA) {
                                Event::fire(new CambiosEstadoBusiness([$envio], Estado::find(Estado::ENTREGA), \Carbon\Carbon::createFromFormat('d/m/y H:i',(string)$trace['Date'] . ' ' . (string)$trace['Heure'])));
                                \Log::info('Actualizado estado de envío ' . $envio->localizador . ' a origen');
                            }
                            break;
                        case Estado::RUTA:
                            if (!$envio->fecha_ruta && $envio->estado_id < Estado::RUTA) {
                                if (!$envio->fecha_origen) {
                                    $envio->fecha_origen = $envio->fecha_pago;
                                    $envio->save();
                                }
                                Event::fire(new CambiosEstadoBusiness([$envio], Estado::find(Estado::RUTA), \Carbon\Carbon::createFromFormat('d/m/y H:i',(string)$trace['Date'] . ' ' . (string)$trace['Heure'])));
                                \Log::info('Actualizado estado de envío ' . $envio->localizador . ' a en ruta');
                            }
                            break;
                        case Estado::RECOGIDA:
                            if (!$envio->fecha_destino && $envio->estado_id < Estado::RECOGIDA) {
                                Event::fire(new CambiosEstadoBusiness([$envio], Estado::find(Estado::RECOGIDA), \Carbon\Carbon::createFromFormat('d/m/y H:i',(string)$trace['Date'] . ' ' . (string)$trace['Heure'])));
                                \Log::info('Actualizado estado de envío ' . $envio->localizador . ' a destino');
                            }
                            break;
                        case Estado::REPARTO:
                            if($envio->devolucionAsDevolucion && $envio->devolucionAsDevolucion->tipo_devolucion_id == TipoDevolucionBusiness::RETORNO) {
                                if ($key == count($tracking) - 1 && !$envio->fecha_destino && $envio->estado_id < Estado::RECOGIDA && $envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                                    Event::fire(new CambiosEstadoBusiness([$envio], Estado::find(Estado::REPARTO), \Carbon\Carbon::createFromFormat('d/m/y H:i', (string)$trace['Date'] . ' ' . (string)$trace['Heure'])));
                                    \Log::info('Actualizado estado de envío ' . $envio->localizador . ' a en reparto');
                                }
                            } else {
                                if (!$envio->fecha_destino && $envio->estado_id < Estado::RECOGIDA && $envio->destino->tipo_entrega_id == TiposRecogidaBusiness::DOMICILIO) {
                                    Event::fire(new CambiosEstadoBusiness([$envio], Estado::find(Estado::REPARTO), \Carbon\Carbon::createFromFormat('d/m/y H:i', (string)$trace['Date'] . ' ' . (string)$trace['Heure'])));
                                    \Log::info('Actualizado estado de envío ' . $envio->localizador . ' a en reparto');
                                }
                            }
                            break;
                        case Estado::FINALIZADO:
                            if (!$envio->fecha_finalizacion && ($envio->estado_id < Estado::FINALIZADO || $envio->estado_id == Estado::REPARTO)) {
                                Event::fire(new CambiosEstadoBusiness([$envio], Estado::find(Estado::FINALIZADO), \Carbon\Carbon::createFromFormat('d/m/y H:i',(string)$trace['Date'] . ' ' . (string)$trace['Heure'])));
                                \Log::info('Actualizado estado de envío ' . $envio->localizador . ' a finalizado');
                            }
                            break;
                        case Estado::DEVUELTO:
                            if (!$envio->fecha_finalizacion && $envio->estado_id != Estado::DEVUELTO && $key == count($tracking) - 1) {
                                Event::fire(new CambiosEstadoBusiness([$envio], Estado::find(Estado::DEVUELTO), \Carbon\Carbon::createFromFormat('d/m/y H:i', (string)$trace['Date'] . ' ' . (string)$trace['Heure'])));
                                \Log::info('Actualizado estado de envío ' . $envio->localizador . ' a devuelto');
                            }
                            break;
                    }

                }
            }
        }
    }

}
