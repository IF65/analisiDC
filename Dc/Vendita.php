<?php
    namespace Dc;
	
	/*
	 *RECORD 'S'
	 *code1 = 0 Money trasnfer, 1 Sale trans., 2 Receipt.abort, 3 Training, 4 Re-entry, 5 Stock count, 6 Transfers, 7 Layaways, 8 Suspended, 9 Reset mode
	 *code2 = 0 Normal data entry,1 Negative subdept. or PLU, 1 Negative subdept. or PLU (bottle refund),4 Item return, 5 Trans. return, 6 Trans. void, 7 Item void, 8 Error Correct
	 *code3 = 0 Manual entry, 1 Scanner entry, 8 Manual entry (special sale), 9 Scanner entry (special sale)
    */
    class Vendita {
		private $codice1 = 1;
		private $codice2 = 0;
		private $codice3 = 1;
		private $reparto = '0000';
        private $plu = '';
        private $quantita = 0.0;
		private $unitaImballo = 0;
        private $importoUnitario = 0.0;
		private $importoTotale = 0.0;
		
        function __construct($codice1, $codice2, $codice3, $reparto, $plu, $quantita, $unitaImballo, $importoUnitario, $importoTotale) {
            $this->codice1 = $codice1;
			$this->codice2 = $codice2;
			$this->codice3 = $codice3;
			$this->plu = $plu;
            $this->quantita = $quantita;
            $this->unitaImballo = $unitaImballo;
			$this->importoUnitario = $importoUnitario;
			$this->importoTotale = $importoTotale;
        }
        
        function __destruct() {}
    }
?>