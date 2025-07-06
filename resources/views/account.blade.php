<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @csrf
    <title>Личный кабинет ресторана | Eat.Drink.Repeat</title>

    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/account/css/app.css', 'resources/account/ts/main.ts'])
</head>

<body class="antialiased">
    <div id="account-app">

    </div>
</body>

</html>
