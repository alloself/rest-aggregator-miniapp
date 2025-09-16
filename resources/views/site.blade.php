<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @csrf
    <title>Eat.Drink.Repeat</title>
    <script src="https://telegram.org/js/telegram-web-app.js?59"></script>
    @vite(['resources/site/css/app.css', 'resources/site/ts/app/main.ts'])
    @if (!empty($yandex_metrica_code))
    <script>
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            k=e.createElement(t),a=e.getElementsByTagName(t)[0];
            k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js', 'ym');

        ym({!! json_encode($yandex_metrica_code) !!}, 'init', {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/{{ $yandex_metrica_code }}" style="position:absolute; left:-9999px;" alt="" /></div>
    </noscript>
    @endif
</head>

<body class="antialiased">
    <div id="site-app">

    </div>
</body>

</html>
