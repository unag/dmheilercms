<?php
/*******************************************************
*Datei: blackboard_input.php                           *
*Verantwortlicher Programmierer: Uwe Nagel             *
*******************************************************/
session_start();
$_SESSION['current_page'] = 'blackboard_input.php';

require_once  './include/database_helper.inc.php';
require_once './include/template_helper.inc.php';
require_once './include/is_send.inc.php';

$title = 'Inserat aufgeben';
$content ='';      
$query = "";

if (false && (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']))
{
    echo get_built_page($title, 'Sie sind nicht angemeldet');    
}
else
{
    $input ='<div class="bb_input" >
                <form  action="" method="Post" >
                        Suchen Sie zuerst eine Rubrik aus:<br />
                    <select name="bb_subject" size="1">
                          <option value="1">Suche</option>
                          <option value="2">Verkaufe</option>
                          <option value="3">Verschenke</option>
                          <option value="4">Tausche</option>
                          <option value="5">Gefunden</option>
                    </select><br />        
                        Hier bitte Ihren Betreff angeben:<br />
                    <input type="text" name="subject" value="'
                    .  !empty($_POST['subject']) ? is_send($_POST['subject']) : $_POST['subject'] .'"/><br />
                        Bitte geben Sie hier Ihren Inserat-Text ein:<br />
                    <textarea name="user_input" value="'.  is_send($_POST['user_input']) .'" cols="50" rows="10"></textarea><br />
                    <input type="submit" name="send_bb" value="absenden"/>
                </form>
            </div>';
    echo get_built_page($title, $input);
}
if(isset($_POST['send_bb']))
{
    if(($_POST['bb_subject'] <=0) || ($_POST['bb_subject'] > 5) || empty ($_POST['subject']) || empty ($_POST['user_input']))
{
        echo '<span class = "bb_very_big_font" >Bitte alle Felder ausfüllen!</span>';
}
    db_connect(DB_WRITE);
    $query =   'INSERT INTO blackboard (subject, text, user_id, blackboard_subject_id)
                VALUES ("'.$_POST['subject'].'", "'.$_POST['user_input'].'", "'.$_SESSION['user_id']
                .'", "'. $_POST['bb_subject'].'")';
    $result = mysql_query($query);
    if($result)
    {
        echo 'BINGO';
    }
    else
    {
        echo mysql_error();
    }
}


/******************************************************************************************
* Hier muss irgendwie die Eingabe der Inserate durch die User (von mir) realisiert werden *
******************************************************************************************/
//$result = mysql_query($query);            



?>
