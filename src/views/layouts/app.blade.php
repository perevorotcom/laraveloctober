@spaceless
    <!doctype html>
    <html lang="{{ Localization::getCurrentLocale() }}">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
            <meta name="csrf-token" content="%%csrf_token%%">
            <link href="{{ mix('css/app.css') }}" rel="stylesheet">
            {!! SEO::head() !!}
        </head>
        <body>
            {!! SEO::bodyTop() !!}

            @yield('content')

            {!! SEO::bodyBottom() !!}

            <script src="{{ mix('js/app.js') }}"></script>

            @yield('js')
        </body>
    </html>
@endspaceless
