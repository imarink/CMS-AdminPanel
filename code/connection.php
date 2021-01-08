<?php

// podaci za bazu
$dbhost = "localhost";
$dbuser = "youtube_face";
$dbpass = "fbhr#1234";
$dbname = "youtube_face";

// konektiranje na bazu
$db = new Database($dbhost, $dbuser, $dbpass, $dbname);
unset($dbhost, $dbuser, $dbpass, $dbname);
$db->query("SET NAMES utf8");
?>
