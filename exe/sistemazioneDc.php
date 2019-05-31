<?php
    @ini_set('memory_limit','8192M');
    ini_set('error_log', '../log/error.log');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath( __DIR__ . '/..' ) . '/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Database\Database;
    use Datacollect\Datacollect;

    $timeZone = new DateTimeZone( 'Europe/Rome' );
    $db = new Database( $sqlDetails );

    $dc = new Datacollect(  "/Users/if65/Desktop/periodi/corrente/ncr/0106_20190529_190529_DC.TXT", $db);

    $dc->scriviDatacollect("/Users/if65/Desktop/", "test.txt");


    echo "\n";