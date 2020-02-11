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
    $request = ['function' => 'creaDatiContabili', 'sede' => '3152', 'data' => '2020-02-07'];
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

if ($request['function'] == 'creaDatiContabili') {
    echo creaDatiContabili($request);
} else if ($request['function'] == 'recuperaDatacollect') {
    echo recuperaDatacollect($request);
}

function creaDatiContabili(array $request) {
    // variabili
    global $sqlDetails;

    $result = [];

    // calcolo il nome del file nel caso i dati siano già stati recuperati
    $dcPath = '/dati/datacollect/';
    $dcFileName = $request['sede'];
    if (preg_match('/^(\d{2})(\d{2})\-(\d{2})\-(\d{2})$/', $request['data'], $matches)) {
        $dcPath .= $matches[1].$matches[2].$matches[3].$matches[4].'/';
        $dcFileName .= '_'.$matches[1].$matches[2].$matches[3].$matches[4].'_'.$matches[2].$matches[3].$matches[4].'_DC.TXT';
    }

    $datacollect = [];
    if (file_exists($dcPath.$dcFileName)) {
        $dc= explode("\n",file_get_contents($dcPath.$dcFileName));
        $datacollect = preg_grep("/(?:H|F|T)/", $dc);
    }

    if (! count($datacollect)) {
        // recupero il dc da mtx
        $client = new GuzzleHttp\Client();

        $url = 'http://10.11.14.77/eDatacollect/eDatacollect.php';
        $headers = array('Content-Type: application/json');
        $requestData = ['function' => 'getDatiContabili', 'sede' => $request['sede'], 'data' => $request['data']];

        $request = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));

        $response = $client->send($request, ['timeout' => 120]);
        if ($response->getStatusCode() == 200) {
            $responseJson = $response->getBody()->getContents();

        } else if ($response->getStatusCode() == 204) {
            http_response_code(204);
            return null;
        }
    }

    if (count($datacollect)) {

    }

    return $responseJson;
}

function recuperaDatacollect(array $request) {
    // variabili
    global $sqlDetails;

    $result = [];

    // calcolo il nome del file nel caso i dati siano già stati recuperati
    $dcPath = '/dati/datacollect/';
    $dcFileName = $request['sede'];
    if (preg_match('/^(\d{2})(\d{2})\-(\d{2})\-(\d{2})$/', $request['data'], $matches)) {
        $dcPath .= $matches[1].$matches[2].$matches[3].$matches[4].'/';
        $dcFileName .= '_'.$matches[1].$matches[2].$matches[3].$matches[4].'_'.$matches[2].$matches[3].$matches[4].'_DC.TXT';
    }

    $datacollect = [];
    if (file_exists($dcPath.$dcFileName)) {
        $datacollect = explode("\n",file_get_contents($dcPath.$dcFileName));
    }

    if (! count($datacollect)) {
        // recupero il dc da mtx
        $client = new GuzzleHttp\Client();

        $url = 'http://10.11.14.77/eDatacollect/eDatacollect.php';
        $headers = array('Content-Type: application/json');
        $requestData = ['function' => 'getDatacollect', 'sede' => $request['sede'], 'data' => $request['data']];

        $request = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));

        $response = $client->send($request, ['timeout' => 120]);
        if ($response->getStatusCode() == 200) {
            $responseObj = json_decode($response->getBody()->getContents(), true);

        } else if ($response->getStatusCode() == 204) {
            http_response_code(204);
            return null;
        }
    }

    if ( key_exists('datacollect', $responseObj) and count($responseObj['datacollect'])) {
        foreach($responseObj['datacollect'] as $row) {
            $REG = $row['REG'];
            $STORE = $row['STORE'];
            $DDATE = $row['DATE'];
            $TTIME = $row['TTIME'];
            $SEQUENCENUMBER = $row['SEQUENCENUMBER'];
            $TRANS = $row['TRANS'];
            $TRANSSTEP = $row['TRANSSTEP'];
            $RECORDTYPE = $row['RECORDTYPE'];
            $RECORDCODE = $row['RECORDCODE'];
            $USERNO = $row['USERNO'];
            $MISC = $row['MISC'];
            $DATA = $row['DATA'];

            $MIXED_FIELD = sprintf( '%04d', $USERNO ) . ':' . $MISC . $DATA;

            if (preg_match("/z/", $RECORDTYPE)) {
                if (preg_match("/^(..\:)(.*)$/", $MISC, $matches)) {
                    $MIXED_FIELD = '00'.$matches[1].$matches[2].$DATA.'000';
                }
            }

            if (preg_match("/m/", $RECORDTYPE)) {
                if (preg_match( "/^(..\:)(.*)$/", $MISC, $matches)) {
                    $MIXED_FIELD = '  '.$matches[1].$matches[2].$DATA.'   ';
                    if (preg_match("/^....:(0492.*)$/", $MIXED_FIELD, $matches)) {
                        $MIXED_FIELD = '0000:'.$matches[1];
                    }
                }
            }

            $datacollect[] = sprintf( '%04s:%03d:%06s:%06s:%04d:%03d:%1s:%03s:', $STORE, $REG, $DDATE, $TTIME, $TRANS, $TRANSSTEP, $RECORDTYPE, $RECORDCODE ) . $MIXED_FIELD ;
        }

        return implode("\r\n", $datacollect);
    }

    return '';
}

?>

