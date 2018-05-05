<?php
    namespace Datacollect\Transazioni;
    
    use Datacollect\Transazioni\Componenti\Vendita;
    use Datacollect\Transazioni\Componenti\Beneficio;

    class Transazione {
        private $righe = array();

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

        function __construct($righe) {
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
                    $this->vendite[] = new Vendita($riga);
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
        }

        function __destruct() {}
    }
?>
