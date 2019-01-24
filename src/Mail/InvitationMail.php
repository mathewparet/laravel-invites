<?php

namespace mathewparet\LaravelInvites\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\URL;

use mathewparet\LaravelInvites\Models\Invite;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    private $invite;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'email' => $this->invite->email,
            'code' => (string) $this->invite->code
        ];

        if($this->invite->valid_upto)
        {
            $url = URL::temporarySignedRoute('laravelinvites.routes.follow', $this->invite->valid_upto, $data);
        }
        else
        {
            $url = URL::signedRoute('laravelinvites.routes.follow', $data);
        }

        return $this->markdown('laravelinvites::Mail/InvitationMailMarkdown')
            ->with(['invite' => $this->invite, 'url'=>$url])
            ->subject(__(config('laravelinvites.mail.subject', ['app' => config('app.name')])));
    }
}
