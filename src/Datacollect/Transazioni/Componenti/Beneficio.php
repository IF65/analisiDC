<?php
	namespace Datacollect\Transazioni\Componenti;
	
	class Beneficio {
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
		public $articoli = [];
		
		function __construct(array $parametri, &$db) {
			$this->id = uniqid('', true);
			$this->tipo = $parametri['tipo'];
			
			if ($parametri['tipo'] == '0022') {
				$this->transazionale = false;
				$this->plu = $parametri['plu'];
				$this->importoRiferimento = $parametri['importoRiferimento'];
				$this->punti = $parametri['punti'];
				
				if (! is_null($db)) {
                    if (array_key_exists($this->plu, $db->barcode['data'])) {
						$this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
						if (array_key_exists($this->articoloCodice, $db->dimensioni['data'])) {
							$this->repartoCodice = $db->dimensioni['data'][$this->articoloCodice]['repartoCodice'];
						}
					}
				}
			} else if ($parametri['tipo'] == '0023') {
				$this->transazionale = false;
				$this->plu = $parametri['plu'];
				$this->importoRiferimento = 0;
				$this->punti = $parametri['punti'];
				$this->quantita = 1;
				
				if (! is_null($db)) {
                    if (array_key_exists($this->plu, $db->barcode['data'])) {
						$this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
						if (array_key_exists($this->articoloCodice, $db->dimensioni['data'])) {
							$this->repartoCodice = $db->dimensioni['data'][$this->articoloCodice]['repartoCodice'];
						}
					}
				}
			} else if ($parametri['tipo'] == '0027') {
				$this->transazionale = false;
				$this->plu = $parametri['plu'];
				$this->quantita = $parametri['quantita'];
				$this->sconto = $parametri['sconto'];
				$this->punti = $parametri['punti'];
				
				if (! is_null($db)) {
                    if (array_key_exists($this->plu, $db->barcode['data'])) {
						$this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
						if (array_key_exists($this->articoloCodice, $db->dimensioni['data'])) {
							$this->repartoCodice = $db->dimensioni['data'][$this->articoloCodice]['repartoCodice'];
						}
					}
				}
			} else if ($parametri['tipo'] == '0493') {
				$this->transazionale = false;
				$this->plu = $parametri['plu'];
				$this->quantita = $parametri['quantita'];
				$this->sconto = $parametri['sconto'];
				
				if (! is_null($db)) {
                    if (array_key_exists($this->plu, $db->barcode['data'])) {
						$this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
						if (array_key_exists($this->articoloCodice, $db->dimensioni['data'])) {
							$this->repartoCodice = $db->dimensioni['data'][$this->articoloCodice]['repartoCodice'];
						}
					}
				}
			} else if ($parametri['tipo'] == '0492') {
				$this->transazionale = false;
				$this->plu = $parametri['plu'];
				$this->quantita = $parametri['quantita'];
				$this->sconto = $parametri['sconto'];
				
				if (! is_null($db)) {
                    if (array_key_exists($this->plu, $db->barcode['data'])) {
						$this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
						if (array_key_exists($this->articoloCodice, $db->dimensioni['data'])) {
							$this->repartoCodice = $db->dimensioni['data'][$this->articoloCodice]['repartoCodice'];
						}
					}
				}
			} else if ($parametri['tipo'] == '0055') {
				$this->transazionale = false;
				$this->sconto = $parametri['sconto'];
				$this->articoli = $parametri['articoli'];
			} else if ($parametri['tipo'] == '0503') {
				$this->transazionale = true;
				$this->sconto = $parametri['sconto'];
			} else if ($parametri['tipo'] == '0481') {
				$this->transazionale = false;
				$this->sconto = $parametri['sconto'];
				$this->articoli = $parametri['articoli'];
			} else if ($parametri['tipo'] == '0061') {
				$this->transazionale = true;
				$this->sconto = $parametri['sconto'];
			}
        }
		
		function __destruct() {
        }
	}
?>