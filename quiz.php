<?php session_start(); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<title>zOMG Quiz</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery-live.js"></script>
<script type="text/javascript">

var enCours = 0;
var focusReponse = 0;

 var chatscroll = new Object();

  chatscroll.Pane = function(scrollContainerId){
    this.bottomThreshold = 20;
    this.scrollContainerId = scrollContainerId;
    this._lastScrollPosition = 100000000;
  }

  chatscroll.Pane.prototype.activeScroll = function(){

    var _ref = this;
    var scrollDiv = document.getElementById(this.scrollContainerId);
    var currentHeight = 0;
    
    var _getElementHeight = function(){
      var intHt = 0;
      if(scrollDiv.style.pixelHeight)intHt = scrollDiv.style.pixelHeight;
      else intHt = scrollDiv.offsetHeight;
      return parseInt(intHt);
    }

    var _hasUserScrolled = function(){
      if(_ref._lastScrollPosition == scrollDiv.scrollTop || _ref._lastScrollPosition == null){
        return false;
      }
      return true;
    }

    var _scrollIfInZone = function(){
      if( !_hasUserScrolled || 
          (currentHeight - scrollDiv.scrollTop - _getElementHeight() <= _ref.bottomThreshold)){
          scrollDiv.scrollTop = currentHeight;
          _ref._isUserActive = false;
      }
    }


    if (scrollDiv.scrollHeight > 0)currentHeight = scrollDiv.scrollHeight;
    else if(scrollDiv.offsetHeight > 0)currentHeight = scrollDiv.offsetHeight;

    _scrollIfInZone();

    _ref = null;
    scrollDiv = null;

  }
  
var divScroll = new chatscroll.Pane('chat');

function rechargerEvents()
{
	//console.log('Entree fonction : ', enCours);
	if(enCours == 1)
	{
		return -1;
	}
	
	enCours = 1;
	//console.log('Avant requête :', enCours);
	$.get("events.php", {}, function(xml){
			//Gestion du chat
			var chat = $("chat", xml).text();
			$("#chat").html(chat);
			setTimeout("divScroll.activeScroll()", 100);
			
			//Gestion des participants
			var noParticipant = 0;
			var nbParticipants = 0;
			var score = 0;
			var pseudo = '';
			
			$(xml).find('participant').each(function(){
					score = $(this).find('score').text();
					pseudo = $(this).find('pseudo').text();
					reponse = $(this).find('reponse').text();
					
					noParticipant++;
					
					$('#pseudo' + noParticipant).html(pseudo);
					$('#score' + noParticipant).html(score);
					$('#reponseCandidat' + noParticipant).html(reponse);
					
					if(pseudo != '---')
						nbParticipants++;
			});

			//Gestion de la partie
			var demarre = $(xml).find('partie').find('demarre').text();
			
			if(demarre == 0)
			{
				$('#formulaireReponse').css('visiblity', 'hidden');
				
				var compteur = $(xml).find('partie').find('demarrage').text() -$(xml).find('partie').find('actuel').text();
				
				if(compteur > 1)
				{
					$('#secondes').html(compteur + ' secondes');
				}
				else if(compteur == 1)
				{
					$('#secondes').html(compteur + ' seconde');
				}
				
			}
			else if(demarre == 1)
			{
				$('#formulaireReponse').css('visibility', 'visible');

				$('#repliqueJeanPierre').html('Voici la question. Bonne chance !');
				
				var finQuestion = $(xml).find('partie').find('arret').text() -$(xml).find('partie').find('actuel').text();
				if(finQuestion > 1)
				{
					$('#compteur').html('Il vous reste <span id="secondes">' + finQuestion + ' secondes</span> pour répondre.');
				}
				else if(finQuestion == 1)
				{
					$('#compteur').html('Il vous reste <span id="secondes">' + finQuestion + ' seconde</span> pour répondre.');
				}
				
				var question = $(xml).find('partie').find('question').text();
				$('#question').html(question);
			}
			//Bonne réponse d'un candidat
			else if(demarre == 2)
			{
				$('#formulaireReponse').css('visibility', 'hidden');
				$('#message').focus();
				
				var gagnant = $(xml).find('partie').find('gagnant').text();
				var reponse = $(xml).find('partie').find('indice').text();
				var points = $(xml).find('partie').find('points').text();
				
				if(nbParticipants > 1)
					$('#repliqueJeanPierre').html("Top, c'est terminé !<br />Nous avons un gagnant ! Félicitations à <strong>" + gagnant + "</strong> qui a donné la bonne réponse : <strong>" +reponse + "</strong>. Cela lui rapporte <strong>" + points + "</strong>, bravo !");
				else
					$('#repliqueJeanPierre').html("Top, c'est terminé !<br />Bravo <strong>" + gagnant + "</strong> ! Vous avez trouvé la bonne réponse, qui était <strong>" +reponse + "</strong>. Cependant, comme vous jouez tout seul, je ne peux pas vous donner de point, désolé. Mais bravo quand même !");
				
				var compteur = $(xml).find('partie').find('demarrage').text() -$(xml).find('partie').find('actuel').text();
				
				if(compteur > 1)
				{
					$('#compteur').html('La prochaine question sera posée dans <span id="secondes">' + compteur + ' secondes</span>.');
				}
				else if(compteur == 1)
				{
					$('#compteur').html('La prochaine question sera posée dans <span id="secondes">' + compteur + ' seconde</span>.');
				}
			}
			else if(demarre == 3)
			{
				$('#formulaireReponse').css('visibility', 'hidden');
				$('#message').focus();
				
				var indice = $(xml).find('partie').find('indice').text();
				
				$('#repliqueJeanPierre').html("Top, c'est terminé !<br />Et il semblerait que personne n'ait trouvé la bonne réponse. Vous avez besoin d'aide ? Ce petit indice vous servira peut-être...<br />" + indice);
				
				var compteur = $(xml).find('partie').find('demarrage').text() -$(xml).find('partie').find('actuel').text();
				
				if(compteur > 1)
				{
					$('#compteur').html('Vous pourrez retenter votre chance dans <span id="secondes">' + compteur + ' secondes</span>.');
				}
				else if(compteur == 1)
				{
					$('#compteur').html('Vous pourrez retenter votre chance dans <span id="secondes">' + compteur + ' seconde</span>.');
				}
			}
			else if(demarre = 4)
			{
				$('#formulaireReponse').css('visibility', 'hidden');
				$('#message').focus();

				var indice = $(xml).find('partie').find('indice').text();
				$('#repliqueJeanPierre').html("Top, c'est terminé !<br />Et il semblerait que personne n'ait trouvé la bonne réponse.<br />Nous allons passer cette question, puisqu'elle semble vous poser problème. Sachez en tout cas que la réponse était <strong>"+ indice +"</strong> !");
				
				var compteur = $(xml).find('partie').find('demarrage').text() -$(xml).find('partie').find('actuel').text();
				
				if(compteur > 1)
				{
					$('#compteur').html('Vous pourrez retenter votre chance dans <span id="secondes">' + compteur + ' secondes</span>.');
				}
				else if(compteur == 1)
				{
					$('#compteur').html('Vous pourrez retenter votre chance dans <span id="secondes">' + compteur + ' seconde</span>.');
				}
			}
			
			//console.log("focus : ", focusReponse);
			if(demarre == 1)
			{
				if(focusReponse == 0)
				{
					if($('#message').val() == '')
					{
						$('#champReponse').focus();
					}
					
					focusReponse = 1;
				}
			}
			else
			{
				focusReponse = 0;
			}
			//console.log("focus : ", focusReponse);
			
			enCours = 0;
			//console.log('Requête finie : ', enCours);
	});
}

function majCompteur()
{
	var secondes = parseInt($('#secondes').html()) - 1;
	
	if(secondes > 1)
		$('#secondes').html(secondes + ' secondes');
	else if(secondes == 1)
	{
		$('#secondes').html(secondes + ' seconde');
	}
	else if(secondes == 0)
	{
		$('#secondes').html(secondes + ' seconde');
		rechargerEvents();
	}
		
}

function cacherClassement()
{
	$("#afficherClassement").slideDown("slow");
	$("#affichageClassement").slideUp("slow");
}

$(document).ready(function(){
		rechargerEvents();
		
		setInterval("rechargerEvents();", 5000);
		setInterval("majCompteur();", 1000);
		
		$("#affichageClassement").hide();
		
		$('#parler').submit(function(){
				message = $("#message").val();
				$("#message").val('');
				if(message != '')
				{
					$.post('envoimessage.php', {message : message}, function(reponse){
							$("#message").focus();
							rechargerEvents();
					});
				}
				
				return false;
		});
		
		$("#formulaireReponse").submit(function(){
				var rep = $("#champReponse").val();
				$("#champReponse").val('');
				
				if(rep != '')
				{
					$.post('repondre.php', {rep : rep}, function(reponse){
							rechargerEvents();
					});
				}
				return false;
		});
		
		$("#afficherClassement").click(function(){
				$("#affichageClassement").load("classement.php", {}, function(){
						$("#afficherClassement").slideUp("slow");
						$("#affichageClassement").slideDown("slow");
						setTimeout("cacherClassement()", 5000);
				});
		});

<?php		
if(isset($_SESSION['pseudo']))
{
?>
		$(window).unload(function(){
				if(confirm("Êtes-vous sûr de vouloir quitter le jeu ? Si vous voulez seulement recharger la page, cliquez sur Annuler, sans quoi vous serez déconnecté."))
				{					
					$.get('logout.php', {}, function(){
					});
				}
				else
				{
					location.href = "quiz.php";
				}
					
		});
<?php
}
?>
});
</script>
</head>
<body>
<?php
if(!isset($_SESSION['pseudo']))
{
	echo '<p>Petit matin ! Tu peux pas accéder à cette page directement, faut d\'abord choisir un pseudo en passant par <a href="index.php">ici</a>. Na.</p>';
	die();
}

require('./mysql.php');
?>
<p id="jeanpierre-image">
	<img src="jeanpierre.jpg"><br />Jean-Pierre
</p>

<p id="deconnecter">
	<a href="index.php" id="deconnecterTexte">Se<br />déconnecter</a>
</p>

<p id="classement">
<span id="afficherClassement">Classement</span>
<span id="affichageClassement"></span>
</p>

<p id="jeanpierre-chat">
<?php
	//On voit si le joueur est tout seul
	$requete = 'SELECT * FROM participants WHERE actif = 1 ORDER BY id';
	$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
	
	$nbJoueurs = 0;
	while($donnees = mysql_fetch_array($reponse))
	{
		$pseudos[$nbJoueurs] = $donnees['pseudo'];
		$scores[$nbJoueurs] = $donnees['score'];
		$nbJoueurs++;
	}
	
	if($nbJoueurs == 1)
	{
		//On efface toutes les parties en cours
		$requete = 'DELETE FROM partie';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		//On crée la nouvelle partie, qui démarrera dans une minute
		$demarrage = time() + 60;
		$arret = $demarrage + 20;
		
		$requete = 'INSERT INTO partie (id, question, demarrage, arret) VALUES("", (SELECT id FROM questions WHERE nbPosee = (SELECT MIN(nbPosee) FROM questions) ORDER BY RAND() LIMIT 1), "'.$demarrage.'", "'.$arret.'")';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		//On refait la requête
		$requete = 'SELECT * FROM partie, questions WHERE partie.id = questions.id ORDER BY partie.id DESC LIMIT 1';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		$donnees = mysql_fetch_array($reponse);
		
		$xml = '';
		
		$xml .= '<question><![CDATA['.$donnees['question'].']]></question>';
		$xml .= '<demarre>0</demarre>';
		$xml .= '<demarrage>'.$donnees['demarrage'].'</demarrage>';
		$xml .= '<arret>'.$donnees['arret'].'</arret>';

		$requete = 'UPDATE partie SET xml="'.$xml.'"';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
		
		?>
	
		<span id="repliqueJeanPierre">
		Bonjour et bienvenue au zOMG Quiz ! Il semblerait que vous soyez le seul participant pour l'instant...<br />
		Vous pouvez jouer seul, bien sûr, mais c'est moins amusant, et vous ne gagnerez aucun point. Nous allons attendre un petit peu, histoire de voir si personne ne va vous rejoindre.
		</span>
		<br /><br />
		<span id="question"></span>
		<br /><br />
		<span id="compteur">La prochaine question sera posée dans <span id="secondes">60 secondes</span>.</span>
		<?php
	}
	//Sinon on vérifie qu'une partie n'est pas déjà en cours
	else
	{
		$requete = 'SELECT * FROM partie ORDER BY id DESC LIMIT 1';
		$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());
	
		//Partie pas encore démarrée
		while($donnees = mysql_fetch_array($reponse))
		{
			$demarrage = $donnees['demarrage'];
		}
		
		if($demarrage > time())
		{
		?>
			<span id="repliqueJeanPierre">
			Bonjour et bienvenue au zOMG Quiz, et merci de nous avoir rejoint ! Vous allez bientôt pouvoir participer au jeu...<br />Préparez-vous !
			</span>
			<br /><br />
			<span id="question"></span>
			<br /><br />
			<span id="compteur">La prochaine question sera posée dans <span id="secondes">60 secondes</span>.</span>
		<?php
		}
		//Partie en cours
		else
		{
		?>
			<span id="repliqueJeanPierre">
			Bonjour et bienvenue au zOMG Quiz, et merci de nous avoir rejoint !<br />La partie est en cours, je vous demande quelques secondes pour pouvoir tout mettre en place pour vous...
			</span>
			<br /><br />
			<span id="question"></span>
			<br /><br />
			<span id="compteur">La prochaine question sera posée dans <span id="secondes">60 secondes</span>.</span>
		<?php
		}
	}
	?>
</p>

<p id="reponseCandidat1" class="reponsesCandidats">
&nbsp;
</p>

<p id="reponseCandidat2" class="reponsesCandidats">
&nbsp;
</p>

<p id="reponseCandidat3" class="reponsesCandidats">
&nbsp;
</p>

<p id="reponseCandidat4" class="reponsesCandidats">
&nbsp;
</p>

<p id="reponseCandidat5" class="reponsesCandidatsdeux">
&nbsp;
</p>

<p id="reponseCandidat6" class="reponsesCandidatsdeux">
&nbsp;
</p>

<p id="reponseCandidat7" class="reponsesCandidatsdeux">
&nbsp;
</p>

<p id="reponseCandidat8" class="reponsesCandidatsdeux">
&nbsp;
</p>

<p id="pupitre1" class="pupitres">
<span class="pseudos" id="pseudo1">
<?php
if(isset($pseudos[0]))
	echo $pseudos[0];
else
	echo '-';
?>
</span>
<br />
<span class="scores" id="score1">
<?php
if(isset($scores[0]))
	echo $scores[0];
else
	echo '-';
?>
</span>
</p>

<p id="pupitre2"  class="pupitres">
<span class="pseudos" id="pseudo2">
<?php
if(isset($pseudos[1]))
	echo $pseudos[1];
else
	echo '---';
?>
</span>
<br />
<span class="scores" id="score2">
<?php
if(isset($scores[1]))
	echo $scores[1];
else
	echo '-';
?>
</span>
</p>

<p id="pupitre3"  class="pupitres">
<span class="pseudos" id="pseudo3">
<?php
if(isset($pseudos[2]))
	echo $pseudos[2];
else
	echo '---';
?>
</span>
<br />
<span class="scores" id="score3">
<?php
if(isset($scores[2]))
	echo $scores[2];
else
	echo '-';
?>
</span>
</p>

<p id="pupitre4"  class="pupitres">
<span class="pseudos" id="pseudo4">
<?php
if(isset($pseudos[3]))
	echo $pseudos[3];
else
	echo '---';
?>
</span>
<br />
<span class="scores" id="score4">
<?php
if(isset($scores[3]))
	echo $scores[3];
else
	echo '-';
?>
</span>
</p>

<p id="pupitre5"  class="pupitresdeux">
<span class="pseudos" id="pseudo5">
<?php
if(isset($pseudos[4]))
	echo $pseudos[4];
else
	echo '---';
?>
</span>
<br />
<span class="scores" id="score5">
<?php
if(isset($scores[4]))
	echo $scores[4];
else
	echo '-';
?>
</span>
</p>

<p id="pupitre6"  class="pupitresdeux">
<span class="pseudos" id="pseudo6">
<?php
if(isset($pseudos[5]))
	echo $pseudos[5];
else
	echo '---';
?>
</span>
<br />
<span class="scores" id="score6">
<?php
if(isset($scores[5]))
	echo $scores[5];
else
	echo '-';
?>
</span>
</p>

<p id="pupitre7"  class="pupitresdeux">
<span class="pseudos" id="pseudo7">
<?php
if(isset($pseudos[6]))
	echo $pseudos[6];
else
	echo '---';
?>
</span>
<br />
<span class="scores" id="score7">
<?php
if(isset($scores[6]))
	echo $scores[6];
else
	echo '-';
?>
</span>
</p>

<p id="pupitre8"  class="pupitresdeux">
<span class="pseudos" id="pseudo8">
<?php
if(isset($pseudos[7]))
	echo $pseudos[7];
else
	echo '---';
?>
</span>
<br />
<span class="scores" id="score8">
<?php
if(isset($scores[7]))
	echo $scores[7];
else
	echo '-';
?>
</span>
</p>

<form id="formulaireReponse" action="">
	<input type="text" name="champReponse" id="champReponse" autocomplete="off"> <input type="submit" value="Répondre">
</form>

<p id="chat">
</p>
<form id="parler" action="">
	<input type="text" name="message" id="message" size="100" autocomplete="off"> <input type="submit" value="Envoyer">
</form>
</body>
</html>
