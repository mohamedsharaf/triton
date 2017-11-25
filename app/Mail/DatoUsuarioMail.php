<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DatoUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $data1;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data1)
    {
        $this->data1 = $data1;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.dato_cuenta')
            ->with($this->data1)
            ->from('informatica@fiscalia.gob.bo', 'MINISTERIO PUBLICO - DATOS DE TU CUENTA')
            ->subject('DATOS DE TU CUENTA');
    }
}
