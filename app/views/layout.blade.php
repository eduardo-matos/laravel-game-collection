<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Games Collection</title>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-default">
            <div class="navbar-header">
                <a href="{{ action('GamesController@index') }}" class="navbar-brand">Games Collection</a>
            </div>
        </nav>

        @yield('content')
    </div>
</body>
</html>
