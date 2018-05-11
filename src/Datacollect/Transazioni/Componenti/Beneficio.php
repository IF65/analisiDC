<?php
	namespace Datacollect\Transazioni\Componenti;
	
	class Beneficio {
		public $tipo = '';
		public $transazionale = false;
		
		public $plu = '';
		public $repartoCodice = '';
		public $articoloCodice = '';
		
		public $quantita = 0.0;
		public $punti = 0;
		public $sconto = 0.0;
		
		function __construct(array $parametri, &$db) {
			$this->tipo = $parametri['tipo'];
			
			if ($parametri['tipo'] == '0027') {
				$this->transazionale = false;
				$this->plu = $parametri['plu'];
				$this->quantita = $parametri['quantita'];
				$this->sconto = $parametri['sconto']/100;
				$this->punti = $parametri['punti']*1;
				
				if (! is_null($db)) {
                    if (array_key_exists($this->plu, $db->barcode['data'])) {
						$this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
						if (array_key_exists($this->articoloCodice, $db->dimensioni['data'])) {
							$this->repartoCodice = $db->dimensioni['data'][$this->articoloCodice]['repartoCodice'];
						}
					}
				}
			}
        }
		
		function __destruct() {
        }
	}
?>