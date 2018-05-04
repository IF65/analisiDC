<?php
    namespace Dc;
    
    require_once(__DIR__."/Scontrino.php");
    
    use Dc\Scontrino;
    
    class Dc {
        
        // tutti le variabili sono valorizzate tenendo conto solo degli scontrini validi
        private $numeroRighe = 0; // rughe del file di testo
        private $numeroReferenze = 0;
        private $numeroScontrini = 0; // sono contati solo
        private $numeroScontriniNimis = 0;
        private $totale = 0;
        private $totaleNimis = 0;
        private $numeroPezzi = 0;
        
        private $scontrini = array();
        private $plu = array();
        private $formePagamento = array();
        
        function __construct(string $fileName) {
            try {
                $righe = $this->caricaRighe($fileName);
                if (count($righe) > 0) {
                    $this->caricaScontrini($righe);
                    if(count($this->scontrini) > 0) {
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
            foreach ($this->scontrini as $scontrino) {
                // informazioni di testata
                $this->numeroRighe += $scontrino->numeroRighe;
                $this->numeroScontrini++;
                $this->totale += $scontrino->totale;
                if ($scontrino->nimis) {
                    $this->numeroScontriniNimis++;
                    $this->totaleNimis += $scontrino->totale;
                }
                $this->numeroPezzi += $scontrino->numeroPezzi;
                
                // determino i plu venduti
                foreach ($scontrino->vendite as $vendita) {
                    if (array_key_exists($vendita->plu, $this->plu)) {
                        $this->plu[$vendita->plu]['quantita'] += $vendita->quantita;
                    } else {
                        $this->plu[$vendita->plu] = ['quantita' => $vendita->quantita];
                    }
                }
                ksort($this->plu, SORT_STRING);
                
                // determino le forme di pagamento
                foreach ($scontrino->formePagamento as $formaPagamento => $importo) {
                    if (array_key_exists($formaPagamento, $this->formePagamento)) {
                        $this->formePagamento[$formaPagamento] += $importo;
                    } else {
                        $this->formePagamento[$formaPagamento] = $importo;
                    }
                }
            }
            ksort($this->formePagamento, SORT_STRING);
        }
        
        private function caricaScontrini($righe) {
            foreach ($righe as $riga) {
                if (preg_match('/^\d{4}:\d{3}:\d{6}:\d{6}:\d{4}:\d{3}:.:1/', $riga)) {
                    if (preg_match('/^.{31}:H:1/', $riga, $matches)) {
                        $righeScontrino = [$riga];
                    } elseif (preg_match('/^.{31}:F:1/', $riga)) {
                        $righeScontrino[] = $riga;
                        $this->scontrini[] = New Scontrino($righeScontrino);
                    } else {
                        $righeScontrino[] = $riga;
                    }
                }
            }
        }
        
        public function mostraInformazioni(&$prezziLocali, &$articoli, &$barcode) {
            echo "- TOTALI:\n";
            echo sprintf("numero righe     : %7d\n", $this->numeroRighe);
            echo sprintf("scontrini        : %7d\n", $this->numeroScontrini);
            echo sprintf("scontrini Nimis  : %7d\n", $this->numeroScontriniNimis);
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