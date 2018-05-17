<?php
    namespace Datacollect\Transazioni\Componenti;
	
	class Vendita {
        protected $db;
        
        public $codice1 = 1;
		public $codice2 = 0;
		public $codice3 = 1;
		public $repartoCassa = '0000';
        public $repartoCodice = '';
        public $plu = '';
        public $pluPeso = False;
        public $articoloCodice = '';
        public $articoloDescrizione = '';
        public $ivaAliquota = '';
        public $ivaCodice = '';
        public $quantita = 0.0;
		public $unitaImballo = 0;
        public $importoUnitario = 0.0;
		public $importoTotale = 0.0;
        
        // benefici
        public $id_0022 = '';
        public $id_0023 = '';
        public $id_0027 = '';
        public $id_0492 = '';
        public $id_0493 = '';
       
		
        function __construct(array $parametri, &$db = null) {
            $this->db = $db;
            
            $this->codice1 = $parametri['codice1'];
            $this->codice2 = $parametri['codice2'];
            $this->codice3 = $parametri['codice3'];
            $this->repartoCassa = $parametri['repartoCassa'];
            $this->plu = $parametri['plu'];
            $this->quantita = $parametri['quantita'];
            $this->unitaImballo = $parametri['unitaImballo'];
            $this->pluPeso = $parametri['pluPeso'];
            $this->importoUnitario = $parametri['importoUnitario'];
            $this->importoTotale = $parametri['importoTotale'];
            
            if (! is_null($db)) {
                if (array_key_exists($this->plu, $db->barcode['data'])) {
                    $this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
                }
            } else {
                if (array_key_exists('codice', $parametri)) {
                    $this->articoloCodice = $parametri['articoloCodice'];
                }
            }
        }
        
        public function leggiVendita() {
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
            $parametri['ivaAliquota'] = $this->ivaAliquota;
            $parametri['ivaCodice'] = $this->ivaCodice;
            $parametri['quantita'] = $this->quantita;
            $parametri['unitaImballo'] = $this->unitaImballo;
            $parametri['importoUnitario'] = $this->importoUnitario;
            $parametri['importoTotale'] = $this->importoTotale;
            
            return $parametri;
        }
        
        public function sommaVendita(array $parametri) {
            if ($this->confronto($parametri)) {// devono avere stesso plu e stesso importo unitario
                if ($parametri['pluPeso']) {
                    $this->quantita += $parametri['quantita'];
                    $this->importoTotale += $parametri['importoUnitario'];
                } else {
                    $this->quantita += $parametri['quantita'];
                    $this->importoTotale = $this->quantita * $this->importoUnitario;
                }
                 return true;
            }
        
            return false;
        }
        
        public function confronto(array $parametri, $ricercaEsatta = false) {
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