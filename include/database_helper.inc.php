<?php
/*******************************************************************************
 * Datei: database_helper.inc.php
 * Verantwortlicher Programmierer: Benjamin Stenzel
 ******************************************************************************/

require_once 'error_handler.inc.php';

/*
 * Berechtigungslevel für den Datenbank-Server
 * Abhängig vom verwendeten Level wird ein unterschiedlicher Datenbank-Account
 * mit abweichenden Rechten verwendet. Ist der User nicht eingeloggt, werden
 * stets nur Leserechte verwendet, unabhängig davon, welcher Parameter übergeben
 * wurde. 
 */
/** Parameter für Leserechte auf die Datenbank - SELECT */
define('DB_READ', 1);
/** Parameter für Schreib- und Leserechte auf die Datenbank - SELECT, INSERT, UPDATE */
define('DB_WRITE', 2);
// define('DB_READ_WRITE', 3);
// define('DB_ALTER', 4);
// define('DB_ALL', 8);

/*
 * Serveradresse, Benutzernamen und Passwörter für die verschiedenen
 * Berechtigungslevel.
 */
define('SERVER', 'localhost:3306');
$users = array();
// DB_READ  - Lesezugriff (SELECT)
$users[DB_READ] = array('username'=>'bug_project_read',
                        'password'=>'RjsJHZM2QKATFAVC');
// DB_WRITE - Schreibzugriff (INSERT & UPDATE)
$users[DB_WRITE] = array('username'=>'bug_project_wrt',
                        'password'=>'VnHjebAtMNGHtLQF');
$GLOBALS['users'] = $users;

/**
 * Die Funktion db_connect() stellt eine Verbindung zum MySQL-Server unter 
 * Verwendung des angegebenen Rechte-Levels her.
 * 
 * Mögliche Rechte-Level:<br />
 * DB_READ  - Lesezugriff (SELECT)<br />
 * DB_WRITE - Schreibzugriff (SELECT, INSERT, UPDATE)
 * 
 * Wenn der aktuelle Benutzer nicht angemeldet ist, wird unabhängig vom
 * angeforderteten Rechte-Level nur Lesezugriff freigegeben.
 * 
 * Wenn die Verbindung fehlschlägt oder der angeforderte Rechte-Level nicht 
 * extistiert, wird die Skriptausführung abgebrochen.
 * 
 * Im Fehlerfall wird stets ein Eintrag ins Log-File geschrieben.
  */
function db_connect($right_level)
{
    if (!isset($GLOBALS['users'][$right_level]))
    {
        $error = 'db_connect() - Angeforderter Berechtigungslevel '
               . $right_level
               . ' nicht vorhanden';
        log_error($error);
        die(GENERIC_ERROR);
    }
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'])
    {
        $right_level = DB_READ;
    }
    $handle = @mysql_connect(SERVER, $GLOBALS['users'][$right_level]['username'], $GLOBALS['users'][$right_level]['password']);
    if (!$handle)
    {
        $error = 'Verbindung zum Datenbankserver fehlgeschlagen; Berechtigungslevel: '
               . $right_level
               . '; Username: '
               . $GLOBALS['users'][$right_level]['username'];
        log_error($error);
        die(GENERIC_ERROR);
    }
    mysql_set_charset('UTF8');
    mysql_select_db('db_bug_project', $handle);
    return $handle;
}
?>