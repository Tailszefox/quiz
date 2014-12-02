<?php
require('./mysql.php');

$requete = 'SELECT * FROM partie, questions WHERE partie.question = questions.id LIMIT 1';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

while($donnees = mysql_fetch_array($reponse))
{
	echo utf8_encode($donnees['question']);
}
?>
