<?php
session_start();

require_once './include/template_helper.inc.php';

// hier content basteln
$title = 'Aktuelles';
$content = <<<TAG
<h1>Dolle Neuigkeiten Content</h1>
<div>hier stehen viele dolle Neuigkeiten. Mit Farbe und Bunt und krass HTML-formatiert.</div>
TAG;
$query = "SELECT subject,timestamp,id,text from news, news_subject
    WHERE news.subject_id = news_subject.id ORDER BY timestamp DESC";
$result = mysql_query($query);
// echo $result;
$content = '';
if ($result)
{
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$part = '<div class="news">'
			  . '<span>' . $row['description'] . '</span>'
			  . '<span>' . $row['subject'] . '</span>'
			  
			  . '<span>' . date('...', $row['timestamp']) . '</span>'
			  . '</div>';
		$content .= $part;
	}
}


/*
<div class="news">
<span>$description="SELECT description FROM news_subject"<span />
<span>$subject="SELECT subject FROM news"<span />
<div>$text="SELECT text FROM news"<div />
<span>$firstname="SELECT first_name FROM user WHERE user.id = news.user_id"<span />
<span>$lastname="SELECT last_name FROM user WHERE user.id = news.user_id"<span />
<span>$newsdate="SELECT timestamp FROM news"<span />
<div />
*/

echo get_built_page($title, $content);
?>
