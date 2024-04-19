<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Auto Stanić</title>

    @vite('public/layout/styles/preloading/preloading.css')
    @vite('public/layout/styles/theme/theme-light/teal/theme.css')
</head>

<body>
    <div id="app"></div>

    @vite('src/main.js')
</body>

</html>