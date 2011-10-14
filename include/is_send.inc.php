<?php

 /***********************************************************************
 *Datei: is_send.inc.php                                                *
 *Verantwortlicher Programmierer: Uwe Nagel                             *
 *                                                                      *
 *Eine Funktion um das Absenden der Inserate zu prüfen.                 *
 *Uwe                                                                   *         
 *                                                                      *
 *                                                                      * 
 ***********************************************************************/

function is_send($send) 
{
    if(isset($send)) 
    {
        return $send;
    }
    return  ".'bitte Text eingeben'.";
}

?>
