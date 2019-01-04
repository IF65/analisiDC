<?php
    @ini_set('memory_limit','2048M');

    require(realpath(__DIR__ . '/..').'/vendor/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Database/autoload.php');
    require(realpath(__DIR__ . '/..').'/src/Datacollect/autoload.php');

    use Database\Database;
    use Datacollect\Datacollect;
    use Datacollect\Fatture\Fattura;

    $timeZone = new \DateTimeZone('Europe/Rome');

    if ($debug) {
        $request = ['function' => 'creaFattura', 'sede' => '9901', 'data' => '2019-01-04', 'cassa' => '001', 'transazione' => '3730'];
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
        
        // carico il database
        $db = new Database($sqlDetails);
        
        // recupero il dc
        $client = new GuzzleHttp\Client();
        
        $url = 'http://10.11.14.77/eFatture/eFatture.php';
        $headers = array('Content-Type: application/json');
        $requestData = ['function' => 'getTransaction', 'sede' => $request['sede'], 'data' => $request['data'], 'cassa' => $request['cassa'], 'transazione' => $request['transazione']];
        
        $request = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));
        
        $response = $client->send($request, ['timeout' => 60]);
        if ($response->getStatusCode() == 200) {
            $responseJson = $response->getBody()->getContents();
            $responseObj = json_decode($responseJson, true);
            $datacollect = Datacollect::mtx2dc($responseObj['datacollect']);
            
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

