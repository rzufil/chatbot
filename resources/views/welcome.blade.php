<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Chatbot</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body>
        <div class="container mt-5">
            <h1>
                Chatbot
            </h1>
            <p>You can click on the envelope icon at the bottom right corner to chat with our bot</p>
            <h2>Available Services</h2>
            <ul>
                <li>Currency Exchange*</li>
                <li>Set Default Currency</li>
                <li>Money Transactions</li>
            </ul>
            <p>* Currency list can be found <a href="https://www.amdoren.com/currency-list/" target="_blank">here</a>.</p>
        </div>
    </body>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/assets/css/chat.min.css">
    <script>
        var botmanWidget = {
            aboutText: 'chatbot',
            introMessage: "✋ Hi! Please type \"login\" or \"signup\" to start."
        };
    </script>
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
</html>