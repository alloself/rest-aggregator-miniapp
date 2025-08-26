<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @csrf
    <title>Eat.Drink.Repeat</title>
    @vite(['resources/site/css/app.css', 'resources/site/ts/app/main.ts'])
</head>

<body class="antialiased">
    <div id="site-app">

    </div>
</body>

</html>
