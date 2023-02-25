<?php

namespace App\Repositories;

class BusinessRepository
{

    // Reglas de validación para datos de comercio
    public $reglasComercio = [
        'nombre_comercial' => 'required|max:255',
        'tipo_negocio' => 'required|numeric|exists:business_registro_tipo_negocio,id',
        'tienda_online' => 'required|numeric|exists:business_registro_tienda_online,id',
        'web' => 'sometimes|nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/|max:255',
        'marketplaces' => 'required|numeric|exists:business_registro_marketplaces,id',
        'envios_mensuales' => 'required|numeric|exists:business_registro_envios_mensuales,id'
    ];

    // Reglas de validación para datos de contacto
    public $reglasContacto = [
        'nombre' => 'required|max:255',
        'apellido' => 'required|max:255',
        'email' => 'required|email|max:255|unique:solicitudes_business,email',
        'telefono' => 'required|max:255|phone:ES|unique:solicitudes_business,telefono',
        'ciudad' => 'required|max:255',
        'codigo_postal' => 'required|max:5|postcode',
    ];

    public $mensajesComercio = [
        'tipo_negocio.*' => 'El tipo de negocio es requerido',
        'tienda_online.*' => 'El campo tienda online es requerido',
        'web.regex' => 'La web no tiene un formato correcto',
        'marketplaces.*' => 'El campo marketplaces es requerido',
        'envios_mensuales.*' => 'El campo envíos mensuales es requerido'
    ];

    public $mensajesContacto = [
        'email.email' => 'El email no tiene un formato correcto',
        'telefono.required' => 'El teléfono es requerido',
        'telefono.max' => 'El teléfono no puede tener más de 255 caracteres',
        'telefono.phone' => 'El teléfono no tiene un formato correcto',
        'telefono.unique' => 'Este teléfono ya está registrado en el sistema',
        'codigo_postal.required' => 'El código postal es requerido',
        'codigo_postal.max' => 'El código postal no puede tener más de 5 caracteres',
        'codigo_postal.postcode' => 'El código postal no tiene un formato correcto'
    ];

    public $reglasDatosFacturacion = [
        'nombre_comercial' => 'required|max:255',
        'direccion' => 'required|max:500',
        'codigo_postal' => 'required|max:5|postcode',
        'razon_social' => 'required|max:255',
        'nif' => 'required|max:9|nifNieCif',
        'ciudad' => 'required|max:255',
    ];

    public $mensajesDatosFacturacion = [
        'nombre_comercial.required' => 'El campo nombre comercial es requerido',
        'nombre_comercial.max' => 'El nombre comercial no puede tener más de 255 caracteres',
        'direccion.required' => 'El campo dirección es requerido',
        'direccion.max' => 'La dirección no puede tener más de 255 caracteres',
        'codigo_postal.required' => 'El código postal es requerido',
        'codigo_postal.max' => 'El código postal no puede tener más de 5 caracteres',
        'codigo_postal.postcode' => 'El código postal no tiene un formato correcto',
        'razon_social.required' => 'La razón social es requerida',
        'razon_social.max' => 'La razón social no puede tener más de 255 caracteres',
        'nif.nifNieCif' => 'El nif no tiene un formato correcto'
    ];

    public $reglasAltaBack = [
        'nombre' => 'required|max:255',
        'apellido' => 'required|max:255',
        'email' => 'required|email|max:255|unique:usuarios,email',
        'telefono' => 'required|max:255|phone:ES|unique:configuraciones,telefono',
        'tarifa' => 'required|numeric',
        'tipo_recogida' => 'required|numeric|exists:tipos_recogida_business,id',
        'razon_social' => 'required|max:255',
        'direccion' => 'required|max:500',
        'nif' => 'required|max:9|nifNieCif',
        'codigo_postal' => 'required|max:5|postcode',
        'ciudad' => 'required|max:255',
        'nombre_comercial' => 'required|max:255',
        'web' => 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/|max:255',
        'tipo_negocio' => 'required|numeric|exists:business_registro_tipo_negocio,id',
        'tienda_online' => 'required|numeric|exists:business_registro_tienda_online,id',
        'marketplaces' => 'required|numeric|exists:business_registro_marketplaces,id',
    ];

    public $mensajesAltaBack = [
        'email.email' => 'El email no tiene un formato correcto',
        'direccion.required' => 'El campo dirección es requerido',
        'direccion.max' => 'La dirección no puede tener más de 255 caracteres',
        'nif.nifNieCif' => 'El nif no tiene un formato correcto',
        'telefono.required' => 'El teléfono es requerido',
        'telefono.max' => 'El teléfono no puede tener más de 255 caracteres',
        'telefono.phone' => 'El teléfono no tiene un formato correcto',
        'telefono.unique' => 'Este teléfono ya está registrado en el sistema',
        'tipo_recogida.required' => 'El tipo de recogida es obligatorio',
        'tipo_recogida.*' => 'El formato del tipo de recogida no es correcto',
        'razon_social.required' => 'La razón social es requerida',
        'razon_social.max' => 'La razón social no puede tener más de 255 caracteres',
        'codigo_postal.required' => 'El código postal es requerido',
        'codigo_postal.max' => 'El código postal no puede tener más de 5 caracteres',
        'codigo_postal.postcode' => 'El código postal no tiene un formato correcto',
        'nombre_comercial.required' => 'El campo nombre comercial es requerido',
        'nombre_comercial.max' => 'El nombre comercial no puede tener más de 255 caracteres',
        'tipo_negocio.*' => 'El tipo de negocio es requerido',
        'tienda_online.*' => 'El campo tienda online es requerido',
        'web.regex' => 'La web no tiene un formato correcto',
        'marketplaces.*' => 'El campo marketplaces es requerido'
    ];


}
