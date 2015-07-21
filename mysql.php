<?php
require("config.php");
mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET NAMES utf8");
mysql_select_db("quiz");
?>
