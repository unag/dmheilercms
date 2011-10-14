<?php
session_start();

require_once './include/template_helper.inc.php';
require_once './include/database_helper.inc.php';

$title = 'Aktuelle Eingabe';

if (false && (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']))
{
    echo get_built_page($title, 'Sie sind nicht angemeldet');    
}
else
{
    $input ='<div class="news_input">
                  <table>
                      <form method="post" action="">
                      <tr><td>Bitte Überschrift eingeben:</td>     
                          <td><span class="news_subject"><textarea name="news_subject" rows="1"></textarea></span></td></tr> 
                      <tr><td>Bitte Text eingeben:</td>     
                          <td><textarea name="news_text"></textarea></td></tr>                  
                      <tr><td>Bitte Thema auswählen</td>
                          <td><select name="news_subject" size="1">
                              <option>EDV</option>
                              <option>Organisatorisches</option>
                              <option>Personelles</option>
                              <option>Feierlichkeiten</option>
                              <option>Wichtiges</option>
                              <select /></td></tr>
                      <tr><td></td>
                          <td><input type="submit" name="news_submit" 
                               value="Absenden" /></td>
                      </tr>                  
                      </form>
                  </table>
            </div>';      

    echo get_built_page($title, $input);
}//textarea --> cols="10" rows="5"

?>
