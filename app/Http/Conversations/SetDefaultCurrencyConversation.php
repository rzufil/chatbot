<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Models\{User, Account};
use Illuminate\Support\Facades\Config;
use App\Helpers\CurrencyConverstionHelper;

class SetDefaultCurrencyConversation extends Conversation
{
    public function setDefaultCurrency()
    {
        $this->ask('Enter a currency code:', function(Answer $answer) {
            if (!in_array($answer->getText(), Config::get('currency_list'))) {
                $this->say('Enter a valid code.');
                $this->setDefaultCurrency();
            }
            else {
                $user = $this->bot->userStorage()->find();
                $user_email = $user->get('email');
                $user_model = User::where('email', $user_email)->first();
                $user_account = Account::where('user_id', $user_model->id)->first();

                $from = $user_account->default_currency;
                $to = $answer->getText();
                $result = CurrencyConverstionHelper::exchange($from, $to, $user_account->balance);
                if ($result['error']) {
                    $this->say($result['error_message']);
                }
                else {
                    $user_account->balance = $result['response'];
                }

                $user_account->default_currency = $answer->getText();
                $user_account->save();
                $this->say('Default currency updated.');
                $this->bot->userStorage()->save([
                    'service' => false,
                ]);
                $this->bot->startConversation(new StartServiceConversation());
            }
        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->setDefaultCurrency();
    }
}