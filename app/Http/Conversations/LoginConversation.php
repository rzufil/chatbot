<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Facades\Auth;

class LoginConversation extends Conversation
{
    public function askEmail()
    {
        $this->ask('Enter your email:', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'email' => $answer->getText(),
            ]);

            $this->askPassword();
        });
    }

    public function askPassword()
    {
        $this->ask('Enter your password:', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'password' => $answer->getText(),
            ]);

            $user = $this->bot->userStorage()->find();
            $user_email = $user->get('email');
            $user_password = $user->get('password');

            if (Auth::attempt(['email' => $user_email, 'password' => $user_password])) {
                $this->say('You are now logged in.');
            }
            else {
                $this->say('Wrong credentials! Please log in again.');
                $this->askEmail();
            }

            $this->bot->startConversation(new SelectServiceConversation());
        });
    }

    public function run()
    {
        $this->askEmail();
    }
}