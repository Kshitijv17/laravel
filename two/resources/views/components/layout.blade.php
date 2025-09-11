<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <nav>
        <!-- <a href="/">home</a>
        <a href="/about">about</a>
        <a href="/contact">contact</a> -->
        <x-nav-link href="/">Home</x-nav-link>
        <x-nav-link  href="/about" style="color: green">about</x-nav-link>
        <x-nav-link href="/contact">contact</x-nav-link>                
    </nav>

    <?php
    //  echo $slot 
    ?>
    {{ $slot }}
</body>
</html>