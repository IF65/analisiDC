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

    // carico in memoria le tabelle di lookup

    echo '- Caricamento articoli           : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n";
    $articoli = $db->articox2->ricerca([]);

    echo '- Caricamento barcode            : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n";
    $barcode = $db->barartx2->ricerca([]);

    echo '- Caricamento negozi             : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n";
    $negozi = $db->negozi->ricerca([]);

    echo '- Caricamento dimensioni articolo: '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n";
    $dimensioni = $db->dimensioni->ricerca([]);

    echo '- Caricamento prezzi locali      : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n";
    $prezziLocali = $db->anagdafi->ricerca(['codiceNegozio' => '3152', 'data' => '2018-04-07']);

    // carico un datacollect
    echo '- Caricamento datacollect        : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n\n";
    $test = new Datacollect(realpath(__DIR__ . '/..')."/examples/data/3152_20180407_180407_DC.TXT");

    // mostro le informazioni
    $test->mostraInformazioni($prezziLocali['data'], $articoli['data'], $barcode['data']);
?>

