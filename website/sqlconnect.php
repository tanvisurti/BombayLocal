<?php
$link = mysql_connect('localhost', 'bombajrm_tanvi', '3stupids'); 
if (!$link) { 
    die('Could not connect: ' . mysql_error()); 
}
mysql_select_db("bombajrm_bombaylocal", $link);

?>
