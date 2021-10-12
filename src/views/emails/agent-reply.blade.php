<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>{{ config("ticket.app_name") }}</title>
    <style>
        *{
            padding: 0;
            margin: 0;
        }
        body{
            width: 100%;
    }
    </style>
</head>
<body>
    <div style="width: 90% margin: auto; padding: 20px;">
        
        {!! $content !!}

        <p>
            @if (auth()->user()->ticket_sub_admin && auth()->user()->ticket_super_admin)
                From <br>
                {{ $agent }}
                <br>
                {{ config("ticket.app_name") }}
            @else
            From <br>
            {{ auth()->user()->name }}
            
            @endif
           

        </p>
    </div>
</body>
</html>