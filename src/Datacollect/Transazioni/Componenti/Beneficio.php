<?php
	namespace Datacollect\Transazioni\Componenti;
	
	class Beneficio {
		private $tipo = '';
		private $transazionale = false;
		private $plu = '';
		private $reparto = '';
		
		private $punti = 0;
		private $quantita = 0.0;
		private $importo = 0.0;
		
		function __construct($tipo, $parametri) {
			$this->tipo = $tipo;
			
			if ($tipo == '0493') {
				$this->transazionale = $parametri['transazionale'];
				$this->plu = $parametri['plu'];
				$this->reparto = $parametri['reparto'];
				$this->quantita = $parametri['quantita'];
				$this->importo = $parametri['importo'];
			}
        }
		
		function __destruct() {
        }
	}
?>