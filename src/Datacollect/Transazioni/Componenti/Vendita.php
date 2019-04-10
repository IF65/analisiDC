<?php
    namespace Datacollect\Transazioni\Componenti;
	
	class Vendita {
        protected $db;
        
        public $id;
        public $numeroVendita = 0;
        public $codice1 = 1;
		public $codice2 = 0;
		public $codice3 = 1;
		public $repartoCassa = '0000';
        public $repartoCodice = '';
        public $plu = '';
        public $pluPeso = False;
        public $articoloCodice = '';
        public $articoloDescrizione = '';
        public $quantita = 0.0;
		public $unitaImballo = 0;
        public $importoUnitario = 0.0;
		public $importoTotale = 0.0;
		public $ivaCodice = 0;

        function __construct(array $parametri, &$db = null) {
            $this->db = $db;
            
            $this->id = $parametri['id'];
            $this->numeroVendita = $parametri['numeroVendita'];
            $this->codice1 = $parametri['codice1'];
            $this->codice2 = $parametri['codice2'];
            $this->codice3 = $parametri['codice3'];
            $this->repartoCassa = $parametri['repartoCassa'];
            $this->plu = $parametri['plu'];
            $this->quantita = $parametri['quantita'];
            $this->unitaImballo = $parametri['unitaImballo'];
            $this->pluPeso = $parametri['pluPeso'];
            $this->ivaCodice = $parametri['ivaCodice'];
            $this->importoUnitario = $parametri['importoUnitario'];
            $this->importoTotale = $parametri['importoTotale'];
            if( $this->pluPeso) {
                $this->importoTotale = $this->importoUnitario;
            }
            
            if (! is_null($db)) {
                if (array_key_exists($this->plu, $db->barcode['data'])) {
                    $this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
                }
            } else {
                if (array_key_exists('codice', $parametri)) {
                    $this->articoloCodice = $parametri['articoloCodice'];
                }
            }
            
            if (! is_null($db)) {
                if (array_key_exists($this->articoloCodice, $db->articoli['data'])) {
                    $this->articoloDescrizione = $db->articoli['data'][$this->articoloCodice]['articoloDescrizione'];
                }
            }
        }
        
        public function leggi() {
            $parametri = [];
            
            $parametri['codice1'] = $this->codice1;
            $parametri['codice2'] = $this->codice2;
            $parametri['codice3'] = $this->codice3;
            $parametri['repartoCassa'] = $this->repartoCassa;
            $parametri['repartoCodice'] = $this->repartoCodice;
            $parametri['plu'] = $this->plu;
            $parametri['pluPeso'] = $this->pluPeso;
            $parametri['articoloCodice'] = $this->articoloCodice;
            $parametri['articoloDescrizione'] = $this->articoloDescrizione;
            $parametri['quantita'] = $this->quantita;
            $parametri['unitaImballo'] = $this->unitaImballo;
            $parametri['importoUnitario'] = $this->importoUnitario;
            $parametri['importoTotale'] = $this->importoTotale;
            
            return $parametri;
        }
        
        public function confronta(array $parametri, $ricercaEsatta = false) {
            if ($parametri['plu'] == $this->plu) {
                if ($parametri['pluPeso']) {
                    return true;
                } else {
                    if (round($parametri['importoUnitario'],2) == round($this->importoUnitario,2)) {
                        if ($ricercaEsatta and $parametri['quantita'] != $this->quantita) {
                            return false;
                        }
                        return true;
                    }
                }
            }
            return false;
        }

        function __destruct() {}
    }
?>