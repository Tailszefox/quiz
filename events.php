<?php
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<events>';

require('./mysql.php');

//Chat

echo '<chat><![CDATA[';

$requete = '(SELECT *, chat.id as idchat FROM chat, participants WHERE chat.auteur=participants.id ORDER BY chat.id DESC LIMIT 30) ORDER BY timestamp, idchat';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

while($donnees = mysql_fetch_array($reponse))
{
	echo '<strong>&lt;'.$donnees['pseudo'].'&gt;</strong> '.wordwrap($donnees['message'], 130, '<br />', true).'<br />';
}
echo ']]></chat>';

//Nombre de participants

$requete = 'SELECT id FROM participants WHERE actif = 1';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$nbParticipants = mysql_num_rows($reponse);

//Partie

echo '<partie>';

$requete = 'SELECT *, questions.reponse AS vraieReponse, partie.question AS idQuestion FROM partie, questions WHERE partie.question = questions.id  ORDER BY partie.id DESC LIMIT 1';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$essais = 0;
while(mysql_num_rows($reponse) == 0)
{
	sleep(1);
	
	$essais++;
	
	if($essais == 10)
		die('</partie></events>');
	
	$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
}

$donnees = mysql_fetch_array($reponse);
//Pas encore démarré
if(time() < $donnees['demarrage'])
{
	$demarre = 0;

	echo $donnees['xml'];
}
//Question en cours
elseif(time() < $donnees['arret'])
{
	$demarre = 1;
	
	$question = $donnees['question'];

	if(substr($question, -1) != '.')
	{
		 $question = $question . ' ?';
	}
	
	echo '<question><![CDATA['.$question.']]></question>';
	echo '<demarre>'.$demarre.'</demarre>';
	echo '<demarrage>'.$donnees['demarrage'].'</demarrage>';
	echo '<arret>'.$donnees['arret'].'</arret>';
	}
//Terminé
else
{
	$demarrage = time() + 15;
	$arret = $demarrage + 20;
	$xml = '';
	
	$requete = 'SELECT * FROM participants WHERE correcte = 1 ORDER BY timestampDerniereReponse ASC LIMIT 1';
	$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
	
	//Si quelqu'un a la bonne réponse
	if(mysql_num_rows($reponse) == 1)
	{
		$candidatCorrect = mysql_fetch_array($reponse);
		
		$demarre = 2;
		
		if($nbParticipants > 1)
		{
			$points = 4 - $donnees['nbEssais'];
		}
		else
		{
			$points = 0;
		}
		
		$xml .= '<gagnant><![CDATA['.$candidatCorrect['pseudo'].']]></gagnant>';
		$xml .= '<indice>'.$donnees['vraieReponse'].'</indice>';
		
		if($points > 1)
			$xml .= '<points>'.$points.' points</points>';
		else
			$xml .= '<points>'.$points.' point</points>';
		
		//On met à jour le score du candidat
		$requete = 'UPDATE participants SET score=score+'.$points.', scoreTotal=scoreTotal+'.$points.' WHERE id="'.$candidatCorrect['id'].'"';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		//On marque la question comme posée
		$requete = 'UPDATE questions SET nbPosee = nbPosee + 1 WHERE id='.$donnees['idQuestion'].'';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		echo '<info>Question ajoutée</info>';
		
		//On efface la question actuelle
		$requete = 'DELETE FROM partie';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		//On en pose une nouvelle
		//$requete = 'INSERT INTO partie (id, question, demarrage, arret) VALUES("", CEIL(RAND()*(SELECT COUNT(*) FROM questions)), "'.$demarrage.'", "'.$arret.'")';
		//$requete = 'INSERT INTO partie (id, question, demarrage, arret) VALUES("", 1374, "'.$demarrage.'", "'.$arret.'")';
		$requete = 'INSERT INTO partie (id, question, demarrage, arret) VALUES("", (SELECT id FROM questions WHERE nbPosee = (SELECT MIN(nbPosee) FROM questions) ORDER BY RAND() LIMIT 1), "'.$demarrage.'", "'.$arret.'")';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		//On efface les réponses des candidats
		$requete = 'UPDATE participants SET reponse="", correcte=0';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		//On refait la requête
		$requete = 'SELECT * FROM partie, questions WHERE partie.id = questions.id ORDER BY partie.id DESC LIMIT 1';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		$donnees = mysql_fetch_array($reponse);
	}
	//Personne n'a la bonne réponse
	else
	{
		//On réessaye, sinon on passe à la suivante
		if($donnees['nbEssais'] < 3)
		{
			$demarre = 3;
			
			$typeIndice = $donnees['nbEssais'];
			$reponse = $donnees['vraieReponse'];
			
			if($typeIndice == 0)
			{
				preg_match_all('/./u', str_replace(' ', '', $reponse), $tableauReponse);
				if(strlen($reponse) > 1)
					$indice = 'La réponse contient <strong>'.count($tableauReponse[0]).' lettres</strong> et commence par un <strong>'.$tableauReponse[0][0].'</strong>.';
				else
					$indice = 'La réponse contient <strong>une seule</strong> lettre</strong> !';
			}
			elseif($typeIndice == 1)
			{
				preg_match_all('/./u', str_replace(' ', '', $reponse), $tableauReponse);
				$longueur = count($tableauReponse[0]);
				
				if(strlen($reponse) > 4)
					$indice = 'La réponse commence par <strong>'.$tableauReponse[0][0].$tableauReponse[0][1].'</strong> et finit par <strong>'.$tableauReponse[0][$longueur-2].$tableauReponse[0][$longueur-1].'</strong>.';
				elseif(strlen($reponse) > 2)
					$indice = 'La réponse commence par <strong>'.$tableauReponse[0][0].$tableauReponse[0][1].'</strong>.';
				else
					$indice = 'En fait, la réponse est trop courte pour que je puisse vous donner un autre indice, désolé !';
			}
			elseif($typeIndice == 2)
			{
				$tableauReponse = explode(' ', $reponse);
				$reponseShuffle = '';
				
				foreach($tableauReponse as $mot)
				{
					preg_match_all('/./u', $mot, $tableauMot);
					shuffle($tableauMot[0]);
					$reponseShuffle .= implode('', $tableauMot[0]) . ' ';
				}
				
				if(strlen($reponse) > 2)
					$indice = 'Voici la réponse mélangée : <strong>'.trim($reponseShuffle).'</strong>.';
				else
					$indice = 'En fait, la réponse est trop courte pour que je puisse vous donner un autre indice, désolé !';
			}
			
			$xml .= '<gagnant></gagnant>';
			$xml .= '<indice><![CDATA['.$indice.']]></indice>';
			
			$requete = 'UPDATE partie SET demarrage="'.$demarrage.'", arret="'.$arret.'", nbEssais=nbEssais+1';
			//$requete = 'UPDATE partie SET demarrage="'.$demarrage.'", arret="'.$arret.'", nbEssais=nbEssais';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			
			//On efface les réponses des candidats
			$requete = 'UPDATE participants SET reponse="", correcte=0';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			
			//On refait la requête
			$requete = 'SELECT * FROM partie, questions WHERE partie.id = questions.id ORDER BY partie.id DESC LIMIT 1';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			$donnees = mysql_fetch_array($reponse);
		}
		//Trop d'essais, on passe à la question suivante
		else
		{
			$demarre = 4;
			
			$xml .= '<gagnant></gagnant>';
			$xml .= '<indice>'.$donnees['vraieReponse'].'</indice>';
			
			//On marque la question comme posée
			$requete = 'UPDATE questions SET nbPosee = nbPosee + 1 WHERE id='.$donnees['idQuestion'].'';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			
			//On efface la question actuelle
			$requete = 'DELETE FROM partie';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			
			//On en pose une nouvelle
			//$requete = 'INSERT INTO partie (id, question, demarrage, arret) VALUES("", CEIL(RAND()*(SELECT COUNT(*) FROM questions)), "'.$demarrage.'", "'.$arret.'")';
			//$requete = 'INSERT INTO partie (id, question, demarrage, arret) VALUES("", 1374, "'.$demarrage.'", "'.$arret.'")';

			$requete = 'INSERT INTO partie (id, question, demarrage, arret) VALUES("", (SELECT id FROM questions WHERE nbPosee = (SELECT MIN(nbPosee) FROM questions) ORDER BY RAND() LIMIT 1), "'.$demarrage.'", "'.$arret.'")';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			
			//On efface les réponses des candidats
			$requete = 'UPDATE participants SET reponse="", correcte=0';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			
			//On refait la requête
			$requete = 'SELECT * FROM partie, questions WHERE partie.id = questions.id ORDER BY partie.id DESC LIMIT 1';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			$donnees = mysql_fetch_array($reponse);
		}
		
	}
	
	$question = $donnees['question'];

	if(substr($question, -1) != '.')
	{
		 $question = $question . ' ?';
	}
	
	$xml .= '<question><![CDATA['.$question.']]></question>';
	$xml .= '<demarre>'.$demarre.'</demarre>';
	$xml .= '<demarrage>'.$donnees['demarrage'].'</demarrage>';
	$xml .= '<arret>'.$donnees['arret'].'</arret>';
	
	echo $xml;
	
	$requete = 'UPDATE partie SET xml="'.$xml.'"';
	$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
}

echo '<actuel>'.time().'</actuel>';
echo '</partie>';

//Participant

echo '<participants>';

$requete = 'SELECT * FROM participants WHERE actif=1 ORDER BY id';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$nbParticipants = 0;
while($donnees = mysql_fetch_array($reponse))
{
	echo '<participant>';
	echo '<score>'.$donnees['score'].' / '.$donnees['scoreTotal'].'</score>';
	echo '<pseudo><![CDATA['.substr($donnees['pseudo'], 0, 12).']]></pseudo>';
	echo '<reponse><![CDATA['.wordwrap(substr($donnees['reponse'], 0, 40), 30, '<br />', true).']]></reponse>';
	echo '</participant>';
	$nbParticipants++;
}

for($i = $nbParticipants; $i < 8; $i++)
{
	echo '<participant>';
	echo '<score>-</score>';
	echo '<pseudo>---</pseudo>';
	echo '<reponse></reponse>';
	echo '</participant>';
}

echo '</participants>';

echo '</events>';

?>
