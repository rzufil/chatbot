<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Facades\{Auth, Hash};
use App\Models\{User, Account};

class SignupConversation extends Conversation
{
    public function askName()
    {
        $this->ask('Enter a name:', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'name' => $answer->getText(),
            ]);

            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask('Enter an email:', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'email' => $answer->getText(),
            ]);

            $this->askPassword();
        });
    }

    public function askPassword()
    {
        $this->ask('Enter a password:', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'password' => $answer->getText(),
            ]);

            $user = $this->bot->userStorage()->find();
            $user_name = $user->get('email');
            $user_email = $user->get('email');
            $user_password = $user->get('password');

            $new_user = new User();
            $new_user->password = Hash::make($user_password);
            $new_user->email = $user_email;
            $new_user->name = $user_name;
            $new_user->save();

            $new_account = new Account();
            $new_account->user_id = $new_user->id;
            $new_account->default_currency = 'USD';
            $new_account->balance = 0;
            $new_account->save();

            if (!$new_user) {
                $this->say('Something went wrong, please try again.');
                return;
            }

            Auth::login($new_user);
            $this->say('User has been created successfully. You are now logged in.');

            $this->bot->startConversation(new SelectServiceConversation());
        });
    }

    public function run()
    {
        $this->askName();
    }
}