<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Models\{User, Account, Log};
use Illuminate\Support\Facades\Config;
use App\Helpers\CurrencyConverstionHelper;

class MoneyTransactionsExchangeConversation extends Conversation
{
    public function askService()
    {
        $question = Question::create('Select the transaction type:')
            ->callbackId('select_service')
            ->addButtons([
                Button::create('Deposit')->value('deposit'),
                Button::create('Withdraw')->value('withdraw'),
                Button::create('Show Balance')->value('show_balance'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->bot->userStorage()->save([
                    'transaction_type' => $answer->getValue(),
                ]);

                $user = $this->bot->userStorage()->find();
                $user_transaction_type = $user->get('transaction_type');
                $user_email = $user->get('email');
                $user_model = User::where('email', $user_email)->first();
                $this->bot->userStorage()->save([
                    'account_id' => Account::where('user_id', $user_model->id)->first()->id,
                ]);
                $this->{$user_transaction_type}();
            }
        });
    }

    public function deposit()
    {
        $this->ask('Enter the amount to deposit:', function(Answer $answer) {
            if (!is_numeric($answer->getText())) {
                $this->say('Enter a valid amount.');
                $this->deposit();
            }
            else {
                $this->bot->userStorage()->save([
                    'deposit_amount' => $answer->getText(),
                ]);
                $this->deposit_amount();
            }
        });
    }

    public function deposit_amount()
    {
        $this->ask('Enter the currency code:', function(Answer $answer) {
            if (!in_array($answer->getText(), Config::get('currency_list'))) {
                $this->say('Enter a valid code.');
                $this->deposit_amount();
            }
            else {
                $user = $this->bot->userStorage()->find();
                $user_account_id = $user->get('account_id');
                $user_account = Account::find($user_account_id);
                $user_deposit_amount = $user->get('deposit_amount');
                $default_currency = $user_account->default_currency;
                $old_balance = $user_account->balance;
                if ($default_currency != $answer->getText()) {
                    $from = $answer->getText();
                    $to = $default_currency;
                    $result = CurrencyConverstionHelper::exchange($from, $to, $user_deposit_amount);
                    if ($result['error']) {
                        $this->say($result['error_message']);
                    }
                    else {
                        $user_account->balance += $result['response'];
                    }
                }
                else {
                    $user_account->balance += $user_deposit_amount;
                }
                $user_account->save();
                $user_email = $user->get('email');
                $user_model = User::where('email', $user_email)->first();
                $log = new Log();
                $log->saveLog(
                    $user_model->id,
                    'Deposit',
                    $answer->getText(),
                    $default_currency,
                    $old_balance,
                    $user_account->balance,
                );
                $this->say('Deposit complete.<br>New balance: ' . $user_account->balance);
                $this->backToService();
            }
        });
    }

    public function withdraw()
    {
        $this->ask('Enter the amount to withdraw:', function(Answer $answer) {
            if (!is_numeric($answer->getText())) {
                $this->say('Enter a valid amount.');
                $this->deposit();
            }
            else {
                $this->bot->userStorage()->save([
                    'withdraw_amount' => $answer->getText(),
                ]);
                $this->withdraw_amount();
            }
        });
    }

    public function withdraw_amount()
    {
        $this->ask('Enter the currency code:', function(Answer $answer) {
            if (!in_array($answer->getText(), Config::get('currency_list'))) {
                $this->say('Enter a valid code.');
                $this->withdraw_amount();
            }
            else {
                $user = $this->bot->userStorage()->find();
                $user_account_id = $user->get('account_id');
                $user_account = Account::find($user_account_id);
                $user_withdraw_amount = $user->get('withdraw_amount');
                $default_currency = $user_account->default_currency;
                $old_balance = $user_account->balance;
                if ($default_currency != $answer->getText()) {
                    $from = $answer->getText();
                    $to = $default_currency;
                    $result = CurrencyConverstionHelper::exchange($from, $to, $user_withdraw_amount);
                    if ($result['error']) {
                        $this->say($result['error_message']);
                    }
                    else {
                        $user_account->balance -= $result['response'];
                    }
                }
                else {
                    $user_account->balance -= $user_withdraw_amount;
                }
                if ($user_account->balance < 0) {
                    $this->say('Withdraw failed. Insufficient balance.');
                }
                else {
                    $user_email = $user->get('email');
                    $user_model = User::where('email', $user_email)->first();
                    $log = new Log();
                    $log->saveLog(
                        $user_model->id,
                        'Deposit',
                        $answer->getText(),
                        $default_currency,
                        $old_balance,
                        $user_account->balance,
                    );
                    $user_account->save();
                    $this->say('Withdraw complete.<br>New balance: ' . $user_account->balance);
                }
                $this->backToService();
            }
        });
    }

    public function show_balance()
    {
        $user = $this->bot->userStorage()->find();
        $user_account_id = $user->get('account_id');
        $user_account = Account::find($user_account_id);
        $this->say('Balance: ' . $user_account->balance . ' ' . $user_account->default_currency);
        $this->backToService();
    }

    private function backToService()
    {
        $this->bot->userStorage()->save([
            'service' => false,
        ]);
        $this->bot->startConversation(new StartServiceConversation());
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askService();
    }
}