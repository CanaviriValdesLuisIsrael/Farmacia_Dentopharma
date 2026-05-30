<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaEmpleado extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombreEmpleado;
    public string $emailEmpleado;
    public string $passwordTemporal;

    public function __construct(string $nombre, string $email, string $password)
    {
        $this->nombreEmpleado   = $nombre;
        $this->emailEmpleado    = $email;
        $this->passwordTemporal = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Bienvenido a Farmacia Dentopharma');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.bienvenida-empleado');
    }
}