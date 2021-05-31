<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class StartServiceConversation extends Conversation
{
    public function startService()
    {
        $user = $this->bot->userStorage()->find();
        $service = $user->get('service');
        switch ($service) {
            case 'currency_exchange':
                $this->say('You selected "Currency Exchange".');
                $this->bot->startConversation(new CurrencyExchangeConversation());
                break;
            case 'set_default_currency':
                $this->say('You selected "Set Default Currency".');
                $this->bot->startConversation(new SetDefaultCurrencyConversation());
                break;
            case 'money_transactions':
                $this->say('You selected "Money Transactions".');
                $this->bot->startConversation(new MoneyTransactionsExchangeConversation());
                break;
            case false:
                $this->bot->startConversation(new SelectServiceConversation());
                break;
        }
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->startService();
    }
}