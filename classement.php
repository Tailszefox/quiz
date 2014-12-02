<?php
require('./mysql.php');

$requete = 'SELECT * FROM participants ORDER BY scoreTotal DESC LIMIT 10';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$podium = 1;

while($donnees = mysql_fetch_array($reponse))
{
	if($podium == 1)
	{
		$style = 'style="color: #FFD700;"';
	}
	elseif($podium == 2)
	{
		$style = 'style="color: #C0C0C0;"';
	}
	elseif($podium == 3)
	{
		$style = 'style="color: #8C7853;"';
	}
	else
	{
		$style = '';
	}
	
	echo '<strong '.$style.'>'.$donnees['pseudo'].'</strong><br />';
	echo $donnees['scoreTotal'].' points<br />';
	
	$podium++;
}

?>
