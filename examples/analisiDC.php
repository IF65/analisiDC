<?php
    require '../vendor/autoload.php';
    
    require_once '../src/Dc/Dc.php';
    
    use Dc\Dc;

    $test = new Dc("../examples/data/3671_20171210_171210_DC.TXT");
    $test->mostraInformazioni();
    
    
?>
