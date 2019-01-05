<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <h1>Spotify - accept auth</h1>
            <h2>Params</h2>
            <p>{{ $params }}</p>
            <h2>Code</h2>
            <p>{{ $code }}</p>
            <h2>Auth response</h2>
            <p>{{{ $response }}}</p>
            <h2>Me response</h2>
            <p>{{{ $response2 }}}</p>
            <h2>User</h2>
            <p>{{{ $user }}}</p>
            <p><a href="/spotify/connect/">Connect again</a></p>
        </div>
    </body>
</html>
