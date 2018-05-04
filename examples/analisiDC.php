<?php
    require '../vendor/autoload.php';
    include('../src/Database/bootstrap.php');
    
    require_once '../src/Dc/Dc.php';
    
    use Dc\Dc;
    use Database\Database;

    //$test = new Dc("../examples/data/3671_20171210_171210_DC.TXT");
    //$test->mostraInformazioni();

	$anagdafi = new Database($sqlDetails);
    
    $elencoPrezziLocali = $anagdafi->prezziDelGiorno('2018-05-02', '0102');
    
    $prezziLocali =[];
    foreach ($elencoPrezziLocali['data'] as $prezzoLocale) {
        $prezziLocali[$prezzoLocale['codice']] = $prezzoLocale;
    }
    print_r($prezziLocali);
    
	/*
select a.`COD-ART2` `codice`, a.`DES-ART2` `descrizione`, ifnull(d.`REPARTO_CASSE`,1) `reparto`, case when a.`COD-IVA-ART2` = 2100 then 2200 else a.`COD-IVA-ART2` end `codiceIva` ,a.`IVA-ART2` `aliquotaIva` from archivi.articox2 as a left join dimensioni.articolo as d on a.`COD-ART2`=d.`CODICE_ARTICOLO` order by 1;

select b.`CODCIN-BAR2` `codice`, b.`BAR13-BAR2` `barcode` from archivi.barartx2 as b order by 1; 

select b.`BAR13-BAR2`, count(*) from archivi.barartx2 as b group by 1 having count(*) > 1; 
*/
?>

