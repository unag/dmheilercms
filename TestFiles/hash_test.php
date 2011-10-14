<form action="" method="post">
    <input type="text" name="p" />
    <input type="submit" name="submit" value="Hash berechnen" />
</form>
<?php
    if (isset($_POST['submit']))
    {
        $hash = hash('ripemd160', $_POST['p']);
        echo '<pre>';
        echo 'Eingabe: ' . $_POST['p'] . '<br />';
        echo 'Berechneter Hash-Wert (ripemd160):<br />';
        echo $hash . '<br />';
        echo 'Typ: ' . gettype($hash);
        echo '</pre>';
    }
?>
