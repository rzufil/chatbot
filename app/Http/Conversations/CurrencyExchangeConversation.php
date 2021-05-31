<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Helpers\CurrencyConverstionHelper;
use App\Models\{User, Log};

class CurrencyExchangeConversation extends Conversation
{
    public function askFrom()
    {
        $this->ask('Enter from code:', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'from' => $answer->getText(),
            ]);
            $this->askTo();
        });
    }

    public function askTo()
    {
        $this->ask('Enter to code:', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'to' => $answer->getText(),
            ]);
            $this->askAmount();
        });
    }

    public function askAmount()
    {
        $this->ask('Enter amount:', function(Answer $answer) {
            if (!is_numeric($answer->getText())) {
                if ($answer->getText() == 'exit') {
                    $this->bot->startConversation(new SelectServiceConversation());
                }
                $this->say('Please enter a numeric value.');
                $this->askAmount();
            }
            $this->bot->userStorage()->save([
                'amount' => $answer->getText(),
            ]);
            $user = $this->bot->userStorage()->find();
            $from = $user->get('from');
            $to = $user->get('to');
            $amount = $user->get('amount');
            $result = CurrencyConverstionHelper::exchange($from, $to, $amount);
            if ($result['error']) {
                $this->say($result['error_message']);
            }
            else {
                $this->say($amount . ' ' . $from . ' = ' . $result['response'] . ' ' . $to);
            }

            $user_email = $user->get('email');
            $user_model = User::where('email', $user_email)->first();
            $log = new Log();
            $log->saveLog(
                $user_model->id,
                'Currency Exchange',
                $from,
                $to,
                $amount,
                $result['response']
            );
            $this->bot->startConversation(new SelectServiceConversation());
        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askFrom();
    }
}