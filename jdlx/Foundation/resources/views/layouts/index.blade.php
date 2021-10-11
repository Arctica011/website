<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="theme-color" content="#000000"/>
    <meta
        name="description"
        content=""
    />
    <!--
      manifest.json provides metadata used when your web app is added to the
      homescreen on Android. See https://developers.google.com/web/fundamentals/engage-and-retain/web-app-manifest/
    -->
    <link rel="manifest" href="{{ config('app.url') }}/manifest.json"/>
    <link rel="shortcut icon" href="{{ config('app.url') }}/favicon.ico"/>
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="{{ config('app.url') }}/apple-touch-icon.png"
    />

    <!--
      Roboto is the default font for Material-UI. Baloo+Bhaijaan is only used for the logo
    -->
    <link
        href="https://fonts.googleapis.com/css?family=Baloo+Bhaijaan|Roboto:300,400,500&display=swap"
        rel="stylesheet"
    />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <title>
        {{ config('app.name') }}
    </title>
    @yield('headscript')
</head>
<body>
<noscript>
    You need to enable JavaScript to run this app.
</noscript>
<div id="root"></div>


@yield('postscript')

</body>
</html>
