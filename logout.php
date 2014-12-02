<?php
session_start();

require('./mysql.php');

//On met l'utilisateur en inactif
$requete = 'UPDATE participants SET actif=0, score=0 WHERE id="'.$_SESSION['id'].'"';
mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

//On supprime la session
$_SESSION = array();
if(isset($_COOKIE[session_name()]))
{
    setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();

?>
