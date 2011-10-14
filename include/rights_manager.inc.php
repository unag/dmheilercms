<?php
/*******************************************************************************
 * Datei: rights_manager.inc.php
 * Verantwortlicher Programmierer: Benjamin Stenzel
 ******************************************************************************/

require_once 'database_helper.inc.php';
require_once 'error_handler.inc.php';

// Definition der Rechte-Flags
/** Beiträge des Users sind für andere sichtbar */
define('RIGHTS_VISIBLE', 1);
/** User darf Beiträge im Schwarzen Brett absetzen */
define('RIGHTS_BLACKBOARD_WRITE', 2);
/** User darf News-Beiträge schreiben */
define('RIGHTS_NEWS_WRITE', 4);
/** Admin-Recht - darf User verwarnen */
define('RIGHTS_WARN', 32);
/** Admin-Recht - darf User sperren */
define('RIGHTS_BAN', 64);
/** Admin-Recht - darf die Rechte anderer User verändern */
define('RIGHTS_ALTER_RIGHTS', 512);


// Definition der Default-Rechte
/** Default-Rechte für User - User sind sichtbar und dürfen BB-Beiträge schreiben */
define('DEFAULT_RIGHTS_USER', 3);
/** Default-Rechte für Journalisten - Journalisten dürfen zusätzlich zu regulären Benutzern News-Beiträge schreiben */
define('DEFAULT_RIGHTS_JOURNALIST', 7);
/** Default-Rechte für Admins - Admins dürfen alles */
define('DEFAULT_RIGHTS_ADMIN', 615);

/**
 * Die Funktion get_user_rights liest die Rechte eines Benutzers aus der 
 * Datenbank aus und gibt sie zurück. Wird kein Wert übergeben oder gibt es 
 * keinen entsprechenden Eintrag in der Datenbank, wird -1 zurückgegeben.
 * @param int $user_id Benutzer-Id
 * @return int 
 */
function get_user_rights($user_id)
{
    if (empty($user_id))
    {
        return -1;
    }
    $query = 'SELECT user_rights FROM user WHERE id = "' . $user_id . '";';
    $handle = db_connect(DB_READ);
    $result = mysql_query($query, $handle);
    $user_rights = -1;
    if ($result && (mysql_num_rows($result) == 1))
    {
        $row = mysql_fetch_assoc($result);
        $user_rights = $row['user_rights'];
    }
    mysql_close($handle);
    return $user_rights;
}

/**
 * Die Funktion has_current_user_right() prüft, ob der aktuelle Benutzer
 * die angegebenen Rechte hat. Wenn der aktuelle Benutzer nicht
 * angemeldet ist, oder seine Benutzer-Id nicht im $_SESSION-Array vorhanden
 * ist, wird false zurückgegeben.
 * @return bool true wenn aktueller Nutzer entsprechende Rechte hat
 */
function has_current_user_right($right)
{
    if (!isset($_SESSION) || !isset($_SESSION['logged-in']) || !$_SESSION['logged-in'])
    {
        return false;
    }
    if (!isset($_SESSION['user_id']))
    {
        return false;
    }
    if (!isset($_SESSION['user_rights']))
    {
        $_SESSION['user_rights'] = get_user_rights($_SESSION['user_id']);
    }
    if ($_SESSION['user_rights'] == -1)
    {
        return false;
    }
    return ($_SESSION['user_rights'] & $right) == $right;
}

/**
 * Die Funktion grant_rights_to_user() setzt Rechte in einem Benutzer-Account
 * unter Beibehaltung der bisherigen Rechte. Wenn der aktuelle Benutzer nicht
 * angemeldet ist oder keine Rechte setzen darf, wird false zurückgegeben.
 * @param int $user_id Benutzer-Id
 * @param int $rights Parameterliste der zu setzenden Benutzer-Rechte
 * @return bool true für Erfolg
 */
function grant_rights_to_user($user_id, $rights)
{
    if (!has_current_user_right(RIGHTS_ALTER_RIGHTS))
    {
        return false;
    }
    $current_rights = get_user_rights($user_id);
    if ($current_rights == -1)
    {
        return false;
    }
    $grant_rights = 0;
    for ($i = 1; $i < func_num_args(); $i++)
    {
        $grant_rights |= func_get_arg($i);
    }
    return set_user_rights($user_id, $current_rights | $grant_rights);
}

/**
 * Die Funktion revoke_rights_of_user() widerruf Rechte in einem Benutzer-
 * Account unter Beibehaltung der anderen Rechte. Wenn der aktuelle Benutzer 
 * nicht angemeldet ist oder keine Rechte setzen darf, wird false zurückgegeben.
 * @param int $user_id Benutzer-Id
 * @param int $rights Parameterliste der zu widerrufenden Benutzer-Rechte
 * @return bool true für Erfolg
 */
function revoke_rights_of_user($user_id, $rights)
{
    if (!has_current_user_right(RIGHTS_ALTER_RIGHTS))
    {
        return false;
    }
    $current_rights = get_user_rights($user_id);
    if ($current_rights == -1)
    {
        return false;
    }
    $revoke_rights = 0;
    for ($i = 1; $i < func_num_args(); $i++)
    {
        $revoke_rights |= func_get_arg($i);
    }
    return set_user_rights($user_id, $current_rights & ~$revoke_rights);
}

/**
 * Die Funktion set_user_rights() setzt Rechte in einem Benutzer-Account.
 * 
 * ACHTUNG: Die bisherigen Rechte des Benutzers werden überschrieben!
 * 
 * Wenn der aktuelle Benutzer nicht
 * angemeldet ist oder keine Rechte setzen darf, wird false zurückgegeben.
 * @param int $user_id Benutzer-Id
 * @param int $rights Parameterliste der zu setzenden Benutzer-Rechte
 * @return bool true für Erfolg
 */
function set_user_rights($user_id, $rights)
{
    if (!has_current_user_right(RIGHTS_ALTER_RIGHTS) || empty($rights) || empty($user_id))
    {
        return false;
    }
    $set_rights = 0;
    for ($i = 1; $i < func_num_args(); $i++)
    {
        $set_rights |= func_get_arg($i);
    }
    
    $query = 'UPDATE user SET user_rights = "' . $set_rights . '" WHERE id = "' . $user_id . '" LIMIT 1;';
    $handle = db_connect(DB_WRITE);
    $result = mysql_query($query, $handle);
    mysql_close($handle);
    return $result;
}

/**
 * Die Funktion has_user_rights wertet aus, ob ein Benutzer die angegebenen
 * Rechte hat. Ist die übergebene Id in der Datenbank nicht vorhanden oder
 * wurde kein Parameter für $right übergeben, wird false zurückgegeben.
 * @param int $user_id Benutzer-Id
 * @param int $right Benutzerrechte
 * @return bool true für Rechte vorhanden, false für Rechte nicht vorhanden
 */
function has_user_right($user_id, $right)
{
    if (empty($right) || empty($user_id))
    {
        return false;
    }
    $user_rights = get_user_rights($user_id);
    if ($user_rights == -1)
    {
        return false;
    }
    return ($user_rights & $right) == $right;
}
?>