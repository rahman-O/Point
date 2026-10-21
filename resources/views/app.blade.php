<!DOCTYPE>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @isset($meta)
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta property="og:site_name" content="مؤتمر بوينت العراق | Point Iraq Conference">
    <meta property="og:type" content="{{ $meta['type'] }}">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:url" content="{{ $meta['url'] }}">
    <meta property="og:image" content="{{ $meta['image'] }}">
    @if (!empty($meta['image_width']) && !empty($meta['image_height']))
    <meta property="og:image:width" content="{{ $meta['image_width'] }}">
    <meta property="og:image:height" content="{{ $meta['image_height'] }}">
    @endif
    <meta property="og:image:alt" content="{{ $meta['title'] }}">
    <meta property="og:locale" content="ar_IQ">
    <meta property="og:locale:alternate" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta['title'] }}">
    <meta name="twitter:description" content="{{ $meta['description'] }}">
    <meta name="twitter:image" content="{{ $meta['image'] }}">
    @endisset
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
