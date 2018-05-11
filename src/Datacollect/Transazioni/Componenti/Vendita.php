<?php
    namespace Datacollect\Transazioni\Componenti;
	
	/*
	 *RECORD 'S'
	 *code1 = 0 Money trasnfer, 1 Sale trans., 2 Receipt.abort, 3 Training, 4 Re-entry, 5 Stock count, 6 Transfers, 7 Layaways, 8 Suspended, 9 Reset mode
	 *code2 = 0 Normal data entry,1 Negative subdept. or PLU, 1 Negative subdept. or PLU (bottle refund),4 Item return, 5 Trans. return, 6 Trans. void, 7 Item void, 8 Error Correct
	 *code3 = 0 Manual entry, 1 Scanner entry, 8 Manual entry (special sale), 9 Scanner entry (special sale)
    */
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
		
        function __construct(string $riga, &$db) {
            $this->db = $db;
            
            if (preg_match('/^.{31}:S:(\d)(\d)(\d):(\d{4}):.{3}(.{13})((?:\+|\-)\d{4})(\d|\.)(\d{3})(\+|\-|\*)(\d{9})$/', $riga, $matches)) {
                $this->codice1 = $matches[1];
                $this->codice2 = $matches[2];
                $this->codice3 = $matches[3];
                $this->repartoCassa = $matches[4];
                $this->plu = trim($matches[5]);
                if ('.' == $matches[7]) {
                    $this->quantita = ($matches[6].'.'.$matches[8])*1;
                    $this->unitaImballo = 0.0;
                    $this->pluPeso = true;
                    $this->plu = substr($this->plu,0,7);
                } else {
                    $this->quantita = $matches[6]*1;
                    $this->unitaImballo = $matches[8]/10;
                }
                if ('*' == $matches[9]) {
                    $this->importoUnitario = $matches[10]/100;
                    $this->importoTotale = $this->quantita*$this->importoUnitario;
                } else {
                    $this->importoUnitario = ($matches[9].$matches[10])/100;
                    $this->importoTotale = $this->importoUnitario;
                }
                
                if (! is_null($db)) {
                    if (array_key_exists($this->plu, $db->barcode['data'])) {
                        $this->articoloCodice = $db->barcode['data'][$this->plu]['articoloCodice'];
                    }
                }
            }
        }
        
        function __destruct() {}
    }
?>