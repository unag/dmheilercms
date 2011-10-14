<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $timestamp = time();
        $ausgabe = date('d.m.y H:i', $timestamp);
        echo $ausgabe;
        ?>
    </body>
</html>
