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
        public $formePagamento = array();

        function __construct(array $righe, &$db) {
            $this->db = $db;    
            $this->righe = $righe;
            $this->carica();
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

            $righeBeneficio = [];
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

                // carico le vendite
                if (preg_match('/^.{31}:S:1/', $riga)) {
                    $this->vendite[] = new Vendita($riga, $this->db);
                }
                
                // carico le righe che fanno parte di un beneficio per eseguire successivamente l'analisi e il loro caricamento
                if (preg_match('/^.{31}:(C|D|G|d|m|w):1/', $riga)) {
                    $righeBeneficio[] = $riga;
                }                

                // carico le forme di pagamento
                if (preg_match('/^.*:T:1.{25}(\d\d).{6}((?:\+|\-)\d{9})$/', $riga, $matches)) {
                    if (array_key_exists($matches[1], $this->formePagamento)) {
                        $this->formePagamento[$matches[1]] += $matches[2]/100;
                    } else {
                        $this->formePagamento[$matches[1]] = $matches[2]/100;
                    }
                }
            }
            
            // carico i benefici
            $esito = function() use(&$righeBeneficio) {
                for ( $i = 0 ; $i < count($righeBeneficio); $i++) {
                    $numeroRigheBlocco = 0;
                    
                    if ((($i + 2) < count($righeBeneficio)) and preg_match('/:C:142:\d{4}:P0:(.{13})((?:\-|\+)\d{4}).{4}(\*|\+|\-)(\d{9})$/', $righeBeneficio[$i], $matches)) {
                        $barcodeC = $matches[1];
                        $quantitaC = $matches[2];
                        $operazioneC = $matches[3];
                        $importoC = $matches[4];
                        if (preg_match('/:G:131:\d{4}:P0:(.{13}):..((?:\-|\+)\d{5})(\*|\+|\-)(\d{9})$/', $righeBeneficio[$i + 1], $matches)) {
                            $barcodeG = $matches[1];
                            $puntiG = $matches[2];
                            $operazioneG = $matches[3];
                            $importoG = $matches[4];
                            if (preg_match('/:m:1.{7}:0027/', $righeBeneficio[$i + 2])) {
                                $righeBeneficio = array_slice($righeBeneficio,$i + 3);
                                return true;
                            }
                        }
                    }
                    return false;
                }
            };
            
            while ($esito) {}
        }

        function __destruct() {}
    }
?>
