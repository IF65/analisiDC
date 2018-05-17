<?php
    @ini_set('memory_limit','512M');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Datacollect\Datacollect;
    use Database\Database;

    $timeZone = new \DateTimeZone('Europe/Rome');

    // creo il database
	$db = new Database($sqlDetails);

    echo '- Caricamento prezzi locali      : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n";
    //$prezziLocali = $db->anagdafi->ricerca(['codiceNegozio' => '3152', 'data' => '2018-04-07']);
    $prezziLocali = [];

    // carico un datacollect
    echo '- Caricamento datacollect        : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n\n";
    $test = new Datacollect(realpath(__DIR__ . '/..')."/examples/data/0103_20180513_180513_DC.TXT", $db);

    // mostro le informazioni
    //$test->mostraInformazioni($prezziLocali['data'], $db->dimensioni['data'], $db->articoli['data'], $db->barcode['data']);
    
    $test->stampaTransazioni();
?>

