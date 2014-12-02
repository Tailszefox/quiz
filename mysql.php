<?php
$config = file('config.txt', FILE_IGNORE_NEW_LINES);
mysql_connect($config[0], $config[1], $config[2]);
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET NAMES utf8");
mysql_select_db("quiz");
?>
