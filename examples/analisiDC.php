<?php
    require '../vendor/autoload.php';
    
    include_once("../Dc/Dc.php");
   
    use Dc\Dc  ;

    $test = new Dc("../examples/3671_20171210_171210_DC.TXT");
    $test->mostraInformazioni();
    
    
?>
