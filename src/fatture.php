<?php
    @ini_set('memory_limit','2048M');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Database\Database;
    use Datacollect\Datacollect;
    use Datacollect\Transazioni\Transazione;

    $timeZone = new \DateTimeZone('Europe/Rome');
    
    //debug
    //$request = ['function' => 'elencoTransazioni', 'sede' => '0134', 'data' => '2018-12-03', 'cassa' => '002', 'transazione' => '0448'];
    
    $input = file_get_contents('php://input');
    $request = json_decode($input, true);

   	if ( ! isset( $request ) ) {
        die;
    }
    
    if ($request['function'] == 'creaFattura') {
        echo creaFattura($request);
    } else if ($request['function'] == 'elencoTransazioni') {
        echo elencoTransazioni($request);
    }
    
    function creaFattura(array $request) {
        // variabili
        global $sqlDetails;
        
        $sede = $request['sede'];
        $data = $request['data'];
        $cassa = $request['cassa'];
        $scontrino = $request['transazione'];
    
        // costanti
        $ivaAliquota = [ 0 => 0, 1 => 4, 2 => 10, 3 => 22, 4 => 0, 5 => 0, 6 => 0, 7 => 0 ];
        $ivaDescrizione = [ 0 => 'IVA 0', 1 => 'IVA 4', 2 => 'IVA 10', 3 => 'IVA 22', 4 => 'IVA 5', 5 => 'ES.GIFT.92', 6 => 'ES.GIFT 93/94', 7 => 'ES. VARI' ];
        
        // carico il database
        $db = new Database($sqlDetails);
        
        // recupero il dc
        $client = new GuzzleHttp\Client();
        
        $url = 'http://10.11.14.77/eFatture/eFatture.php';
        $headers = array('Content-Type: application/json');
        $requestData = ['function' => 'getTransaction', 'sede' => $request['sede'], 'data' => $request['data'], 'cassa' => $request['cassa'], 'transazione' => $request['transazione']];
        
        $request = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));
        
        $response = $client->send($request, ['timeout' => 20]);
        $responseJson = $response->getBody()->getContents();
        $responseObj = json_decode($responseJson, true);
        $datacollect = Datacollect::mtx2dc($responseObj['datacollect']);
        
        $transazione = New Transazione($datacollect, $db);
        
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
    
    
    function elencoTransazioni(array $request) {
        // variabili
        global $sqlDetails;
            
        // recupero l'elenco
        $client = new GuzzleHttp\Client();
        
        $url = 'http://10.11.14.77/eFatture/eFatture.php';
        $headers = array('Content-Type: application/json');
        
        //$request['function'] = 'getTransactionList';
        $requestData = ['function' => 'getTransactionList', 'sede' => $request['sede'], 'data' => $request['data'], 'cassa' => $request['cassa'], 'transazione' => $request['transazione']];
        $postRequest = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));
        
        $response = $client->send($postRequest, ['timeout' => 20]);
        $responseJson = $response->getBody()->getContents();     
        
        return $responseJson;
    }
?>

