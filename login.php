<?php
session_start();

/*******************************************************************************
 * Datei: login.php
 * Verantwortlicher Programmierer: Benjamin Stenzel
 ******************************************************************************/

require_once './include/database_helper.inc.php';
require_once './include/error_handler.inc.php';
require_once './include/template_helper.inc.php';

// Definition des generischen Anmelde-Fehlers
define('GENERIC_LOGIN_ERROR', '<div>Benutzername oder Passwort sind falsch. Bitte versuchen Sie es erneut.</div>');

// Definition der Fehlermeldung, die angezeigt werden, wenn sich ein gesperrter
// Benutzer einzuwählen versucht
define('BAN_ERROR_TEMP', '<div>Ihr Zugang ist noch bis ####date#### gesperrt.</div>');
define('BAN_ERROR_PERMA', '<div>Ihr Zugang ist gesperrt.</div>');

// Definition der Meldung für erfolgreiche Anmeldung
define('LOGIN_SUCCESS_MESSAGE_PERSONAL', '<div>Herzlich Willkommen, ####name####! Sie werden auf die eben besuchte Seite weitergeleitet.</div>');
define('LOGIN_SUCCESS_MESSAGE_GENERIC', '<div>Herzlich Willkommen! Sie werden auf die eben besuchte Seite weitergeleitet.</div>');

// Definition der Error-Level
define('ERROR_LEVEL_NO_ERROR', 0);
define('ERROR_LEVEL_NON_CRITICAL', 1);
define('ERROR_LEVEL_MALICIOUS', 2);

// Definition der Alert-Level
define('ALERT_LEVEL_NONE', 0);
// define('ALERT_LEVEL_WARN', 3);
define('ALERT_LEVEL_TEMP_BAN', 5);
define('ALERT_LEVEL_PERMA_BAN', 10);

/**
 * Die Funktion get__input_error_level() dient zur Überprüfung der übergebenen 
 * Strings für Benutzername und Passwort. Hier sollen im Wesentlichen 
 * SQL-Injections abgefangen werden.
 *
 * Mögliche Rückgaben:
 * 0 - ERROR_LEVEL_NO_ERROR - Die Prüfungf hat keine Fehler gefunden.
 * 1 - ERROR_LEVEL_NON_CRITICAL - Die Prüfung hat unkritische Fehler gefunden,
 * die vermutlich unabsichtlich sind.
 * 2 - ERROR_LEVEL_MALICIOUS - Die Prüfung hat kritische Fehler gefunden
 * gefunden, die vermutlich absichtlich sind und auf den Versuch von
 * SQL-Injection schließen lassen.
 * 
 * Die Funktion nimmt keine Basis-Fehlerprüfung der übergebenen Parameter vor.
 * Leer-Strings oder NULL-Werte werden nicht abgefangen.
 * @param string $username Benutzername
 * @param string $password unverschlüsseltes Passwort
 * @return int
 */
function get_input_error_level($username, $password)
{
    return ERROR_LEVEL_NO_ERROR;
}

/**
 * Die Funktion handle_login prüft die Eingabe des Nutzers in $_POST
 * (falls vorhanden) und gibt einen entsprechenden String zurück, der zur
 * Einfügung in da login_page_template geeignet ist.
 * @return string
 */
function handle_login()
{
    // FALL1 - User hat login.php direkt aufrufen, wurde also nicht weitegeleitet.
    // Reaktion -> Anzeige eines neuen Login-Formulars
    if (!isset($_POST['submit_login']))
    {
        return get_login_form();
    }
    // FALL2 - User hat eine oder beide Formularfelder leer gelassen
    // Reaktion -> Generischer Login-Fehler und Anzeige eines neuen
    // Login-Formulars
    if (empty($_POST['u']) || empty($_POST['p']))
    {
        return GENERIC_LOGIN_ERROR . get_login_form();
    }
    // Zustand hier: Submit-Button wurde gedrückt und beide Formular-Felder
    // wurden gefüllt.
    $username = $_POST['u'];
    $password = $_POST['p'];
    
    // Prüfung der Formular-Felder
    $error_level = get_input_error_level($username, $password);
    if ($error_level != ERROR_LEVEL_NO_ERROR)
    {
        // Falls ein kritischer Fehler in der Eingabe gefunden wurde, wird die
        // Eingabe geloggt und der Alert-Level des Benutzers (falls in DB
        // vorhanden) erhöht.
        if ($error_level == ERROR_LEVEL_MALICIOUS)
        {
            
        }
        return GENERIC_LOGIN_ERROR . get_login_form();
    }
    
    // Datenbank-Verbindung erstellen und $username gegen SQL-Injections
    // absichern.
    $handle = db_connect(DB_READ);
    $username = mysql_real_escape_string($username, $handle);
    
    // Datenbank-Query (Benutzername, Passwort-Hash, Alert-Level, Alert-Timeout)
    $query = 'SELECT user_id, user_name, password_hash, alert_level, alert_timeout
        FROM pass
        WHERE user_name = "' . $username . '";';
    $result = mysql_query($query, $handle);
    
    // Falls die Query fehlgeschlagen ist, wird ein neues Login-Formular
    // angezeigt und eine Fehlermeldung, dass der Login fehlgeschlagen ist.
    // Ebenso, wenn aus irgendeinem Grund mehr als ein Ergebnis zurückkommt.
    if (!$result || (mysql_num_rows($result) != 1))
    {
        mysql_close($handle);
        return GENERIC_LOGIN_ERROR . get_login_form();
    }
    $data = mysql_fetch_assoc($result);
    mysql_close($handle);
    
    // Falls der Alert-Level des Benutzers größer oder gleich 
    // ALERT_LEVEL_TEMP_BAN ist, wird die Einwahl verweigert und *kein* neues
    // Login-Feld angezeigt.
    if ($data['alert_level'] >= ALERT_LEVEL_TEMP_BAN)
    {
        if ($data['alert_level'] < ALERT_LEVEL_PERMA_BAN)
        {
            return str_replace('####date####', $data['alert_timestamp'], BAN_ERROR_TEMP);
        }
        return BAN_ERROR_PERMA;
    }
    
    // FINALLY - Überprüfung der Eingabe
    $password_hash = hash('ripemd160', $password);
    if ($password_hash != $data['password_hash']) {
        return GENERIC_LOGIN_ERROR . get_login_form();
    }
    
    // Der Nutzer wird angemeldet
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['logged_in'] = true;
    
    // Nachnamen, Vornamen, Location-Id und Rechte aus der Datenbank auslesen
    $handle = db_connect(DB_READ);
    $query = 'SELECT first_name, last_name, location_id, user_rights
        FROM user
        WHERE id = ' . $_SESSION['user_id'] . ';';
    $handle = db_connect(DB_READ);
    $result = mysql_query($query, $handle);
    
    // Wenn die user-Datenbank keinen Eintrag zur user_id aus pass enthält,
    // liegt eindeutig was im Argen. Da das Auslesen der o.a. Daten an diesem
    // Punkt zunächst eine reine Komfort-Funktion ist (namentliche Begrüßung)
    // wird der Fehler nur ins Log geschrieben, das Skript aber nicht beendet.
    if (!$result || (mysql_num_rows($result) != 1))
    {
        mysql_close($handle);
        $error = 'Schwerer Datenbank-Fehler; Kein korrespondierender user-Eintrag zu pass-Eintrag;';
        log_error('$error', $_SESSION['user_id']);
        return LOGIN_SUCCESS_MESSAGE_GENERIC;
    }
    $data = mysql_fetch_assoc($result);
    mysql_close($handle);
    
    $_SESSION['last_name'] = $data['last_name'];
    $_SESSION['first_name'] = $data['first_name'];
    $_SESSION['location_id'] = $data['location_id'];
    $_SESSION['user_rights'] = $data['user_rights'];
    
    $name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
    return str_replace('####name####', $name, LOGIN_SUCCESS_MESSAGE_PERSONAL);
}

// Aufrufen der eigentlichen Login-Funktion
$page_content = handle_login();
$meta = '';
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['current_page']))
{
    // Wenn der Benutzer von einer bekannten Seite kam (aus 
    // $_SESSION['current_page']), wird er automatisch dorthin zurückgeleitet.
    $meta = '<meta http-equiv="refresh" content="5; URL=' . $_SESSION['current_page'] . '">';
}
echo get_built_login_logout_page('Anmeldung', $page_content, $meta);
?>