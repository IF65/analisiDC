<?php

$filename = realpath(__DIR__ . '/..').'/debug.php';
if (file_exists($filename)) {
	include( $filename );
} else {
	$debug = true;
}

// Attiva la visualizzazione degli errori in fase di debug
if ($debug) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

$sqlDetails = array(
	"user" 		=> "root",
	"password" 	=> "mela",
	"host" 		=> "10.11.14.78",
	"port" 		=> "",
	"db"   		=> ['archivi', 'dc', 'dimensioni'],
	"dsn" 		=> "",
	"pdoAttr" 	=> array()
);

$dcDetails = array(
	"dcFolder" => "/data/datacollect"
);

?>
