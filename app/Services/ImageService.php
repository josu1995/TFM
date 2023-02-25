<?php

namespace App\Services;

use Image;
use Carbon;
use File;

class ImageService {

    public function saveImagenProfesional($profesionalId, $image, $type) {

        $folder = hash('sha256', $profesionalId);

        $timestamp = str_replace([' ', ':', '-'], '', Carbon::now()->toDateTimeString());
        $nombre = str_random(7).'_'.$timestamp.'.jpg';
        $path = config('app.url').config('imagenes.profesional') . $folder . '/' . $type . '/' .$nombre;
        // Movemos imagen a directorio público
        $savedImage = Image::make($image)->encode('jpg', 50)->orientate();
        $savedImage->widen(500, function($constraint) {
            $constraint->upsize();
        });
        if(!File::exists(public_path().config('imagenes.profesional') . $folder)) {
            File::makeDirectory(public_path().config('imagenes.profesional') . $folder);
        }
        if(!File::exists(public_path().config('imagenes.profesional') . $folder . '/' . $type)) {
            File::makeDirectory(public_path().config('imagenes.profesional') . $folder . '/' . $type);
        }
        $savedImage->save(public_path().config('imagenes.profesional') . $folder . '/' . $type . '/' .$nombre);

        return $path;

    }

    public function saveFileProfesional($profesionalId, $file, $type, $ext) {

        $folder = hash('sha256', $profesionalId);

        $timestamp = str_replace([' ', ':', '-'], '', Carbon::now()->toDateTimeString());
        $nombre = str_random(7) . '_' . $timestamp . '.' . $ext;
        $path = config('app.url').config('imagenes.profesional') . $folder . '/' . $type . '/' .$nombre;
        if(!File::exists(public_path().config('imagenes.profesional') . $folder)) {
            File::makeDirectory(public_path().config('imagenes.profesional') . $folder);
        }
        if(!File::exists(public_path().config('imagenes.profesional') . $folder . '/' . $type)) {
            File::makeDirectory(public_path().config('imagenes.profesional') . $folder . '/' . $type);
        }
        if(is_string($file)) {
            $file = base64_decode($file);
            $newDoc = fopen(public_path().config('imagenes.profesional') . $folder . '/' . $type . '/' . $nombre, 'w');
            fwrite ($newDoc, $file);
            fclose($newDoc);
        } else {
            $file->move(public_path() . config('imagenes.profesional') . $folder . '/' . $type, $nombre);
        }

        return $path;

    }

}
