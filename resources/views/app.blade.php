<!DOCTYPE>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @viteReactRefresh
    @vite([
    'resources/js/app.jsx'
    ])
</head>

<body>
<div id="root"></div>
</body>

</html>
