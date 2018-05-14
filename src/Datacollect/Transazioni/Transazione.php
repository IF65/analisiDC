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

        public $vendite = array();
        public $benefici = array();
        public $formePagamento = array();
        
        public $blocchi = array();

        function __construct(array $righe, &$db) {
            $this->db = $db;    
            $this->righe = $righe;
            $this->carica();
        }
        
        protected function cercaVendita(array $parametri, $ricercaEsatta = false) {
            for ($i=0; $i<count($this->vendite); $i++) {
                if ($this->vendite[$i]->confronto($parametri, $ricercaEsatta)) {
                    return $i;
                }
            }
            return -1;
        }

        private function carica() {
            // testata transazione
            if (preg_match('/^(\d{2})(\d{2}):(\d{3}):(\d{2})(\d{2})(\d{2}):(\d{2})(\d{2})(\d{2}):(\d{4}):(\d{3}):H:1/', $this->righe[0], $matches)) {
                $this->societa = $matches[1];
                $this->negozio = $matches[1].$matches[2];
                $this->cassa = $matches[3];
                $this->data = '20'+$matches[4].'-'.$matches[5].'-'.$matches[6];
                $this->ora = $matches[7].':'.$matches[8].':'.$matches[9];
                $this->numero = $matches[10];
            }
            
            // ora suddivido le righe nei vari tipi di movimento e carico alcuni dati di testata
            $righeVendita = [];
            $righeBeneficio = [];
            $righeFormePagamento = [];
            $righeRepartiIva = [];
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

                // selezione le righe vendita
                if (preg_match('/^.{31}:S:1/', $riga)) {
                    if (! preg_match('/^.{31}:S:1.{11}998011/', $riga)) {
                        $righeVendita[] = $riga;
                    }
                }
                
                // seleziono le righe beneficio
                if (preg_match('/^.{31}:(C|D|G|d|m|w):1/', $riga) or preg_match('/^.{31}:S:1.{11}998011/', $riga)) {
                    $righeBeneficio[] = $riga;
                }                

                // seleziono le righe delle forme di pagamento
                if (preg_match('/^.*:T:1.{25}(\d\d).{6}((?:\+|\-)\d{9})$/', $riga, $matches)) {
                    if (array_key_exists($matches[1], $this->formePagamento)) {
                        $righeFormePagamento[] = $riga;
                    }
                }
                
                // seleziono le righe dei reparti iva
                if (preg_match('/^.*:V:1/', $riga, $matches)) {
                    $righeRepartiIva[] = $riga;
                }
            }
            
            // carico le vendite
            $esitoCaricamentoVendite = function() use(&$righeVendita) {
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
                    array_splice($righeVendita, 0, 1);
                    $indiceVendita = $this->cercaVendita($parametri);
                    if ($indiceVendita < 0) {//-1 == non trovato
                        $this->vendite[] = New Vendita($parametri, $this->db);
                    } else {
                        if (! $this->vendite[$indiceVendita]->sommaVendita($parametri)) {
                            echo "errore di caricamento\n";
                        }
                    }
                    return true;
                }
                return false;
            };
            
            // carico i benefici
            $esitoCaricamentoBenefici = function() use(&$righeBeneficio) {
                for ( $i = 0 ; $i < count($righeBeneficio); $i++) {                   
                    // pago con nimis
                    if ((($i + 2) < count($righeBeneficio)) and preg_match('/:C:142:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
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
                    
                    // sconto articolo semplice (deve sempre essere dopo pago nimis)
                    if (preg_match('/:C:142:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0493', 'plu'  => trim($matches[1]), 'quantita' => $matches[2]*1, 'sconto' => $matches[3]/100];
                        array_splice($righeBeneficio, $i, 1);
                        $this->benefici[] = new Beneficio($parametri, $this->db);
                        return true;
                    }
                    
                    // sconto articolo
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:C:143:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0493', 'plu'  => trim($matches[1]), 'quantita' => $matches[2]*1, 'sconto' => $matches[3]/100];
                        if (preg_match('/:m:1.{7}:0493/', $righeBeneficio[$i + 1])) {
                            array_splice($righeBeneficio, $i, 2);
                            $this->benefici[] = new Beneficio($parametri, $this->db);
                            return true;
                        }
                    }
                    
                    // e convenienza
                    if ((($i + 2) < count($righeBeneficio)) and preg_match('/^.{31}:S:1.{11}998011/', $righeBeneficio[$i])) {
                        if (preg_match('/:C:143:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}((?:\+|\-)\d{9})$/', $righeBeneficio[$i + 1], $matches)) {
                            $parametri = ['tipo' => '0492', 'plu'  => trim($matches[1]), 'quantita' => $matches[2]*1, 'sconto' => $matches[3]/100];
                            if (preg_match('/:m:1.{7}:0492/', $righeBeneficio[$i + 2])) {
                                array_splice($righeBeneficio, $i, 3);
                                $this->benefici[] = new Beneficio($parametri, $this->db);
                                return true;
                            }
                        }
                    }
                    
                    // sconto reparto
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:D:196:.{30}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0055', 'sconto' => $matches[1]/100];
                        
                        if (preg_match('/:m:1.{7}:0055/', $righeBeneficio[$i + 1])) {
                            $j = $i - 1;
                            $articoli = [];
                            while ($j >= 0 and preg_match('/:d:1.{7}:P0:(.{13})((?:\-|\+)\d{4}).{4}.(\d{9})$/', $righeBeneficio[$j], $matches)) {
                                $articoli[] = ['plu' => $matches[1], 'quantita' => $matches[2]*1, 'sconto' => $matches[2]*$matches[3]/100];
                                $j--;
                            }
                            $parametri['articoli'] = $articoli;
                            array_splice($righeBeneficio, $i-count($articoli), 2 + count($articoli));
                            $this->benefici[] = new Beneficio($parametri, $this->db);
                            return true;
                        }
                    }
                    
                    // sconto catalina transazione
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:D:197:.{30}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                         $parametri = ['tipo' => '0503', 'sconto' => $matches[1]/100];
                        
                        if (preg_match('/:w:1.{11}(.{13})/', $righeBeneficio[$i + 1], $matches)) {
                            $barcodeCatalina = $matches[1];
                            
                            array_splice($righeBeneficio, $i, 2);
                            $this->benefici[] = new Beneficio($parametri, $this->db);
                            return true;
                        }
                    }
                    
                    // sconto catalina a reparto
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:D:196:.{30}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0481', 'sconto' => $matches[1]/100];
                        
                        if (preg_match('/:w:1.{11}(.{13})/', $righeBeneficio[$i + 1])) {
                            $j = $i - 1;
                            $articoli = [];
                            while ($j >= 0 and preg_match('/:d:1.{7}:P0:(.{13})((?:\-|\+)\d{4}).{4}.(\d{9})$/', $righeBeneficio[$j], $matches)) {
                                $articoli[] = ['plu' => $matches[1], 'quantita' => $matches[2]*1, 'sconto' => $matches[2]*$matches[3]/100];
                                $j--;
                            }
                            $parametri['articoli'] = $articoli;
                            array_splice($righeBeneficio, $i-count($articoli), 2 + count($articoli));
                            $this->benefici[] = new Beneficio($parametri, $this->db);
                            return true;
                        }
                    }
                    
                    // sconto dipendenti transazione
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:D:198:.{30}((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $parametri = ['tipo' => '0061', 'sconto' => $matches[1]/100];
                        
                        if (preg_match('/:m:1.*0061/', $righeBeneficio[$i + 1])) {
                            array_splice($righeBeneficio, $i, 2);
                            $this->benefici[] = new Beneficio($parametri, $this->db);
                            return true;
                        }
                    }
                    
                    // premio
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:G:131:\d{4}:P0:(.{13}):00((?:\-|\+)\d{5})((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $barcode = $matches[1];
                        $punti = $matches[2];
                        $importo = $matches[3];
                        $parametri = ['tipo' => '0023', 'plu'  => trim($matches[1]), 'quantita' => $matches[2]*1, 'sconto' => $matches[3]/100];
                         
                        if (preg_match('/:m:1.{7}:0023/', $righeBeneficio[$i + 1])) {
                            array_splice($righeBeneficio, $i, 2);
                            return true;
                        }
                    }
                    
                    // punti articolo
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:G:111:\d{4}:P0:(.{13}):00((?:\-|\+)\d{5})((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $barcode = $matches[1];
                        $punti = $matches[2];
                        $importo = $matches[3];
                        
                        if (preg_match('/:m:1.{7}:0022/', $righeBeneficio[$i + 1])) {
                            array_splice($righeBeneficio, $i, 2);
                            return true;
                        }
                    }
                    
                    // punti ACPT
                    if ((($i + 1) < count($righeBeneficio)) and preg_match('/:m:1.{7}:ACPT/', $righeBeneficio[$i])) {
                        if (preg_match('/:G:111:\d{4}:P1:(.{13}):00((?:\-|\+)\d{5})((?:\+|\-)\d{9})$/', $righeBeneficio[$i + 1], $matches)) {
                            $barcode = $matches[1];
                            $punti = $matches[2];
                            $importo = $matches[3];
                            
                            array_splice($righeBeneficio, $i, 2);
                            return true;
                        }
                    }
                    
                    // punti su transazione
                    if (preg_match('/:G:121:.{21}:00((?:\-|\+)\d{5})((?:\+|\-)\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $punti = $matches[1];
                        $importo = $matches[2];
                        if (preg_match('/:m:1.{7}:0034/', $righeBeneficio[$i + 1])) {
                            array_splice($righeBeneficio, $i, 2);
                            return true;
                        }
                    }
                }
                return false;
            };
            
            // chiamo la closure fino a che tutte le vendite siano state caricate
            while ($esitoCaricamentoVendite($righeVendita)) {}
             if (count($righeVendita) > 0) {// se aquesto punto l'array che contiene le righe vemdita non è vuoto c'è un errore
                echo "errore: $righeVendita[0]\n";
            }
            
            // chiamo la closure fino a che tutti benefici siano stati individuati
            while ($esitoCaricamentoBenefici($righeBeneficio)) {}
            if (count($righeBeneficio) > 0) {// se aquesto punto l'array che contiene le righe beneficio non è vuoto c'è un errore
                echo "errore: $righeBeneficio[0]\n";
            }
            
        }      

        function __destruct() {}
    }
?>
