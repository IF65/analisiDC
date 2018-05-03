<?php

// Attiva la visualizzazione degli errori in fase di debug (rimuovere in produzione)
error_reporting(E_ALL);
ini_set('display_errors', '1');

$sqlDetails = array(
	"user" 		=> "root",
	"password" 	=> "mela",
	"host" 		=> "10.11.175.228",
	"port" 		=> "",
	"db"   		=> ['archivi', 'dc', 'dimensioni'],
	"dsn" 		=> "",
	"pdoAttr" 	=> array()
);

?>
