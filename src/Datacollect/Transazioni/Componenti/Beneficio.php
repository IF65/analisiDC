<?php
	namespace Datacollect\Transazioni\Componenti;
	
	class Beneficio {
	    private $db = null;

		public $id = '';
		public $tipo = '';
		public $transazionale = false;
		
		public $plu = '';
		public $repartoCodice = '';
		public $articoloCodice = '';
		
		public $quantita = 0.0;
		public $punti = 0;
		public $sconto = 0.0;
		public $importoRiferimento = 0;
		public $idVendita = null;
		
		function __construct(array $parametri, &$db) {
		    $this->db = $db;

			$this->id = uniqid('', true);
			$this->tipo = $parametri['tipo'];
            $this->plu = $parametri['plu'];
            $this->quantita = $parametri['quantita'];
            $this->sconto = $parametri['sconto'];

            if (! is_null($this->db)) {
                if (array_key_exists($this->plu, $this->db->articoli->elencoBarcode)) {
                    $this->articoloCodice = $this->db->articoli->elencoBarcode[$this->plu];
                }
            }

        }
		
		function __destruct() {
        }
	}
?>