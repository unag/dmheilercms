<?php
/*******************************************************
*Datei: logged_in_check.inc.php                        *
*Verantwortlicher Programmierer: Uwe Nagel             *
*******************************************************/


/*
 * Diese Funktion überprüft ob überhaupt jemand eingelogged ist und wenn ja,
 * ob die aus der Datenbank entnommenen User angemeldet sind und wer sie sind.
 * Ich benötige sie für den Abgleich mit den Erstellern der Schwarzen-Brett-
 * Einträge, da ich dort einen Link einfüge falls Übereinstimmung vorhanden.
 * Uwe
 *
 */
function logged_in_check($is_on_page)
{
    if(!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id']))
    {
        return ' ';
    }
    if($_SESSION['logged_in'])
    {
        if($_SESSION['user_id'] == $is_on_page)
        {
            $checked = '<tr><td><a href="blackboard_input.php">editieren</a></td></tr>';       
        }
        else
        {
            $checked = ' ';
        }
        
    }
    else 
    {
        $checked = ' ';
    }
    return $checked;
}
?>
