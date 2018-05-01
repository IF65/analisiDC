<?php
    namespace Dc;
    
    require_once(__DIR__."/Vendita.php");
    
    class Scontrino {
        private $righe = array();
        
        private $negozio = '';
        private $data = '';
        private $cassa = '';
        private $numero = '';
        
        private $vendite = array();
        
        function __construct($righe) {
           $this->righe = $righe;
           $this->caricaVendite();
        }
        
        private function caricaVendite() {
            $righe = array();
            foreach ($this->righe as $riga) {
                if (preg_match('/^.{31}:S:(\d)(\d)(\d):(\d{4}):.{3}(.{13})((?:\+|\-)\d{4})(\d|\.)(\d{3})(\+|\-|\*)(\d{9})$/', $riga, $matches)) {
                    array_push($righe, $riga);
                    
                    $codice1 = $matches[1];
                    $codice3 = $matches[2];
                    $codice3 = $matches[3];
                    $reparto = $matches[4];
                    $plu = trim($matches[5]);
                    if ('.' == $matches[7]) {
                        $quantita = ($matches[6].'.'.$matches[8])*1;
                        $unitaImballo = 0.0;
                    } else {
                        $quantita = $matches[6]*1;
                        $unitaImballo = $matches[8]/10;
                    }
                    $importoUnitario = 1;
                    $importoTotale = 1;
                    if ('*' == $matches[9]) {
                        $importoUnitario = $matches[10]*1;
                        $importoTotale = $quantita*$importoUnitario;
                    } else {
                        $importoUnitario = ($matches[9].$matches[10])*1;
                        $importoTotale = $importoUnitario;
                    }
                    
                    array_push($this->vendite, new Vendita($codice1, $codice2, $codice3, $reparto, $plu, $quantita, $unitaImballo, $importoUnitario, $importoTotale));
                } 
            }
        }
        
        function __destruct() {}
    }
?>