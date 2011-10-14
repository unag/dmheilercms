<?php
    session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
    <head>
        <title>session_mysql_test</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css">
        </style>

    </head>
    <body>
        <?php
        echo 'Session-Id: ' .  session_id( ) . '<br />';
        echo 'Session-Name: ' . session_name() . '<br />';
        echo 'SID: ' . SID . '<br />';
        echo '<hr />';
        
        // MySQL verbinden
        $sqlHandle = @mysql_connect('localhost:3306', 'root')
                or die('Datenbankzugriff gescheitert!');
        echo 'Mit Datenbankserver verbunden.';
        echo '<hr />';
        
        // Mit Nordwind verbinden
        $query = 'USE nordwind;';
        mysql_query($query)
            or die('Datenbank nicht gefunden!');
        echo 'Datenbank gefunden.';
        echo '<hr />';
        
        // $query = 'SELECT `Kategorie-Nr`,Beschreibung FROM kategorien';
        $query = 'SELECT Beschreibung FROM kategorien';
        $result = mysql_query($query);
        if ($result) {
            //echo '<pre>';
            while($zeile = mysql_fetch_array($result, MYSQL_ASSOC)) {
                // var_dump($zeile);
                // print_r($zeile);
                // echo $zeile['Kategorie-Nr'].' '.$zeile['Beschreibung'].'<br />';
                echo $zeile['Beschreibung'].'<br />';
                // echo '<br />';
            }
            //echo '</pre>';
        }
        else {
            echo 'Query fehlgeschlagen!<br />Fehler: ' .  mysql_error();
        }
        
        echo '<hr />';
        
        // Beispiel aus dem Unterricht
        mysql_select_db('verkauf', $sqlHandle)
              or die('Datenbank verkauf nicht gefunden');
        $query = 'SELECT id, artikelnummer, artikelname FROM artikel ORDER BY artikelnummer;';
        $result = mysql_query($query, $sqlHandle);
        if ($result) {
            echo '<table border="1">';
            echo "\t<tr>\r\n";
            echo "\t\t<th>Id</th>\r\n";
            echo "\t\t<th>Artikelnummer</th>\r\n";
            echo "\t\t<th>Artikelname</th>\r\n";
            echo "\t<tr>\r\n";
            while($zeile = mysql_fetch_array($result, MYSQLI_ASSOC)) {
                echo "\t<tr>\r\n";
                echo "\t\t<td>".$zeile['id']."</td>\r\n";
                echo "\t\t<td>".$zeile['artikelnummer']."</td>\r\n";
                echo "\t\t<td>".$zeile['artikelname']."</td>\r\n";
                echo "\t<tr>\r\n";
            }
            echo '</table>';
        }
        else {
            echo 'Query an verkauf fehlgeschlagen!<br />Fehler: ' .  mysql_error();
        }
        echo '<hr />';
        
        /*
        $query = "INSERT INTO artikel (artikelnummer, artikelname) VALUES (\"pen02a\",\"Pelikan Fuellfederhalter\");";
        $result = mysql_query($query, $sqlHandle);
        if ($result) {
            echo 'Eintrag wurde in Datenbank eingefuegt.<br />';
            echo 'Query: ' . $query;
        }
        else {
            echo 'Eintrag wurde nicht in Datenbank eingefuegt.<br />';
            echo mysql_error();
        }
        echo '<hr />';
         */
        
        // MySQL trennen
        mysql_close($sqlHandle);
        echo 'Von Datenbankserver getrennt.';
        echo '<hr />';
        
        // Session zerstoeren
        $_SESSION = array();
        session_destroy();
        echo 'Session zerstört.<br />';
        echo 'Session-Name: ' .session_name() .'<br />';
        echo 'Session-Id: ' .session_id() .'<br />';
        ?>
    </body>
</html>