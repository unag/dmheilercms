<?php
/*******************************************************************************
 * Datei: template_helper.inc.php
 * Verantwortlicher Programmierer: Benjamin Stenzel
 ******************************************************************************/

require_once 'error_handler.inc.php';

/** Fehlermeldung für den Fall, dass Login-Container-Template nicht gefunden wurde */
define('ERROR_LOGIN_CONAINER_NOT_FOUND', 'Die Anmeldung ist derzeit nicht möglich, bitte versuchen Sie es zu einem späteren Zeitpunkt nochmal');

/**
 * Die Funktion get_built_page() lädt das HTML-Template, ersetzt die darin
 * enthaltenen Platzhalter gegen die in $title und $content enthaltenen Strings
 * und gibt die fertig erstellte Seite dann zurück.
 *
 * Die Funktion nimmt keine Parameterüberprüfung vor. Wenn das Laden des
 * HTML-Templates fehlschlägt, wird eine generische Fehler-Seite zurückgegeben.
 * @param string $title Seitentitel
 * @param string $content Seiteninhalt
 * @return string
 */
function get_built_page($title, $content)
{
    $template = file_get_contents('./templates/template.php', FILE_USE_INCLUDE_PATH);
    if (!$template)
    {
        log_error('template not found!');
        return get_generic_error_page();
    }
    $template = str_replace('####title####', $title, $template);
    $template = str_replace('####content####', $content, $template);
    if (isset($_SESSION) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'])
    {
        $name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        $logout_container = '<div>Sie sind angemeldet als ' . $name . '.</div>'
                          . '<div><a href="logout.php">Abmelden</a></div>';
        $template = str_replace('####rightcol####', $logout_container, $template);
    }
    else
    {
        $login_container = file_get_contents('./templates/login_form_for_main_page.php', FILE_USE_INCLUDE_PATH);
        if (!$login_container)
        {
            log_error('login_form_for_main_page.php not found!');
            $login_container = ERROR_LOGIN_CONAINER_NOT_FOUND;
        }
        $template = str_replace('####rightcol####', $login_container, $template);
    }
    return $template;
}

/**
 * Die Funktion get_built_login_logout_page() lädt das Login/Logout-Template
 * und fügt an den entsprechenden Platzhaltern die strings aus $title, $content
 * und $meta ein.
 * @param string $title Seitentitel
 * @param string $content Seiteninhalt
 * @param string $meta Meta-Tag z.B. zur Weiterleitung
 * @return string 
 */
function get_built_login_logout_page($title, $content, $meta)
{
    $template = file_get_contents('./templates/login_page_template.php', FILE_USE_INCLUDE_PATH);
    if (!$template)
    {
        log_error('login template not found!');
        return get_generic_error_page();
    }
    $template = str_replace('<!-- ####meta#### -->', $meta, $template);
    $template = str_replace('####content####', $content, $template);
    $template = str_replace('####title####', $title, $template);
    return $template;
}

/**
 * Lädt den HTML-Code für das Anmeldeformular und gibt ihn zurück
 * @return string
 */
function get_login_form()
{
    $template = file_get_contents('./templates/login_form_template.php', FILE_USE_INCLUDE_PATH);
    if (!$template)
    {
        log_error('login form not found!');
        die(GENERIC_ERROR);
    }
    return $template;
}
?>