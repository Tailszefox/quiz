<?php
session_start();
$reponseCandidat = $_POST['rep'];

require('./mysql.php');

$requete = 'SELECT * FROM partie, questions WHERE partie.question = questions.id LIMIT 1';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$donnees = mysql_fetch_array($reponse);
$vraieReponse = $donnees['reponse'];

$requete = 'UPDATE participants SET reponse="'.htmlspecialchars($reponseCandidat, ENT_COMPAT, 'UTF-8').'", timestampDerniereReponse="'.time().'" WHERE id="'.$_SESSION['id'].'"';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

//Vérification de la réponse

$reponseCandidatM = strtolower($reponseCandidat);
$vraieReponseM = strtolower($vraieReponse);
$bonneReponse = 0;

//Si le type a directement la bonne réponse, pas de problème
if($reponseCandidatM == $vraieReponseM)
	$bonneReponse = 1;
else
{
	//Sinon on cherche si la réponse est proche
	$proche = levenshtein($reponseCandidatM, $vraieReponseM);
	$tolerance = floor(strlen($vraieReponseM)/5);
	
	if($proche <= $tolerance)
	{
		$bonneReponse = 1;
	}
}

/*
echo strlen($vraieReponseM) . ' soit ' .$tolerance. '    ';
echo "Bonne réponse : $vraieReponseM\n";
echo "Réponse donnée : $reponseCandidatM";
echo "\n$proche";
echo "\n$bonneReponse";
*/

if($bonneReponse == 1 )
	$requete = 'UPDATE participants SET correcte=1 WHERE id="'.$_SESSION['id'].'"';
else
	$requete = 'UPDATE participants SET correcte=0 WHERE id="'.$_SESSION['id'].'"';
	
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
?>
