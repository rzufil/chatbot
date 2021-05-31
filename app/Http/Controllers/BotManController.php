<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Http\Conversations\LoginConversation;
use App\Http\Conversations\SignupConversation;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');
        $botman->listen();
    }

    public function loginConversation(BotMan $bot)
    {
        $bot->startConversation(new LoginConversation());
    }

    public function signupConversation(BotMan $bot)
    {
        $bot->startConversation(new SignupConversation());
    }
}