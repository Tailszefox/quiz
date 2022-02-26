<?php
session_start();
$message = trim(htmlspecialchars($_POST['message'], ENT_COMPAT, 'UTF-8'));

$chemin = '<img src=\"smileys/';

$code = array(':D', ':\\\'(', 'oO', 'Oo', '&gt;&gt;', '&lt;&lt;', '^^', ':s', ':0', ':@', ':nerdz:', ':|', ':)', ':o', ':p', ';)', ':(');
$smiley = array($chemin.'biggrin.gif'.'\">', $chemin.'cryingsmiley.gif'.'\">', $chemin.'eek.gif'.'\">', $chemin.'eek.gif'.'\">', $chemin.'glare.gif'.'\">', $chemin.'glare.gif'.'\">', $chemin.'happy.gif'.'\">', $chemin.'huh.gif'.'\">', $chemin.'lmaosmiley.gif'.'\">', $chemin.'mad.gif'.'\">', 
	$chemin.'nerd.gif'.'\">', $chemin.'neutral.gif'.'\">', $chemin.'smile.gif'.'\">', $chemin.'ohmy.gif'.'\">', $chemin.'tongue.gif'.'\">', $chemin.'wink.gif'.'\">',  $chemin.'sad.gif'.'\">');

$message = str_replace($code, $smiley, $message);

//echo $message;

require('./mysql.php');

if($message != '')
{
	$requete = 'INSERT INTO chat (auteur, message, timestamp) VALUES("'.$_SESSION['id'].'", "'.$message.'", "'.time().'")';
	$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
}
?>
