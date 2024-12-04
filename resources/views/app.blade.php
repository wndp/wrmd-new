<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Preconnect -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap" media="print" onload="this.onload=null;this.removeAttribute('media');" rel="stylesheet">
        <script>window.i18n = []</script>

        <!-- Synchronous JS -->
        @routes
        <script src="{{ asset('lang/'.app()->getLocale().'.js') }}" defer></script>
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <!-- Everything else -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" type='application/manifest+json' href="{{ asset('webmanifest.json') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="@WildlifeRehabMD" />
        <meta name="twitter:title" content="Wildlife Rehabilitation MD (WRMD)" />
        <meta name="twitter:description" content="A free on-line medical database designed specifically for wildlife rehabilitators to collect, manage and analyze data for our patients." />
        <meta name="twitter:image" content="{{ asset('og-image.png') }}" />
        <meta name="twitter:creator" content="@WildlifeRehabMD" />
        <meta property="og:url" content="https://www.wrmd.org/" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Wildlife Rehabilitation MD (WRMD)" />
        <meta property="og:description" content="A free on-line medical database designed specifically for wildlife rehabilitators to collect, manage and analyze data for our patients." />
        <meta property="og:image" content="{{ asset('og-image.png') }}" />
        <meta property="description" content="A free on-line medical database designed specifically for wildlife rehabilitators to collect, manage and analyze data for our patients." />
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <noscript>You must enable JavaScript to use Wildlife Rehabilitation MD (WRMD).</noscript>
        @inertia
    </body>
    @production
        <script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});
        </script>
        <script type="text/javascript">
          window.Beacon('init', 'a9e2b564-4328-4fa2-871e-ab79a1dc43d8')
        </script>
    @endproduction
</html>
