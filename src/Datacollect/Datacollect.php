<?php
    namespace Datacollect;

    use Datacollect\Transazioni\Transazione;

    class Datacollect {
        protected $db = null;
        protected $elencoArticoli = null;
        protected $elencoBarcode= null;

        private $data = null;
        private $negozio = null;
        
        // tutti le variabili sono valorizzate tenendo conto solo delle transazioni valide.
        private $numeroRighe = 0; // righe del file di testo
        private $numeroReferenze = 0;
        private $numeroTransazioni = 0; // sono contati solo
        private $numeroTransazioniNimis = 0;
        private $totale = 0;
        private $totaleNimis = 0;
        private $numeroPezzi = 0;

        private $transazioni = [];
        private $plu = [];
        private $formePagamento = [];
        private $totaleFormePagamento = 0;
        private $repartiIva = [];
        private $totaleRepartiIva = 0;

        function __construct(string $fileName, &$db = null) {
            try {
                $this->db = $db;
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

        private function caricaTransazioni($righe) {
            foreach ($righe as $riga) {
                if (preg_match('/^\d{4}:\d{3}:\d{6}:\d{6}:\d{4}:\d{3}:.:1/', $riga)) {
                    if (preg_match('/^.{31}:H:1/', $riga, $matches)) {
                        $righeTransazione = [$riga];
                    } elseif (preg_match('/^.{31}:F:1/', $riga)) {
                        $righeTransazione[] = $riga;
                        $this->transazioni[] = New Transazione($righeTransazione, $this->db);
                    } else {
                        $righeTransazione[] = $riga;
                    }
                }
            }
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
                    $this->totaleFormePagamento += $importo;
                }
                
                // determino i reparti iva
                foreach ($transazione->repartiIva as $repartoIva => $importo) {
                    if (array_key_exists($repartoIva, $this->repartiIva)) {
                        $this->repartiIva[$repartoIva] += $importo;
                    } else {
                        $this->repartiIva[$repartoIva] = $importo;
                    }
                    $this->totaleRepartiIva += $importo;
                }
            }
            ksort($this->formePagamento, SORT_STRING);
        }
        
        public function stampaTransazioni() {
            foreach ($this->transazioni as $transazione) {
                echo sprintf("%s\n", str_repeat('-',92));
                echo sprintf("negozio: %4s,  data: %10s,  cassa/trans.: %3s/%4s,  carta: %s\n", $transazione->negozio, $transazione->data, $transazione->cassa, $transazione->numero, $transazione->carta);
                echo sprintf("%s\n", str_repeat('-',92));

                foreach ($transazione->vendite as $vendita) {
                    echo sprintf("%3s %4s * %-13.13s %-7.7s %-25.25s %5d %8.2f %8.2f\n", $transazione->cassa, $transazione->numero, $vendita->plu, $vendita->articoloCodice, $vendita->articoloDescrizione, $vendita->quantita, $vendita->importoUnitario,  $vendita->importoTotale);

                    foreach($vendita->benefici as $tipo => $beneficio) {
                        if ($tipo == '0493') {
                            echo sprintf( "%73s %8.2f\n", $tipo, $transazione->benefici[$beneficio]->sconto );
                        }
                    }
                }

                echo "\n";
            }
        }

        public function mostraInformazioni(&$prezziLocali, &$dimensioni, &$articoli, &$barcode) {
            echo "- TOTALI:\n";
            echo sprintf("numero righe       : %32s\n", number_format ( $this->numeroRighe , 0 , "," , "." ));
            echo sprintf("transazioni Nimis  : %32s\n", number_format ( $this->numeroTransazioniNimis , 0 , "," , "." ));
            echo sprintf("transazioni Totali : %32s\n", number_format ( $this->numeroTransazioni , 0 , "," , "." ));
            echo sprintf("importo Nimis      : %32s\n", number_format ( $this->totaleNimis , 2 , "," , "." ));
            echo sprintf("importo Totale     : %32s\n", number_format ( $this->totale , 2 , "," , "." ));
            
            echo "\n";
            echo "- FORME DI PAGAMENTO:\n";
            foreach( $this->formePagamento as $formaPagamento => $importo ) {
                echo sprintf("codice: %3s importo: %32s\n", $formaPagamento, number_format ( $importo , 2 , "," , "." ));
            }
            echo sprintf("%11s  totale: %32s\n", '', number_format ( $this->totaleFormePagamento , 2 , "," , "." ));
            echo "\n";
            
            echo "\n";
            echo "- REPARTI IVA:\n";
            foreach( $this->repartiIva as $repartoIva => $importo ) {
                echo sprintf("codice: %3s importo: %32s\n", $repartoIva, number_format ( $importo , 2 , "," , "." ));
            }
            echo sprintf("%11s  totale: %32s\n", '', number_format ( $this->totaleRepartiIva , 2 , "," , "." ));
            echo "\n";
            
            if ($barcode != null and $dimensioni != null and false) {
                echo "- RIPARTIZIONE REPARTI;\n";
                foreach( $this->transazioni as $key => $row) {
                    $codice = '';
                    if(array_key_exists($key,$barcode)) {
                        $codice = $barcode[$key]['articoloCodice'];
                    } else {
                        if(array_key_exists(substr($key,0,7),$barcode)) {
                            $codice = $barcode[substr($key,0,7)]['articoloCodice'];
                        }
                    }

                    $codiceReparto = 1;
                    if(array_key_exists($codice,$dimensioni)) {
                            $codiceReparto = $articoli[$codice]['codiceReparto'];
                    }

                    $quantita = number_format ( $row['quantita'] , 3 , "," , "." );
                }
            }
            
            if ($barcode != null and false) {
                echo sprintf("|%s|\n", str_repeat("-",78));
                foreach( $this->plu as $key => $row) {
                    $codice = '';
                    if(array_key_exists($key,$barcode)) {
                        $codice = $barcode[$key]['articoloCodice'];
                    } else {
                        if(array_key_exists(substr($key,0,7),$barcode)) {
                            $codice = $barcode[substr($key,0,7)]['articoloCodice'];
                        }
                    }

                    $descrizione = '';
                    if(array_key_exists($codice,$articoli)) {
                            $descrizione = $articoli[$codice]['articoloDescrizione'];
                    }

                    $quantita = number_format ( $row['quantita'] , 3 , "," , "." );
                    echo sprintf("| %-13s | %07s | %-35s | %32s |\n", $key, $codice, $descrizione, $quantita);
                }
                echo sprintf("|%s|\n", str_repeat("-",78));
            }
        }
        
        static function mtx2dc(array $righe) {
            $dc = [];
            
            foreach ($righe as $riga) {
                $REG = $riga['REG'];
                $STORE = $riga['STORE'];
                $DDATE = $riga['DATE'];
                $TTIME = $riga['TTIME'];
                $SEQUENCENUMBER = $riga['SEQUENCENUMBER'];
                $TRANS = $riga['TRANS'];
                $TRANSSTEP = $riga['TRANSSTEP'];
                $RECORDTYPE = $riga['RECORDTYPE'];
                $RECORDCODE = $riga['RECORDCODE'];
                $USERNO = $riga['USERNO'];
                $MISC = $riga['MISC'];
                $DATA = $riga['DATA']; 
                
                $MIXED_FIELD = sprintf('%04d',$USERNO).':'.$MISC.$DATA;
                
                if (preg_match('/z/', $RECORDTYPE)) {
                    if (preg_match('/^(..\:)(.*)$/', $MISC, $matches)) {
                        $MIXED_FIELD = '00'.$matches[1].$matches[2].$DATA.'000';
                    }
                }
                
                if (preg_match('/m/', $RECORDTYPE)) {
                    if (preg_match('/^(..\:)(.*)$/', $MISC, $matches)) {
                        $MIXED_FIELD = '  '.$matches[1].$matches[2].$DATA.'   ';
                        if (preg_match('/^....:(0492.*)$/', $MIXED_FIELD, $matches)) {
                            $MIXED_FIELD = '0000:'.$matches[1];
                        }
                    }
                }
                $dc[] = sprintf('%04s:%03d:%06s:%06s:%04d:%03d:%1s:%03s:',$STORE,$REG,$DDATE,$TTIME,$TRANS,$TRANSSTEP,$RECORDTYPE,$RECORDCODE).$MIXED_FIELD;
            }
            
            return $dc;
        }

        public function esportaEpipoli() {
            
        }

        public function scriviDatacollect(string $path, string $fileName) {
            if (file_exists($path)) {
                $dc = [];
                foreach ($this->transazioni as $transazione) {
                    $dc = array_merge( $dc, $transazione->righeTransazione() );
                }

                file_put_contents($path.$fileName, implode("\r\n", $dc));
            }
        }
        
        function __destruct() {}
    }
?>
