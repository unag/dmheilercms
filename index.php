<?php 
session_start();
$_SESSION['current_page'] = 'index.php';

/*******************************************************************************
* Datei: index.php
* Verantwortlicher Programmierer: someone
*******************************************************************************/

require_once './include/template_helper.inc.php';

$title = 'BUG-Software - Startseite';
$content = 'Willkommen auf dem Portal von BUG-Software!';

echo get_built_page($title, $content);
?>