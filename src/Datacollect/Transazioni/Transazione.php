<?php
    namespace Datacollect\Transazioni;
    
    use Datacollect\Transazioni\Componenti\Vendita;
    use Datacollect\Transazioni\Componenti\Beneficio;

    class Transazione {
        private $righe = array();
        protected $db;

        public $societa = '';
        public $negozio = '';
        public $cassa = '';
        public $data = '';
        public $ora = '';
        public $numero = '';
        public $totale = 0;
        public $numeroPezzi = 0;
        public $carta = '';
        public $nimis = false;
        public $numeroRighe = 0;

        public $vendite = [];
        public $benefici = [];
        public $formePagamento = [];
        public $repartiIva = [];

        function __construct(array $righe, &$db) {
            $this->db = $db;    
            $this->righe = $righe;
            $this->normalizzaTransazione();
            $this->carica();
            $this->eliminaErroriAsar();
        }
        
        protected function cercaVendita(array $parametri, $ricercaEsatta = false) {
            for ($i=0; $i<count($this->vendite); $i++) {
                if ($this->vendite[$i]->confronta($parametri, $ricercaEsatta)) {
                    return $i;
                }
            }
            return -1;
        }
        
        protected function convalidaTransazione() {
            $totale = 0;
            foreach ($this->vendite as $vendita) {
                $totale += $vendita->importoTotale;
            }
            foreach ($this->benefici as $beneficio) {
                $totale += $beneficio->sconto;
            }
            
            $totaleFormePagamento = 0;
            foreach ($this->formePagamento as $formaPagamento => $importo) {
                $totaleFormePagamento += $importo;
            }
                
            $totaleRepartiIva = 0;
            foreach ($this->repartiIva as $repartoIva => $importo) {
                $totaleRepartiIva += $importo;
            }
            
            if ((round($totaleRepartiIva,2) != round($totaleFormePagamento,2)) or (round($totaleRepartiIva,2) != round($totale,2)) or (round($totale,2) != round($this->totale,2))) {
                echo "- ANOMALIA TRANS. $this->cassa/$this->numero\n";
                echo sprintf("totale      : %12s\n", number_format ( $this->totale , 2 , "," , "." ));
                echo sprintf("totale calc.: %12s\n", number_format ( $totale , 2 , "," , "." ));
                echo sprintf("forme pag.  : %12s\n", number_format ( $totaleFormePagamento , 2 , "," , "." ));
                echo sprintf("reparti iva : %12s\n\n", number_format ( $totaleRepartiIva , 2 , "," , "." ));
            }
        }
        
        protected function normalizzaTransazione() {
            //$this->righe = preg_grep("/^.{31}:i:.01:/", $this->righe, PREG_GREP_INVERT);
            //$this->righe = array_values( $this->righe);
        }

        protected function eliminaErroriAsar() {
            //storno articolo a cui si aggiunge un bollone senza relativo sconto
            for($i = 0; $i < count($this->righe); $i++) {
                if (preg_match( '/:S:17/', $this->righe[$i] )) {
                    if (preg_match( '/:S:17.{10}9980/', $this->righe[$i + 3] )) {
                        array_splice( $this->righe, $i + 3, 3 );
                    }
                }
            }
        }
        
        protected function carica() {
            // testata transazione
            if (preg_match('/^(\d{2})(\d{2}):(\d{3}):(\d{2})(\d{2})(\d{2}):(\d{2})(\d{2})(\d{2}):(\d{4}):(\d{3}):H:1/', $this->righe[0], $matches)) {
                $this->societa = $matches[1];
                $this->negozio = $matches[1].$matches[2];
                $this->cassa = $matches[3];
                $this->data = '20'.$matches[4].'-'.$matches[5].'-'.$matches[6];
                $this->ora = $matches[7].':'.$matches[8].':'.$matches[9];
                $this->numero = $matches[10];
            }
            
            // ora suddivido le righe nei vari tipi di movimento e carico alcuni dati di testata
            $righeVendita = [];
            $righeBeneficio = [];
            $tipoIva = [];
            foreach ($this->righe as $riga) {
                // numero di righe del datacollect
                if (preg_match('/^.{31}:.:1/', $riga)) {
                    $this->numeroRighe += 1;
                }

                // carta nimis
                if (preg_match('/^.{31}:k:1.{11}(\d{3})(\d{10})/', $riga, $matches)) {
                    $this->carta = $matches[1].$matches[2];
                    if($matches[1] == '046') {
                        $this->nimis = true;
                    }
                }

                // totale transazione
                if (preg_match('/^.{31}:F:1.{27}((?:\+|\-)\d{5})((?:\+|\-)\d{9})$/', $riga, $matches)) {
                    $this->pezzi = $matches[1]*1;
                    $this->totale = $matches[2]/100;
                }

                // seleziono le righe vendita
                if (preg_match('/^.{31}:(S|i):1/', $riga)) { //S + i + i
                    $righeVendita[] = $riga;
                }
                
                // seleziono le righe beneficio
                if (preg_match('/^.{31}:(C|D|G|d|m|w):1/', $riga) or preg_match('/^.{31}:S:1.{11}998011/', $riga)) {
                    $righeBeneficio[] = $riga;
                }

                // carico le forme di pagamento (carico direttamente vista la semplicita' dell'informazione)
                if (preg_match('/^.*:T:1.{25}(\d\d).{6}((?:\+|\-)\d{9})$/', $riga, $matches)) {
                    if (array_key_exists($matches[1], $this->formePagamento)) {
                        $this->formePagamento[$matches[1]] += $matches[2]/100;
                    } else {
                        $this->formePagamento[$matches[1]] = $matches[2]/100;
                    }
                }
            }
            
            // carico le vendite
            $esitoCaricamentoVendite = function() use(&$righeVendita, &$tipoIva, &$contatoreVendita) {
                if (count($righeVendita) and preg_match('/^.{31}:S:(\d)(\d)(\d):(\d{4}):.{3}(.{13})((?:\+|\-)\d{4})(\d|\.)(\d{3})(\+|\-|\*)(\d{9})$/', $righeVendita[0], $matches)) {
                    $parametri['codice1'] = $matches[1];
                    $parametri['codice2'] = $matches[2];
                    $parametri['codice3'] = $matches[3];
                    $parametri['repartoCassa'] = $matches[4];
                    $parametri['plu'] = trim($matches[5]);
                    if ('.' == $matches[7]) {
                        $parametri['quantita'] = ($matches[6].'.'.$matches[8])*1;
                        $parametri['unitaImballo'] = 0.0;
                        $parametri['pluPeso'] = true;
                        $parametri['plu'] = substr($parametri['plu'],0,7);
                    } else {
                        $parametri['pluPeso'] = false;
                        $parametri['quantita'] = $matches[6]*1;
                        $parametri['unitaImballo'] = $matches[8]/10;
                    }
                    if ('*' == $matches[9]) {
                        $parametri['importoUnitario'] = round($matches[10]/100,2);
                        $parametri['importoTotale'] = round($parametri['quantita']*$parametri['importoUnitario'],2);
                    } else {
                        $parametri['importoUnitario'] = round(($matches[9].$matches[10])/100,2);
                        $parametri['importoTotale'] = round($parametri['importoUnitario'],2);
                    }
                    $parametri['id'] = $contatoreVendita;
                  
                    array_splice($righeVendita, 0, 1);
                    
                    $contatoreVendita++;

                    if (count($righeVendita) and preg_match('/^.{31}:i:..0:.{26}(\d{7})/', $righeVendita[0], $matches)) {
                        $parametri['ivaCodice'] = $matches[1] * 1;
                        array_splice($righeVendita, 0, 1);

                        if (count($righeVendita) and preg_match('/^.{31}:i:..1:.{22}(\d{4})/', $righeVendita[0], $matches)) {
                            $parametri['numeroVendita'] = $matches[1] * 1;
                            array_splice($righeVendita, 0, 1);

                            $this->vendite[] = New Vendita($parametri, $this->db);

                            return true;
                        }
                    }
                }
                return false;
            };
            
            // chiamo la closure fino a che tutte le vendite siano state caricate
            $contatoreVendita = 1;
            while ($esitoCaricamentoVendite($righeVendita)) {}
            if (count($righeVendita) > 0) {// se aquesto punto l'array che contiene le righe vendita non è vuoto c'è un errore
                //echo "errore: $righeVendita[0]\n";
            }
            

            // carico i benefici
            $esitoCaricamentoBenefici = function() use(&$righeBeneficio) {
                for ( $i = 0 ; $i < count($righeBeneficio); $i++) {                   
                    // 0027: pago con nimis
                    /*if ((($i + 2) < count($righeBeneficio)) and preg_match('/:C:142:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0027', 'plu' => trim($matches[1]), 'quantita' => $matches[2]*1, 'sconto' => $matches[3]/100];
                        if (preg_match('/:G:131:\d{4}:P0:(.{13}):..((?:\-|\+)\d{5})((?:\+|\-)\d{9})$/', $righeBeneficio[$i + 1], $matches)) {
                            $parametri['punti'] = $matches[2]*1;
                            if (preg_match('/:m:1.{7}:0027/', $righeBeneficio[$i + 2])) {
                                array_splice($righeBeneficio, $i, 3);
                                $this->benefici[] = new Beneficio($parametri, $this->db);
                                return true;
                            }
                        }
                    }                  
                    
                    // 0493: sconto articolo semplice (questa parte di codice deve sempre essere dopo pago nimis)
                    if (preg_match('/:C:142:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0493', 'plu'  => trim($matches[1]), 'quantita' => $matches[2]*1, 'sconto' => $matches[3]/100];
                        array_splice($righeBeneficio, $i, 1);
                        $this->benefici[] = new Beneficio($parametri, $this->db);
                        return true;
                    }*/
                    
                    // 0493: sconto articolo (in questa bersione di sconto il reocrd :m: e' esplicito)
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:C:143:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0493', 'plu'  => trim($matches[1]), 'quantita' => $matches[2]*1, 'sconto' => $matches[3]/100];
                        if (preg_match('/:m:1.{7}:0493/', $righeBeneficio[$i + 1])) {
                            array_splice($righeBeneficio, $i, 2);
                            $beneficio = new Beneficio($parametri, $this->db);
                            $this->benefici[$beneficio->id] = $beneficio;
                            return true;
                        }
                    }
                    
                    // 0492: e convenienza
                    /**/
                }
                return false;
            };
            
            // chiamo la closure fino a che tutti benefici siano stati individuati
            while ($esitoCaricamentoBenefici($righeBeneficio)) {}
            if (count($righeBeneficio) > 0) {// se aquesto punto l'array che contiene le righe beneficio non è vuoto c'è un errore
               //echo "errore: $righeBeneficio[0]\n";
            }
            
            $this->associaVenditeBenefici();
            
        }
        
        private function associaVenditeBenefici() {
            foreach ($this->benefici as $beneficio) {
                // I benefici transazionali non hanno bsogno di alcuna associazione
                
                // PUNTI----------------------------------------------0022 INIZIO

                // PUNTI----------------------------------------------0022 FINE
                
                // PUNTI----------------------------------------------0505 INIZIO

                // PUNTI----------------------------------------------0505 FINE
                
                // SCONTO----------------------------------------------0492 INIZIO

                // SCONTO----------------------------------------------0492 FINE

                // SCONTO----------------------------------------------0493 INIZIO
                if ($beneficio->tipo == '0493') {
                    // questo beneficio è esclusivo percio' devo verificare che la vendita non sia già accoppiata a un altro beneficio
                    foreach($this->vendite as $vendita) {
                        if ($vendita->plu == $beneficio->plu and $vendita->quantita == $beneficio->quantita) {
                            if (! array_key_exists($beneficio->tipo, $vendita->benefici))  {
                                $vendita->benefici[$beneficio->tipo] = $beneficio->id;
                            }
                        }
                    }
                }
                // SCONTO----------------------------------------------0493 FINE
                
                // SCONTO+PUNTI----------------------------------------------0023, 0027 INIZIO

                // SCONTO+PUNTI----------------------------------------------0023, 0027 FINE
                
                // SET----------------------------------------------0055 INIZIO

                // SET----------------------------------------------0055 FINE
                
                // CATALINA REPARTO----------------------------------------------0481 INIZIO

                // CATALINA REPARTO----------------------------------------------0481 FINE
                
            }
        }

        public function righeTransazione():array {
            return $this->righe;
        }
        
        function __destruct() {}
    }
?>
