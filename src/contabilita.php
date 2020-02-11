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
    $request = ['function' => 'datiContabili', 'sede' => '3151', 'data' => '2020-02-07'];
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

if ($request['function'] == 'datiContabili') {
    echo creaDatiContabili($request);
}

function creaDatiContabili(array $request) {
    // variabili
    global $sqlDetails;

    $datiContabili = [];

    $client = new GuzzleHttp\Client();

    $url = 'http://10.11.14.77/eDatacollect/eDatacollect.php';
    $headers = array('Content-Type: application/json');
    $requestData = ['function' => 'getDatiContabili', 'sede' => $request['sede'], 'data' => $request['data']];

    $request = new GuzzleHttp\Psr7\Request("POST", $url, $headers, json_encode($requestData));

    $response = $client->send($request, ['timeout' => 120]);
    if ($response->getStatusCode() == 200) {
        $obj = json_decode($response->getBody()->getContents(), true);

        $righe = $obj['datacollect'];

        // sistemo i dati e creo l'id della transazione
        foreach($righe as $index => $value) {
            if (preg_match('/^(15(?:1|2))$/',$righe[$index]['STORE'], $matches)) {
                $righe[$index]['STORE'] = '3' . $matches[1];
            } elseif (preg_match('/^(6\d\d)$/',$righe[$index]['STORE'], $matches)) {
                $righe[$index]['STORE'] = '3' . $matches[1];
            } else {
                $righe[$index]['STORE'] .= '0';
            }

            if (preg_match('/^(\d\d)(\d\d)(\d\d)$/', $righe[$index]['TTIME'], $matches)) {
                $righe[$index]['TTIME'] = $matches[1] . ':' . $matches[2] . ':' . $matches[3];
            }

            $righe[$index]['REG'] = str_pad($righe[$index]['REG'], 3, "0", STR_PAD_LEFT);
            $righe[$index]['TRANS'] = str_pad($righe[$index]['TRANS'], 4, "0", STR_PAD_LEFT);

            $righe[$index]['ID'] = $righe[$index]['STORE'] . str_replace('-', '', $righe[$index]['DDATE']) . $righe[$index]['REG'] . $righe[$index]['TRANS'];
        }

        // inizio creando gli scontrini. Non ho certezza dell'ordine con cui arrivano i dati perciò
        // spezzo l'operazione di caricamento in 4 parti: Testata, Piede, Imposta, Pagamento.
        foreach($righe as $riga) { //-->Testata (solo qui si creano le righe contabili)
            if (preg_match('/^1/', $riga['RECORDCODE'])) {
                if ($riga['RECORDTYPE'] == 'H') {
                    if (! key_exists($riga['ID'], $datiContabili)) {
                        $datiContabili[$riga['ID']] = [
                            'data' => $riga['DDATE'],
                            'transazione' => $riga['TRANS'],
                            'cassa' => $riga['REG'],
                            'sede' => $riga['STORE'],
                            'cassiere' => $riga['USERNO']
                        ];
                    }
                }
            }
        }
        foreach($righe as $riga) { //-->Piede
            if (preg_match('/^1/', $riga['RECORDCODE'])) {
                if ($riga['RECORDTYPE'] == 'F') {
                    if (key_exists($riga['ID'], $datiContabili)) {
                        $datiContabili[$riga['ID']]['totale'] = 0;
                        if (preg_match('/(.{10})$/', $riga['DATA'], $matches)) {
                            $datiContabili[$riga['ID']]['totale'] = $matches[1] / 100;
                        }
                    }
                }
            }
        }
        foreach($righe as $riga) { //-->Imposta
            if (preg_match('/^1/', $riga['RECORDCODE'])) {
                if ($riga['RECORDTYPE'] == 'V') {
                    if (key_exists($riga['ID'], $datiContabili)) {
                        IF (! key_exists('imposta', $datiContabili[$riga['ID']])) {
                            $datiContabili[$riga['ID']]['imposta'] = [];
                        }
                        $codiceImposta = substr($riga['RECORDCODE'],1,1);
                        $tipoImporto = substr($riga['RECORDCODE'],2,1);

                        if (! key_exists($codiceImposta, $datiContabili[$riga['ID']]['imposta'])) {
                            $datiContabili[$riga['ID']]['imposta'][$codiceImposta] = ['descrizione' => trim($riga['MISC'])];
                        }
                        if ($tipoImporto == '0') {
                            if (preg_match('/(.{10})$/', $riga['DATA'], $matches)) {
                                $datiContabili[$riga['ID']]['imposta'][$codiceImposta]['importo'] = $matches[1] / 100;
                            }
                        } else {
                            if (preg_match('/(.{10})$/', $riga['DATA'], $matches)) {
                                $datiContabili[$riga['ID']]['imposta'][$codiceImposta]['imponibile'] = $matches[1] / 100;
                            }
                        }
                    }
                }
            }
        }
        foreach($righe as $riga) { //-->Pagamento
            if (preg_match('/^1/', $riga['RECORDCODE'])) {
                if ($riga['RECORDTYPE'] == 'T') {
                    if (key_exists($riga['ID'], $datiContabili)) {
                        IF (! key_exists('pagamento', $datiContabili[$riga['ID']])) {
                            $datiContabili[$riga['ID']]['pagamento'] = [];
                        }
                        $tipoPagamento = substr($riga['DATA'],1,2);

                        $importo = 0;
                        if (preg_match('/(.{10})$/', $riga['DATA'], $matches)) {
                            $importo = $matches[1] / 100;
                        }

                        if (key_exists($tipoPagamento, $datiContabili[$riga['ID']]['pagamento'])) {
                            $datiContabili[$riga['ID']]['pagamento'][$tipoPagamento] += $importo;
                        } else {
                            $datiContabili[$riga['ID']]['pagamento'][$tipoPagamento] = $importo;
                        }
                    }
                }
            }
        }
    }

    return json_encode($datiContabili, JSON_PRETTY_PRINT );
}

?>

