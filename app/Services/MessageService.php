<?php

namespace App\Services;

use Session;

class MessageService
{

    public function flashMessage($message)
    {
        Session::flash('message', $message);
    }
}
