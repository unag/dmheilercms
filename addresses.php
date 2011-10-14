<?php
session_start();

require_once './include/template_helper.inc.php';

// hier content basteln
$title = 'Wichtige Adressen';
$content = <<<TAG
<h1>Doller Adressen Content</h1>
<div>Hier stehen viele Adressen. Zum Beispiel von Shani Shanaviri Dingens aus der Verwaltung.</div>
TAG;

echo get_built_page($title, $content);
?>
