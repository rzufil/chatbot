<?php

use App\Http\Controllers\BotManController;
use BotMan\BotMan\BotMan;

$botman = resolve('botman');

$botman->hears('login', BotManController::class.'@loginConversation');
$botman->hears('signup', BotManController::class.'@signupConversation');
$botman->hears('exit', function (BotMan $bot) {
    $bot->userStorage()->delete();
});