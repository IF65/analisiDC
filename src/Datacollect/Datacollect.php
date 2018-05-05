<?php
    namespace Datacollect;

    use Datacollect\Transazioni\Transazione;

    class Datacollect {

        // tutti le variabili sono valorizzate tenendo conto solo degli transazioni validi
        private $numeroRighe = 0; // rughe del file di testo
        private $numeroReferenze = 0;
        private $numeroTransazioni = 0; // sono contati solo
        private $numeroTransazioniNimis = 0;
        private $totale = 0;
        private $totaleNimis = 0;
        private $numeroPezzi = 0;

        private $transazioni = array();
        private $plu = array();
        private $formePagamento = array();

        function __construct(string $fileName) {
            try {
                $righe = $this->caricaRighe($fileName);
                if (count($righe) > 0) {
                    $this->caricaTransazioni($righe);
                    if(count($this->transazioni) > 0) {
                        $this->recuperaInformazioni();
                    }
                }
            } catch (Exception $e) {
                die("Errore:,".$e->getMessage()."\n");
            }
        }

        final private function caricaRighe(string $fileName) {
            $righe = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (false === $righe) {
                throw new Exception(
                    sprintf("Errore leggendo il file %s", $fileName)
                );
            }
            return $righe;
        }

        private function recuperaInformazioni() {
            foreach ($this->transazioni as $transazione) {
                // informazioni di testata
                $this->numeroRighe += $transazione->numeroRighe;
                $this->numeroTransazioni++;
                $this->totale += $transazione->totale;
                if ($transazione->nimis) {
                    $this->numeroTransazioniNimis++;
                    $this->totaleNimis += $transazione->totale;
                }
                $this->numeroPezzi += $transazione->numeroPezzi;

                // determino i plu venduti
                foreach ($transazione->vendite as $vendita) {
                    if (array_key_exists($vendita->plu, $this->plu)) {
                        $this->plu[$vendita->plu]['quantita'] += $vendita->quantita;
                    } else {
                        $this->plu[$vendita->plu] = ['quantita' => $vendita->quantita];
                    }
                }
                ksort($this->plu, SORT_STRING);

                // determino le forme di pagamento
                foreach ($transazione->formePagamento as $formaPagamento => $importo) {
                    if (array_key_exists($formaPagamento, $this->formePagamento)) {
                        $this->formePagamento[$formaPagamento] += $importo;
                    } else {
                        $this->formePagamento[$formaPagamento] = $importo;
                    }
                }
            }
            ksort($this->formePagamento, SORT_STRING);
        }

        private function caricaTransazioni($righe) {
            foreach ($righe as $riga) {
                if (preg_match('/^\d{4}:\d{3}:\d{6}:\d{6}:\d{4}:\d{3}:.:1/', $riga)) {
                    if (preg_match('/^.{31}:H:1/', $riga, $matches)) {
                        $righeTransazione = [$riga];
                    } elseif (preg_match('/^.{31}:F:1/', $riga)) {
                        $righeTransazione[] = $riga;
                        $this->transazioni[] = New Transazione($righeTransazione);
                    } else {
                        $righeTransazione[] = $riga;
                    }
                }
            }
        }

        public function mostraInformazioni(&$prezziLocali, &$articoli, &$barcode) {
            echo "- TOTALI:\n";
            echo sprintf("numero righe     : %7d\n", $this->numeroRighe);
            echo sprintf("transazioni        : %7d\n", $this->numeroTransazioni);
            echo sprintf("transazioni Nimis  : %7d\n", $this->numeroTransazioniNimis);
            echo sprintf("importo          : %10.2f\n", $this->totale);
            echo sprintf("importo Nimis    : %10.2f\n", $this->totaleNimis);
            echo "\n";
            echo "- FORME DI PAGAMENTO:\n";
            foreach( $this->formePagamento as $formaPagamento => $importo ) {
                echo sprintf("codice: %3s importo: %10.2f\n", $formaPagamento, $importo);
            }
            echo "\n";

            if ($barcode != null) {
                echo sprintf("|%s|\n", str_repeat("-",78));
                foreach( $this->plu as $key => $row) {
                    $codice = '';
                    if(array_key_exists($key,$barcode)) {
                        $codice = $barcode[$key]['codice'];
                    } else {
                        if(array_key_exists(substr($key,0,7),$barcode)) {
                            $codice = $barcode[substr($key,0,7)]['codice'];
                        }
                    }

                    $descrizione = '';
                    if(array_key_exists($codice,$articoli)) {
                            $descrizione = $articoli[$codice]['descrizione'];
                    }

                    $quantita = number_format ( $row['quantita'] , 3 , "," , "." );
                    echo sprintf("| %-13s | %07s | %-35s | %12s |\n", $key, $codice, $descrizione, $quantita);
                }
                echo sprintf("|%s|\n", str_repeat("-",78));
            }
        }

        function __destruct() {}
    }
?>
