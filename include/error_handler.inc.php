<?php
/*******************************************************************************
 * Datei: error_handler.inc.php
 * Verantwortlicher Programmierer: Benjamin Stenzel
 ******************************************************************************/

/**
 * Die generische Fehlermeldung.
 */
define('GENERIC_ERROR', 'Service derzeit nicht verf&uuml;gbar. Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt erneut.');

/**
 * Die Funktion get_generic_error_page() lädt den Inhalt der Fehlerseite in
 * einen String und gibt diesen zurück.
 * @return string
 */
function get_generic_error_page()
{
    return file_get_contents('./templates/generic_error_page.php', FILE_USE_INCLUDE_PATH);
}

/**
 * Die Funktion log_error übernimmt einen Fehler-String und schreibt ihn inkl.
 * der aktuellen Zeit in eine Log-Datei.
 * 
 * Die Funktion nimmt eine rudimentäre Parameterüberprüfung auf Typ und 
 * Leerstring vor. Es erfolgt kein Error- Handling falls das Schreiben ins 
 * Log-File fehlschlägt.
  * @param string $error
 * @param int $user_id optional
 * @return bool true für Erfolg - false für Misserfolg
 */
function log_error($error, $user_id = null)
{
    if (empty($error) || !is_string($error))
    {
        return false;
    }
    
    $log_string = date('y-m-d H:m:s', time())
                . '; '
                . (empty($user_id) ? '' : ($user_id . ', '))
                . $error
                . ';'
                . PHP_EOL;
    $date = date('y_m_d');
    $filename = './logs/error_log_' . $date . '.txt';
    return file_put_contents($filename, $log_string, FILE_APPEND);
}
?>