<?php
    include_once(__DIR__."/Dc/Dc.php");
   
    use Dc\Dc  ;

    $test = new Dc(__DIR__."/Dati/3671_20171210_171210_DC.TXT");
    $test->mostraInformazioni();

?>
