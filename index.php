<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<title>zOMG Quiz</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">

$(document).ready(function(){
		$('#formulaire').submit(function(){
				var pseudo = $('#pseudo').val();
				var password = $('#password').val();
				if(pseudo == '')
				{
					$('#entree').html("Vous devez entrer un pseudo, parce que bon, quand même !");
				}
				else if(password == '')
				{
					$('#entree').html("Il vous faut quand même un mot de passe, hein, quand même !");
				}
				else
				{
					$("#entree").html("Une petit seconde, nous vérifions que le pseudo que vous voulez n'est pas déjà pris...");
					$.post("participer.php", { pseudo: pseudo, password: password }, function(reponse){
							etat = $("etat", reponse).text();
							message = $("message", reponse).text();
							
							$('#entree').html(message);

							if(etat == 0)
							{
								//$('#pseudo').val('');
								//$('#password').val('');
							}
							else
							{
								setTimeout('location.href = "quiz.php"', 1000);
							}
					});
				}
				return false;
		});
		
		$.get('actuellement.php', {}, function(reponse){
				actuellement = $("actuellement", reponse).text();
				max = $("max", reponse).text();
				
				$('#actuellement').html(actuellement);
				
				if(max == 1)
				{
					$('#participer').css('visibility', 'hidden');
					$('#participer').html('');
				}
		});
});

</script>
</head>
<body>
<p id="titre">zOMG Quiz</p>

<div class="rubrique">
<span class="titre">Actuellement</span><br />
<span id="actuellement">Nous vérifions le nombre de participants...</span>
</div>

<div class="rubrique" id="participer">
<span class="titre">Participer</span>
<br />
<span id="entree">Si vous avez déjà un compte, entrez le pseudo et le mot de passe utilisés lors de sa création.<br />
Si vous n'avez pas de compte, il sera créé automatiquement avec les infos que vous entrerez. La prochaine fois, vous n'aurez alors qu'à les remettre pour pouvoir jouer 
et retrouver les points que vous avez gagnés lors de vos parties précédentes.</span><br />
<form method="post" id="formulaire">
<br />Pseudo : <br />
<input type="text" name="pseudo" id="pseudo">
<br />Mot de passe : <br />
<input type="password" name="password" id="password">
<br />
<input type="submit" name="participer" value="Participer !" id="participer">
</form>
</span>
</div>

<div class="rubrique">
<span class="titre">Règles</span>
<br />
Les règles de ce quiz sont très simples, vous allez voir.<br />
Jean-Pierre, le sympathique présentateur, s'occupera de vous poser les questions. Une fois l'énoncé d'une question donné, vous avez 20 secondes pour y répondre.<br />
Pendant ces 20 secondes, vous pouvez donner autant de réponses que vous voulez, mais seule la dernière que vous donnez sera prise en compte.<br /><br />
Une fois les 20 secondes écoulées, Jean-Pierre vérifie si quelqu'un a trouvé la bonne réponse. Si c'est le cas, cette personne reçoit des points en fonction du nombre d'indices donnés.
Dans le cas contraire, Jean-Pierre vous donne un indice, et vous pouvez réessayer.<br />
Si plusieurs personnes ont trouvé la bonne réponse, c'est évidemment celle qui l'a trouvée en premier qui remporte le point.<br />
Cependant, si au bout d'un moment Jean-Pierre voit que vous ramez vraiment et que personne ne trouve la bonne réponse, il passera à la question suivante histoire que le jeu continue.<br />
Notez que si vous jouez seul et que vous donnez une bonne réponse, vous ne gagnerez aucun point. Vous pouvez en gagner uniquement si vous jouez avec d'autres personnes.<br /><br />
Jean-Pierre est plutôt sympa, et n'exige pas que vous lui donniez la bonne réponse à la lettre près. Si vous faites une petite faute mais que vous êtes tout près de la réponse, ça passera quand même. 
Cependant, il pourrait arriver que Jean-Pierre accepte une réponse fausse, ou qu'il n'accepte pas une réponse bonne. Si c'est le cas, veuillez l'excuser, lui aussi peut faire des erreurs. 
<br /><br />
Voilà, c'est tout con. Et si vous avez encore des doutes, venez participer, vous comprendrez vite comment ça marche !
<br /><br />
Attention : si vous ne donnez aucune réponse pendant dix minutes, vous serez considéré comme inactif et votre pseudo sera effacé de la liste des participants, ceci afin d'éviter que des joueurs
qui ne font rien prennent de la place inutilement.<br />Pensez à répondre de temps en temps si vous ne voulez pas que ça arrive !
</div>

<div class="rubrique">
<span class="titre">Nouveautés / Mises à jour</span>
<br />
<ul>
<li>Il n'est désormais plus possible de gagner des points en jouant tout seul. Bien sûr, vous pouvez continuer à participer s'il n'y a personne d'autre, mais vous ne gagnerez aucun point en donnant une bonne réponse,
cela afin d'éviter que des gens viennent passer tout le week-end sur le quiz histoire de gagner des dizaines et des dizaines de points tout seul, ne laissant aucune chance aux autres de les rattraper.<br />
Maintenant, donc, si vous voulez des points, il faudra réellement affronter d'autres participants !</li>
<li>Pour que chaque joueur soit au même niveau, les points de tous les participants inscrits ont été remis à zéro. Désolé pour ceux qui ont passé du temps à les gagner, mais autrement, ceux qui ont gagné des centaines
de points en venant jouer seuls seraient très difficiles à rattraper à cause de la dernière mise à jour. Encore une fois, la seule façon de (re)gagner vos points sera de jouer contre d'autres personnes !</li>
<li>Il n'est plus possible de jouer avec deux pseudos différents en même temps, afin d'éviter la tricherie en rapport avec la dernière mise à jour. Si vous tentez de jouer avec un autre pseudo alors que vous êtes déjà sur le quiz,
vous ne pourrez pas entrer. Il faudra auparavant vous déconnecter pour pouvoir jouer avec le nouveau pseudo que vous voulez utiliser.<br />
Notez que si vous tentez d'entrer dans le quiz avec le même pseudo que celui que vous utilisez alors que vous jouez déjà, cela fonctionnera, 
afin de permettre à ceux qui sont partis en oubliant de se déconnecter de jouer quand même. Notez que cela équivaut à vous déconnecter puis vous reconnecter, ce qui aura pour effet de remettre vos points actuels à 0.</li>
<li>Ajout d'une vérification du nombre de participants lors de l'entrée dans le quiz, en plus de celle faite lors du chargement de cette page. Si la salle n'était pas remplie au moment du chargement de cette page, mais qu'elle
se remplit entre temps, vous ne pourrez pas entrer et devrez attendre qu'un joueur s'en aille, alors que vous pouviez quand même participer avant.</li>
<li>Le focus est toujours automatiquement donné au champ de texte où entrer la réponse dès qu'il apparait, mais désormais uniquement si vous n'êtes pas en train de taper quelque chose sur le chat. 
Dans ce cas, le focus reste sur le champ de texte du chat.<br />
Donc, si vous voulez pouvoir taper votre réponse dès que le champ apparait, n'écrivez rien sur le chat s'il ne reste que quelques secondes avant la prochaine question !</li>
</ul>
Mises à jour précédentes :
<ul class="anciennes">
<li>Correction (du moins on espère) d'un bug gênant qui effaçait la partie en cours de route, relançait le compteur à 60 secondes et faisait passer directement à la question suivante sans afficher la bonne réponse
quand personne n'avait trouvé. Cette correction pourrait avoir entrainé d'autres bugs plus fourbes, alors ouvrez l'œil...</li>
<li>Correction d'un bug moins gênant dans le chat, qui se produisait lorsque deux personnes parlaient à la même seconde. Les lignes de chacun sont maintenant rangées dans l'ordre où elles ont été reçues même
si elles ont été envoyées à la même seconde.</li>
<li>Le chat, encore lui, affiche maintenant les 30 derniers messages au lieu de 8. Les derniers messages sont les plus récents, il faudra donc scroller jusqu'en bas pour les voir quand vous entrerez sur la page du quiz.</li>
<li>Ajout de quelques smileys pour égayer le chat. À vous de trouver lesquels sont disponibles, mais vous ne devriez pas avoir trop de mal...</li>
<li>Ajout d'un classement des dix meilleurs joueurs, se basant sur le score général. Pour le consulter, cliquez sur le lien Classement à gauche de la fenêtre du quiz.</li> 
<li>Le focus est automatiquement donné au champ de texte où entrer la réponse dès que celui-ci apparait, pour que vous puissiez taper votre réponse dès que possible.</li>
</ul>
<ul class="anciennes">
<li><strong>Le système de réponse a changé : seule la dernière réponse que vous donnez est prise en compte, alors si vous donnez la bonne réponse puis que vous changez d'avis 
et donnez une mauvaise réponse, tant pis pour vous !</strong></li>
<li>Le quiz fait désormais le tour de toutes les questions disponibles avant de recommencer depuis le début, ce qui veut dire que vous ne retomberez plus deux fois sur la même question,
sauf si vous jouez assez longtemps pour toutes les avoir !<br />
Bien sûr, si vous avez déjà participé au quiz avant cette mise à jour, vous allez revoir passer certaines questions que vous connaissez, mais ce sera la dernière fois que vous les verrez avant un moment.</li>
<li>L'indice que Jean-Pierre donne après trois mauvaises réponses, à savoir la réponse mélangée, agit maintenant sur les mots et pas sur la réponse en entier. 
Chaque mot de la réponse est mélangé, mais leur emplacement reste le même, et leurs lettres ne sont plus mélangés avec celles des autres mots de la question. En espérant que ça soit clair...</li>
<li>Le score de chaque participant est maintenant sauvegardé. Cela veut dire que si vous quittez le jeu, ou si vous êtes déconnecté à cause d'un vilain bug, vous ne perdrez pas tous vos points durements acquis.<br />
Pour pouvoir se servir de tout ça, un système de compte a été mis en place. Il vous suffit d'entrer votre pseudo et un mot de passe la première fois que vous jouez, et par la suite, vous n'aurez qu'à utiliser ce même pseudo et
mot de passe pour pouvoir retrouver tous les points que vous aviez gagnés.</li>
<li>Parallèlement à cette mise à jour, les pupitres de chaque candidat affichent désormais deux scores : à gauche, le score de la partie qui est remis à zéro si le candidat s'en va, et à droite, le score général, c'est à dire
tous les points acquis par le candidat depuis qu'il est inscrit.</li>
</ul>
</div>
</body>
</html>
