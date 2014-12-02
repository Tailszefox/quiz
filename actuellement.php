<?php
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<actuel>';

require('./mysql.php');

$dixMinutes = time()-600;

//On supprime les joueurs qui n'ont donné aucune réponse depuis 10 minutes
$requete = 'UPDATE participants SET actif=0, score=0 WHERE timestampDerniereReponse < "'.$dixMinutes.'" ';
mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

//On va voir combien il reste de joueurs
$requete = 'SELECT id FROM participants WHERE actif = 1';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$nbJoueurs = mysql_num_rows($reponse);
$max = 8;

//Si aucun joueur, on peut supprimer les restes de la partie précédente
if($nbJoueurs == 0)
{
	$requete = 'DELETE FROM partie';
	$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
	$requete = 'DELETE FROM chat';
	$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
	$actuellement = 'Il n\'y a aucun joueur dans la partie.';
}
elseif($nbJoueurs == 1)
{
	$actuellement = 'Il y a un joueur dans la partie.';
}
elseif($nbJoueurs == $max)
{
	$actuellement = 'Il y a '.$nbJoueurs.' joueurs dans la partie. Désolé, vous ne pouvez pas participer !<br />Attendez un peu ou revenez plus tard...';
}
else
{
	$actuellement = 'Il y a  '.$nbJoueurs.' joueurs dans la partie.';
}             

if($nbJoueurs == $max)
	echo '<max>1</max>';
else
	echo '<max>0</max>';

echo '<actuellement><![CDATA['.$actuellement.']]></actuellement>';

echo '</actuel>';
?>
