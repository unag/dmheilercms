<?php

/*******************************************************
*Datei: blackboard.php                                 *
*Verantwortlicher Programmierer: Uwe Nagel             *
*******************************************************/

session_start();
$_SESSION['current_page'] = 'blackboard.php';
require_once  './include/database_helper.inc.php';
require_once './include/template_helper.inc.php';
require_once './include/logged_in_check.inc.php';
// hier content basteln
$title = 'Schwarzes Brett';
$content ='<span class="bb_tab"><a href="blackboard_input.php" >Inserat aufgeben</a></span>';      
$query =    "SELECT * 
            FROM blackboard, blackboard_subject, user 
            WHERE blackboard.blackboard_subject_id = blackboard_subject.id 
            AND blackboard.user_id = user.id ORDER BY timestamp DESC";
db_connect(DB_READ);
$result = mysql_query($query);  
if ($result)
{    
    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {        
        $content .= '<table class="bb_tab" color="#252671" width="100%" align="center" >
                        <tr>
                            <td><span class = "bb_very_big_font" >'.$row['description'].'</span></td>
                        </tr>
                        <tr>
                            <td><span class = "bb_big_font" >'.$row['subject'].'</span></td>
                        </tr>
                        <tr>
                            <td>'.$row['text'].'</td>
                        </tr>
                        <tr>
                            <td>'.$row['timestamp'].' '.$row['first_name'].' '.$row['last_name'].'</td>
                        </tr>'.
                        logged_in_check($row['user_id']).
                    '</table>';
    }
}
echo get_built_page($title, $content);
?>
