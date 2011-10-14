<?php
require_once '../include/error_handler.inc.php';
require_once '../include/database_helper.inc.php';
require_once '../include/template_helper.inc.php';

$handle = db_connect(DB_READ);
$query = 'SELECT * FROM blackboard';
$result = mysql_query($query, $handle);
$content = '<pre>';
if ($result)
{
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        // $content .= $row['text'] . '<br />';
        echo $row['text'] . '<br />';
    }
}
$content .= '</pre>';
//echo $content;
?>
