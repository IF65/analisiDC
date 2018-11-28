<?php
    @ini_set('memory_limit','2048M');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Database\Database;
    use Datacollect\Transazioni\Transazione;

    $timeZone = new \DateTimeZone('Europe/Rome');
    

    //debug
    $request = ['function' => 'creaFattura', 'sede' => '0110', 'data' => '2018-11-28', 'cassa' => '004', 'transazione' => '7047'];
    
    //$input = file_get_contents('php://input');
    //$request = json_decode($input, true);
    
   	if ( ! isset( $request ) ) {
        die;
    }
    
    if ($request['function'] == 'creaFattura') {
        echo creaFattura($request);
    }
    
    function creaFattura(array $request) {
            // variabili
            global $sqlDetails;
            
            
            $sede = $request['sede'];
            $data = $request['data'];
            $cassa = $request['cassa'];
            $scontrino = $request['transazione'];
        
            // costanti
            $ivaAliquota = [ 1 => 0, 2 => 4, 3 => 10, 4 => 22, 5 => 0, 6 => 0, 7 => 0, 8 => 0 ];
            $ivaDescrizione = [ 1 => 'IVA 0', 2 => 'IVA 4', 3 => 'IVA 10', 4 => 'IVA 22', 5 => 'IVA 5', 6 => 'ES.GIFT.92', 7 => 'ES.GIFT 93/94', 8 => 'ES. VARI' ];
            
            // carico il database
            $db = new Database($sqlDetails);
            
            $righeTransazione = [];
            
            //$result = exec ('perl /script/mtxGetTransactionList.pl -d 2018-11-23 -h 0110', $output );
            $result = exec("ssh root@10.11.14.78 \"perl /script/mtxGetTransaction.pl -d $data -h $sede -c $cassa -t $scontrino\"", $righeTransazione );
            //$result = exec("perl /script/mtxGetTransaction.pl -d $data -h $sede -c $cassa -t $scontrino", $righeTransazione );  
            
            $transazione = New Transazione($righeTransazione, $db);
            
            // creo la fattura
            $fattura = [];
            
            // leggo le righe
            $righeFattura = [];
            foreach ($transazione->vendite as $vendita) {
                $dettaglioVendita = $vendita->leggi();
                
                $dettaglioVendita['ivaAliquota'] = $ivaAliquota[$dettaglioVendita['ivaCodice']];
                $dettaglioVendita['ivaDescrizione'] = $ivaDescrizione[$dettaglioVendita['ivaCodice']];
                $dettaglioVendita['imponibileTotale'] = round($dettaglioVendita['importoTotale'] * 100 / ($dettaglioVendita['ivaAliquota'] + 100),2);
                $dettaglioVendita['impostaTotale'] =  round($dettaglioVendita['importoTotale'] -  $dettaglioVendita['imponibileTotale'],2) ;
                
                $righeFattura[] = $dettaglioVendita;
            }
            
            // calcolo i reparti iva
            $repartiIva = [];
            foreach ($righeFattura as $riga) {
                if (! key_exists($riga['ivaCodice'], $repartiIva)) {
                    $repartiIva[$riga['ivaCodice']]['imposta'] = $riga['impostaTotale'];
                    $repartiIva[$riga['ivaCodice']]['imponibile'] = $riga['imponibileTotale'];
                    $repartiIva[$riga['ivaCodice']]['descrizione'] = $ivaDescrizione[$riga['ivaCodice']];
                    $repartiIva[$riga['ivaCodice']]['aliquota'] = $ivaAliquota[$riga['ivaCodice']];
                } else {
                    $repartiIva[$riga['ivaCodice']]['imposta'] = round($repartiIva[$riga['ivaCodice']]['imposta'] + $riga['impostaTotale'],2);
                    $repartiIva[$riga['ivaCodice']]['imponibile'] = round($repartiIva[$riga['ivaCodice']]['imponibile'] + $riga['imponibileTotale'],2);
                }
            }
            
            // calcolo il totale
            $totaleFattura = 0;
            foreach ($righeFattura as $riga) {
                $totaleFattura += $riga['importoTotale'];
            }
            
            $fattura['sede'] = $sede;
            $fattura['data'] = $data;
            $fattura['cassa'] = $cassa;
            $fattura['transazione'] = $scontrino;
            $fattura['totaleFattura'] = round($totaleFattura,2);
            $fattura['repartiIva'] = $repartiIva;
            $fattura['righe'] = $righeFattura;
            
            return json_encode($fattura);
        }
?>

