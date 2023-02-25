<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EtiquetaDualCarrier implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $envio;
    protected $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($envio, $path)
    {
        $this->envio = $envio;
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        \Log::info('Se ejecuta job');

        $ch = curl_init($this->path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);

        file_put_contents(storage_path() . '/app/docs/tmp/' . $this->envio->codigo . '.pdf', $data);

        //TODO: Descomentar en produccion
//        chown(storage_path() . '/app/docs/tmp/' . $this->envio->codigo . '.pdf', 'ubuntu');

//        chgrp(storage_path() . '/app/docs/tmp/' . $this->envio->codigo . '.pdf', 'ubuntu');

        $tempImage = $this->envio->codigo . '.jpg';

        $im = new \Imagick();

        $im->setResolution(377,567);
        $im->readimage(storage_path() . '/app/docs/tmp/' . $this->envio->codigo . '.pdf');
        $im->setImageFormat('jpeg');
        $im->setCompressionQuality(100);
        $im->writeImage(storage_path() . '/app/docs/devoluciones/etiquetas/' . $tempImage);
        $im->clear();
        $im->destroy();

        \Storage::disk('local')->delete('docs/tmp/' . $this->envio->codigo . '.pdf');

        $this->envio->etiqueta_dual_carrier = $this->path;

        $this->envio->save();

    }
}
