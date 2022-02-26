<?php
session_start();
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<connexion>';

require('./mysql.php');

if(isset($_POST['pseudo']))
{
	$pseudoP = htmlspecialchars($_POST['pseudo'], ENT_COMPAT, 'UTF-8');
	$password = md5($_POST['password']);
}
else
	die();

$requete = 'SELECT id FROM participants WHERE actif=1';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$nbJoueurs = mysql_num_rows($reponse);

$requete = 'SELECT * FROM participants WHERE LOWER(pseudo)="'.strtolower($pseudoP).'"';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

//Nouveau joueur
if(mysql_num_rows($reponse) == 0)
{
	if(isset($_SESSION['pseudo']) == FALSE || strtolower($_SESSION['pseudo']) == strtolower($pseudoP))
	{
		if(isset($_SESSION['pseudo']) == FALSE && $nbJoueurs == 8)
		{
			echo '<etat>0</etat>';
			echo '<message><![CDATA[Désolé, pendant que vous aviez le dos tourné, la partie s\'est remplie est à atteint le maximum de joueurs autorisés. Vous devriez essayer de revenir plus tard...]]></message>';
		}
		else
		{
			$requete = 'INSERT INTO participants(pseudo, password, timestampDerniereReponse, actif, reponse) VALUES("'.$pseudoP.'", "'.$password.'", "'.time().'", 1, "")';
			$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
			
			$_SESSION['pseudo'] = $pseudoP;
			$_SESSION['id'] = mysql_insert_id();
			
			echo '<etat>1</etat>';
			echo '<message><![CDATA[Vous allez être redirigé sur la page du quiz où vous allez pouvoir commencer à jouer. Hourra !]]></message>';
		}
	}
	else
	{
		echo '<etat>0</etat>';
		echo '<message><![CDATA[Vous êtes déjà connecté sous le pseudo <strong>'.$_SESSION['pseudo'].'</strong> ! Vous devez d\'abord vous déconnecter si vous désirez utiliser un nouveau pseudo.]]></message>';
	}
}
//Ancien joueur
else
{
	$donnees = mysql_fetch_array($reponse);

	if($password == $donnees['password'])
	{		
		if(isset($_SESSION['pseudo']) == FALSE || strtolower($_SESSION['pseudo']) == strtolower($pseudoP))
		{
			if(isset($_SESSION['pseudo']) == FALSE && $nbJoueurs == 8)
			{
				echo '<etat>0</etat>';
				echo '<message><![CDATA[Désolé, pendant que vous aviez le dos tourné, la partie s\'est remplie est à atteint le maximum de joueurs autorisés. Vous devriez essayer de revenir plus tard...]]></message>';
			}
			else
			{
				$_SESSION['pseudo'] = $pseudoP;
				$_SESSION['id'] = $donnees['id'];
				
				$requete = 'UPDATE participants SET actif = 1, score=0, timestampDerniereReponse="'.time().'" WHERE id = "'.$donnees['id'].'"';
				$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
				
				echo '<etat>1</etat>';
				echo '<message><![CDATA[Vous allez être redirigé sur la page du quiz où vous allez pouvoir commencer à jouer. Hourra !]]></message>';
			}
		}
		else
		{
			echo '<etat>0</etat>';
			echo '<message><![CDATA[Vous êtes déjà connecté sous le pseudo <strong>'.$_SESSION['pseudo'].'</strong> ! Vous devez d\'abord vous déconnecter si vous désirez utiliser un nouveau pseudo.]]></message>';
		}
	}
	else
	{
		echo '<etat>0</etat>';
		echo '<message><![CDATA[Soit le pseudo dont vous voulez est déjà utilisé par quelqu\'un d\'autre, soit vous vous êtes planté dans le mot de passe. Dans tous les cas, va falloir réessayer !]]></message>';
	}
}

echo '</connexion>';

?>
