<?php

require('./mysql.php');

$questions = fopen('./questions.txt', 'r');

if($questions == FALSE)
{
	die('Impossible d\'ouvrir le fichier.');
}

while(!feof($questions))
{
	$questionEtReponse = str_replace("\r\n", '', fgets($questions));
	
	$tableau = explode("?", $questionEtReponse);
	
	$question = trim($tableau[0]);
	$reponse = trim($tableau[1]);
	
	$requete = 'INSERT INTO questions (id, question, reponse) VALUES("", "'.$question.'", "'.$reponse.'")';
	//$reponse = mysql_query($requete )or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
}

fclose($questions);
?>
