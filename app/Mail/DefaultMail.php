<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DefaultMail extends Mailable
{
    use Queueable, SerializesModels;



    private $fromName = '';
    private $toName = '';
    private $contents = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contents, $subject = '', $fromaddress = 'jbr@consiglia.dk', $fromName = 'testFromName', $toName = '', $attachements = [])
    {
        //$this->from($fromaddress);
        $this->subject($subject);
        $this->fromName = $fromName;
        $this->toName = $toName;
        $this->contents = $contents;
        foreach($attachements as $attachement) $this->attach($attachement);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email/default', ['toName' => $this->toName, 'fromName' => $this->fromName, 'contents' => $this->contents]);
    }
}
