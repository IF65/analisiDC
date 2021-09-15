<?php
    @ini_set('memory_limit','8192M');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Database\Database;
    use Datacollect\Datacollect;
    use Datacollect\Fatture\Fattura;

    $timeZone = new \DateTimeZone('Europe/Rome');

    if ($debug) {
        //$request = ['function' => 'creaFattura', 'sede' => '0173', 'data' => '2019-01-03', 'cassa' => '002', 'transazione' => '4522'];
        $request = ['function' => 'creaFattura', 'sede' => '0125', 'data' => '2021-08-29', 'cassa' => '004', 'transazione' => '7261'];
    } else {
        $input = file_get_contents('php://input');
        $request = json_decode($input, true);
    }
    
   	if ( ! isset( $request ) ) {
        die;
    }
    
    if ($request['sede'] == '9901') {
        $request['sede'] = '0134';
    }
    
    if ($request['function'] == 'creaFattura') {
        echo creaFattura($request);
    } else if ($request['function'] == 'elencoTransazioni') {
        echo elencoTransazioni($request);
    }
    
    function creaFattura(array $request) {
        // variabili
        global $sqlDetails;
        
        $result = [];
        
        // calcolo il nome del file nel caso i dati non siano piu' su mtx
        $dcPath = '/dati/datacollect/';
        $dcFileName = $request['sede'];
        $regex = "/^\d{4}:".$request['cassa'].':';
        if (preg_match('/^(\d{2})(\d{2})\-(\d{2})\-(\d{2})$/', $request['data'], $matches)) {
            $dcPath .= $matches[1].$matches[2].$matches[3].$matches[4].'/';
            $dcFileName .= '_'.$matches[1].$matches[2].$matches[3].$matches[4].'_'.$matches[2].$matches[3].$matches[4].'_DC.TXT';
            $regex = "^\d{4}:".$request['cassa'].':'.$matches[2].$matches[3].$matches[4].':\d{6}:'.$request['transazione'];
        }
        
        $datacollect = [];
        if (file_exists($dcPath.$dcFileName)) {
            $dc= explode("\n",file_get_contents($dcPath.$dcFileName));
            $datacollect = preg_grep("/$regex/", $dc);
        }
        //$datacollect = [];
        
        // carico il database
        $db = new Database($sqlDetails);
        
        if (count($datacollect) == 0) {
            // recupero il dc da mtx
            $client = new GuzzleHttp\Client();
            
            /*$url = 'http://10.11.14.77/eFatture/eFatture.php';
            $headers = array('Content-Type: application/json');
            $requestData = ['function' => 'getTransaction', 'sede' => $request['sede'], 'data' => $request['data'], 'cassa' => $request['cassa'], 'transazione' => $request['transazione']];
            
            $request = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));
            
            $response = $client->send($request, ['timeout' => 120]);
            if ($response->getStatusCode() == 200) {
                $responseJson = $response->getBody()->getContents();
                $responseObj = json_decode($responseJson, true);
                $datacollect = Datacollect::mtx2dc($responseObj['datacollect']);
                
                $fattura = New Fattura($datacollect, $db);
                echo "\n";
            } else if ($response->getStatusCode() == 204) {
                http_response_code(204);
                return null;
            }*/

            $url = 'http://10.11.14.128/eDatacollect/src/eDatacollect.php';
            $headers = array('Content-Type: application/json');
            $requestData = ['function' => 'creazioneDatacollect', 'sede' => $request['sede'], 'data' => $request['data'], 'cassa' => $request['cassa'], 'transazione' => $request['transazione']];

            $request = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));

            $response = $client->send($request, ['timeout' => 120]);
            if ($response->getStatusCode() == 200) {
                $datacollect = explode("\r\n", $response->getBody()->getContents());

                $fattura = New Fattura($datacollect, $db);
            } else if ($response->getStatusCode() == 204) {
                http_response_code(204);
                return null;
            }
        } else {
            // il datacollect è già presente su file locale
            $fattura = New Fattura($datacollect, $db);
        }
        
        $result['sede'] = $fattura->sede;
        $result['data'] = $fattura->data;
        $result['cassa'] = $fattura->cassa;
        $result['transazione'] = $fattura->transazione;
        $result['totaleFattura'] = $fattura->totale;
        $result['repartiIva'] = $fattura->repartiIva;
        $result['righe'] = $fattura->righe;
        $result['importoSospeso'] = 0.0;
        
        return json_encode($result);
    }
    
    
    function elencoTransazioni(array $request) {
        // variabili
        global $sqlDetails;
            
        // recupero l'elenco
        $client = new GuzzleHttp\Client();
        
        $url = 'http://10.11.14.77/eFatture/eFatture.php';
        $headers = array('Content-Type: application/json');
        
        //$request['function'] = 'getTransactionList';
        $requestData = ['function' => 'getTransactionList', 'sede' => $request['sede'], 'data' => $request['data']];
        $postRequest = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));
        
        $response = $client->send($postRequest, ['timeout' => 20]);
        $responseJson = $response->getBody()->getContents();     
        
        return $responseJson;
    }
?>

