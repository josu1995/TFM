<?php

namespace App\Repositories;

// Modelos

use App\Models\DatosFacturacion;
use App\Models\ImagenDriver;
use App\Models\Profesional;
use App\Models\TipoCuentaProfesional;
use App\Models\Vehiculo;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Crypt;

class ProfesionalRepository
{

    protected $imageService;

    /**
     * Create a new repository instance.
     */
    public function __construct(ImageService $imageService) {
        $this->imageService = $imageService;
    }

    // Reglas de validación para autónomos
    public $reglasAutonomo = [
        'nombre' => 'required|max:255',
        'email' => 'required|email|max:255|unique:profesionales,email,NULL,id,deleted_at,NULL',
        'telefono' => 'required|phone:ES,mobile|unique:configuraciones,telefono|unique:profesionales,telefono,NULL,id,deleted_at,NULL',
        'referencia' => 'required|exists:referencias,id',
        'matricula' => 'required',
        'marcaVehiculo' => 'required',
        'modeloVehiculo' => 'required',
        'tarjetaTransporte' => 'file|mimedocimg',
        'tarjetaTransporteSession' => 'mimedocimgname',
        'polizaSeguro' => 'file|mimedocimg',
        'polizaSeguroSession' => 'mimedocimgname',
        'nombreAutonomo' => 'required',
        'nif' => 'required|nif-nie|unique:datos_facturaciones,nif',
        'direccion' => 'required',
        'cp' => 'required|postcode',
        'ciudad' => 'required',
        'iban' => 'iban',
        'reciboCuota' => 'file|mimedocimg',
        'reciboCuotaSession' => 'mimedocimgname',
    ];

    // Reglas de validación para empresas
    public $reglasEmpresa = [
        'nombre' => 'required|max:255',
        'email' => 'required|email|max:255|unique:profesionales,email,NULL,id,deleted_at,NULL',
        'telefono' => 'required|phone:ES,mobile|unique:configuraciones,telefono|unique:profesionales,telefono,NULL,id,deleted_at,NULL',
        'referencia' => 'required|exists:referencias,id',
        'matricula' => 'required',
        'marcaVehiculo' => 'required',
        'modeloVehiculo' => 'required',
        'tarjetaTransporte' => 'file|mimedocimg',
        'tarjetaTransporteSession' => 'mimedocimgname',
        'polizaSeguro' => 'file|mimedocimg',
        'polizaSeguroSession' => 'mimedocimgname',
        'razonSocial' => 'required',
        'cif' => 'required|cif|unique:datos_facturaciones,nif',
        'direccion' => 'required',
        'cp' => 'required|postcode',
        'ciudad' => 'required',
        'iban' => 'iban',
        'escrituraRegistro' => 'file|mimedocimg',
        'escrituraRegistroSession' => 'mimedocimgname',
    ];

    // Mensajes de validación para autónomos
    public $mensajesAutonomo = [
        'telefono.required' => 'El teléfono es obligatorio',
        'telefono.phone' => 'El teléfono no tiene un formato válido',
        'telefono.mobile' => 'El teléfono no tiene un formato válido',
        'telefono.unique' => 'El teléfono introducido ya está siendo usado',
        'matricula.required' => 'La matrícula es obligatoria',
        'marcaVehiculo.required' => 'La marca del vehículo es obligatoria',
        'modeloVehiculo.required' => 'El modelo del vehículo es obligatorio',
        'tarjetaTransporte.file' => 'La tarjeta de transporte debe ser un archivo válido',
        'tarjetaTransporte.mimedocimg' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'tarjetaTransporteSession.mimedocimgname' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'polizaSeguro.file' => 'La póliza del seguro debe ser un archivo válido',
        'polizaSeguro.mimedocimg' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'polizaSeguroSession.mimedocimgname' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'nombreAutonomo.required' => 'El nombre es obligatorio',
        'nif.nif_nie' => 'El NIF no tiene un formato válido',
        'direccion.required' => 'La dirección es obligatoria',
        'cp.required' => 'El código postal es obligatorio',
        'cp.postcode' => 'El código postal no tiene un formato válido',
        'iban.iban' => 'La cuenta bancaria no tiene un formato válido',
        'reciboCuota.file' => 'El recibo de pago de cuota debe ser un archivo válido',
        'reciboCuota.mimedocimg' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'reciboCuotaSession.mimedocimgname' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes'
    ];

    // Mensajes de validación para empresas
    public $mensajesEmpresa = [
        'telefono.required' => 'El teléfono es obligatorio',
        'telefono.phone' => 'El teléfono no tiene un formato válido',
        'telefono.mobile' => 'El teléfono no tiene un formato válido',
        'telefono.unique' => 'El teléfono introducido ya está siendo usado',
        'matricula.required' => 'La matrícula es obligatoria',
        'marcaVehiculo.required' => 'La marca del vehículo es obligatoria',
        'modeloVehiculo.required' => 'El modelo del vehículo es obligatorio',
        'tarjetaTransporte.file' => 'La tarjeta de transporte debe ser un archivo válido',
        'tarjetaTransporte.mimedocimg' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'tarjetaTransporteSession.mimedocimgname' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'polizaSeguro.file' => 'La póliza del seguro debe ser un archivo válido',
        'polizaSeguro.mimedocimg' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'polizaSeguroSession.mimedocimgname' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'razonSocial.required' => 'La razón social es obligatoria',
        'cif.cif' => 'El CIF no tiene un formato válido',
        'direccion.required' => 'La dirección es obligatoria',
        'cp.required' => 'El código postal es obligatorio',
        'cp.postcode' => 'El código postal no tiene un formato válido',
        'iban.iban' => 'La cuenta bancaria no tiene un formato válido',
        'escrituraRegistro.file' => 'El recibo de pago de cuota debe ser un archivo válido',
        'escrituraRegistro.mimedocimg' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes',
        'escrituraRegistroSession.mimedocimgname' => 'Tipo de archivo no válido. Puedes introducir documentos Word, PDF e imágenes'
    ];


    public function crearProfesional($tipoCuenta, Request $request) {

        // Profesional
        $profesional = new Profesional();
        $profesional->tipo_cuenta_id = $tipoCuenta;
        $profesional->nombre = $request->get('nombre');
        $profesional->email = $request->get('email');
        $profesional->telefono = $request->get('telefono');
        $profesional->referencia_id = $request->get('referencia');
        $profesional->iban = $request->get('iban') ? Crypt::encrypt(str_replace(' ', '', $request->get('iban'))) : null;

        // Vehículo
        $vehiculo = new Vehiculo();
        $vehiculo->matricula = $request->get('matricula');
        $vehiculo->marca = $request->get('marcaVehiculo');
        $vehiculo->modelo = $request->get('modeloVehiculo');
        $vehiculo->save();

        // Datos Facturación
        $datosFacturacion = new DatosFacturacion();
        if($tipoCuenta == TipoCuentaProfesional::AUTONOMO) {
            $datosFacturacion->razon_social = $request->get('nombreAutonomo');
            $datosFacturacion->nif = $request->get('nif');
        } else {
            $datosFacturacion->razon_social = $request->get('razonSocial');
            $datosFacturacion->nif = $request->get('cif');
        }
        $datosFacturacion->direccion = $request->get('direccion');
        $datosFacturacion->codigo_postal = $request->get('cp');
        $datosFacturacion->ciudad = $request->get('ciudad');
        $datosFacturacion->save();

        // Añadimos los datos de facturación y el vehículo al profesional
        $profesional->vehiculo_id = $vehiculo->id;
        $profesional->datos_facturacion_id = $datosFacturacion->id;

        $profesional->save();

        // Guardamos las imágenes
        $vehiculo->tarjeta_transporte_id = $this->checkImage($request, $profesional->id, ImagenDriver::TARJETA_TRANSPORTE);
        $vehiculo->seguro_mercancias_id = $this->checkImage($request, $profesional->id, ImagenDriver::SEGURO_MERCANCIAS);
        $vehiculo->save();
        if($tipoCuenta == TipoCuentaProfesional::AUTONOMO) {
            $datosFacturacion->recibo_escritura_id = $this->checkImage($request, $profesional->id, ImagenDriver::RECIBO_CUOTA);
        } else {
            $datosFacturacion->recibo_escritura_id = $this->checkImage($request, $profesional->id, ImagenDriver::ESCRITURA_EMPRESA);
        }
        $datosFacturacion->save();

    }

    private function checkImage(Request $request, $profesionalId,  $tipo) {

        if($tipo == ImagenDriver::TARJETA_TRANSPORTE) {
            $param = 'tarjetaTransporte';
        } else if($tipo == ImagenDriver::SEGURO_MERCANCIAS) {
            $param = 'polizaSeguro';
        } else if($tipo == ImagenDriver::RECIBO_CUOTA) {
            $param = 'reciboCuota';
        } else {
            $param = 'escrituraRegistro';
        }

        $file = $param . 'File';

        if(($request->has($param) && $request->get($param))) {

            $imagenDriver = new ImagenDriver();
            $imagenDriver->tipo_imagen_id = $tipo;
            $file = $request->file($param);
            if(substr($file->getMimeType(), 0, 5) == 'image') {
                $imagenDriver->path = $this->imageService->saveImagenProfesional($profesionalId, $file, $tipo);
            } else {
                $ext = explode('.', $file->getClientOriginalName())[count(explode('.', $file->getClientOriginalName())) - 1];
                $imagenDriver->path = $this->imageService->saveFileProfesional($profesionalId, $file, $tipo, $ext);
            }

            $imagenDriver->save();

            return $imagenDriver->id;

        } else if($request->session()->has($file)) {
            $name = $request->session()->get($param);
            $ext = explode('.', $name)[count(explode('.', $name)) - 1];
            $imagenDriver = new ImagenDriver();
            $imagenDriver->tipo_imagen_id = $tipo;
            if($ext === 'pdf' || $ext === 'docx' || $ext === 'doc' || $ext === 'odt') {
                $imagenDriver->path = $this->imageService->saveFileProfesional($profesionalId, $request->session()->get($file), $tipo, $ext);
            } else {
                $imagenDriver->path = $this->imageService->saveImagenProfesional($profesionalId, $request->session()->get($file), $tipo);
            }
            $imagenDriver->save();

            return $imagenDriver->id;

        }

    }

}
