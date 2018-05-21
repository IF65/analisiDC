<?php
    @ini_set('memory_limit','512M');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Datacollect\Datacollect;
    use Database\Database;

    $timeZone = new \DateTimeZone('Europe/Rome');
    
    echo '- Inizio elaborazione            : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n\n";
    $db = new Database($sqlDetails);

    echo '- Caricamento prezzi locali      : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n";
    //$prezziLocali = $db->ricercaPrezziLocali(['codiceNegozio' => '0133', 'data' => '2018-05-17']);
    
    echo '- Caricamento datacollect        : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n\n";
    $test = new Datacollect(realpath(__DIR__ . '/..')."/examples/data/0133_20180517_180517_DC.TXT", $db);
    
    echo '- Stampa transazioni             : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n\n";
    $test->stampaTransazioni();
    
    echo '- Fine elaborazione              : '.(new \DateTime())->setTimezone($timeZone)->format('H:i:s')."\n\n";
?>

