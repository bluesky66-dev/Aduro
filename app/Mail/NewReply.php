<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     BluCrew
 */
 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewReply extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $topic;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\User $user, \App\Topic $topic)
    {
        $this->user = $user;
        $this->topic = $topic;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.new_reply')
            ->from(config('other.email'), config('other.title'))
            ->subject('The topic ' . $this->topic->name . ' has a new reply');

    }
}
