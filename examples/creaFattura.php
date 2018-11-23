<?php
    @ini_set('memory_limit','512M');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Database\Database;

    $timeZone = new \DateTimeZone('Europe/Rome');
    
    echo '- Inizio elaborazione            : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n\n";
    $db = new Database($sqlDetails);
    
    $output = [];
	
	//$result = exec ('perl /script/mtxGetTransactionList.pl -d 2018-11-23 -h 0110', $output );
	$result = exec ('perl /script/mtxGetTransaction.pl -d 2018-11-23 -h 0110 -c 5 -t 6083', $output );
	print_r($output);
?>

