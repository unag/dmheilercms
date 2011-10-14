<?php
session_start();
//einmal include-dateien einbinden
require_once './include/template_helper.inc.php';
require_once './include/database_helper.inc.php';

$title = 'Aktuelles';//

//$content = <<<TAG
//<h1>Dolle Neuigkeiten Content</h1>
//<div>hier stehen viele dolle Neuigkeiten. Mit Farbe und Bunt und krass HTML-formatiert.</div>
//TAG;
 $handle = db_connect(DB_READ);
//$handle = mysql_connect('localhost:3306', 'root');
//mysql_select_db('db_bug_project', $handle);
//$query = 'SELECT subject, timestamp, text, first_name, last_name, from news, news_subject, user
  //       WHERE news.subject_id = news_subject.id AND user.id = news.user_id ORDER BY timestamp DESC';
$query = 'SELECT description, subject, text, first_name, last_name, timestamp FROM news_subject, news, user WHERE news.user_id = user.id AND news_subject.id = news.subject_id ORDER BY timestamp DESC;';


$result = mysql_query($query, $handle);
//echo $result;
/*if ($result)
{
    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        print_r($row);
    }
} 
*/
$content = '<div class="news_link">' 
            . '<a href="newsinput.php">Button neuer Eintrag</a>' . '</div>';


//mysql_query($content)or die(mysql_error());
if ($result)
{
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$part =     '<div class="news_div">'
			  . '<span class="news_description">' . $row['description'] .': ' . '<br>' . '</span>'
			  . '<span class="news_subject">' . $row['subject'] .' ' . '</span>'
                          . '<div class="news_text">' . '<p>' . $row['text'] .' ' . '</p>'
                          . '</div>'
                          . '<div class="news_datname">'
                          . '<div class = "news_name">'
                          . $row['first_name'] .' '
                          . $row['last_name'] .' '
                          . '</div>'
                          . '<div class="news_timestamp">' . $row['timestamp'] 
                          . '</div>'
                          . '</div>'
			  . '</div>';
		$content .= $part;              
	}

}
//$content.= '<div class="news_link">' . '<hr>' . '<a href="newsinput.php">Neuer Eintrag</a>' . '</div>';
//echo $content;
/*
if (isset($_POST['news_submit'])) 
{                            

$input = '<div class="news_input">
              <form method="post" action="news.php">
              <select name="news_subject" size="2" multiple="multiple">
              <option>EDV</option>
              <option>Organisatorisches</option>
              <option>Personelles</option>
              <option>Feierlichkeiten</option>
              <option>Wichtiges</option>
              <select />
              Text eingeben:     
              <textarea name="news_text" cols="50" rows="10"></textarea><br/>
              <input type="submit" name="news_submit" value="Absenden" /><br/> 
              </form>
          </div>';      
}



*/
echo get_built_page($title, $content);

?>

