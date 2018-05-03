<?php
    require '../vendor/autoload.php';
    include('../src/Database/bootstrap.php');
    
    require_once '../src/Dc/Dc.php';
    
    use Dc\Dc;
    use Database\Tables\Anagdafi;

    $test = new Dc("../examples/data/3671_20171210_171210_DC.TXT");
    $test->mostraInformazioni();

	$anagdafi = new Anagdafi($sqlDetails);
    
    $prezzi = $anagdafi->prezziDelGiorno('2018-05-02', '0101');
    print_r($prezzi);
	
?>
