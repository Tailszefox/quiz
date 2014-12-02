<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<title>zOMG Quiz</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

require('./mysql.php');
$requete = 'SELECT * FROM questions WHERE id = 1374';
$reponse = mysql_query($requete)or die('Erreur SQL<br>'.$requete.'<br>'.mysql_error());

$donnees = mysql_fetch_array($reponse);

echo $donnees['question'].'<br />';
echo $donnees['reponse'].'<br />';

$reponse = strtolower($donnees['reponse']);

$bonne = "Détective privé";

echo $reponse;
echo '<br />';

echo $bonne == $reponse;

echo '<br />';
echo strtolower($_POST['r']);
echo '<br />';
echo strtolower($_POST['r']) == $reponse;

echo '<br />';
?>
<form method="post">
<input type="text" name="r">
</form>
</body>
</html>
