<?php
    @ini_set('memory_limit','512M');

    require '../vendor/autoload.php';
    require('../src/Database/bootstrap.php');
    require('../src/Datacollect/bootstrap.php');

    //require_once '../src/Datacollect/Datacollect.php';

    use Datacollect\Datacollect;
    use Database\Database;

	$db = new Database($sqlDetails);

    //$elencoPrezziLocali = $db->prezziDelGiorno('2018-05-02', '0102');
    $prezziLocali =[];
    /*foreach ($elencoPrezziLocali['data'] as $prezzoLocale) {
        $prezziLocali[$prezzoLocale['codice']] = $prezzoLocale;
    }
    unset($elencoPrezziLocali);*/

    $elencoArticoli = $db->anagraficaArticoli();
    $articoli = [];
    foreach ($elencoArticoli['data'] as $articolo) {
        $articoli[$articolo['codice']] = $articolo;
    }
    unset($elencoArticoli);

    $elencoBarcode = $db->barcode();
    $barcode = [];
    foreach ($elencoBarcode['data'] as $ean) {
        $barcode[$ean['barcode']] = $ean;
    }
    unset($elencoBarcode);

    $test = new Datacollect("../examples/data/3152_20180407_180407_DC.TXT");
    $test->mostraInformazioni($prezziLocali, $articoli, $barcode);
?>

