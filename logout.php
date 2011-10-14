<?php
session_start();

/*******************************************************************************
 * Datei: logout.php
 * Verantwortlicher Programmierer: Benjamin Stenzel
 ******************************************************************************/

require_once './include/template_helper.inc.php';

$previous_page = (isset($_SESSION['previous_page'])) ? $_SESSION['previous_page'] : 'index.php';
$meta = '<meta http-equiv="refresh" content="5; URL=' . $previous_page . '">';

$_SESSION = array();
session_destroy();

$content = 'Sie wurden abgemeldet. Weiterleitung zur Hauptseite.';

echo get_built_login_logout_page('Abmeldung', $content, $meta);
?>