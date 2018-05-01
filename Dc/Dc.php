<?php
    namespace Dc;
    
    require_once(__DIR__."/Scontrino.php");
    
    class Dc {
        
        private $righe = array();
        
        private $numeroRighe = 0;
        private $numeroScontrini = 0;
        private $numeroScontriniNimis = 0;
        private $totale = 0;
        private $totaleNimis = 0;
        
        private $scontrini = array();
        
        function __construct(string $fileName) {
            try {
                $this->caricaRighe($fileName);
                $this->recuperaInformazioni();
                $this->caricaScontrini();
            } catch (Exception $e) {
                die("Errore:,".$e->getMessage()."\n");
            }
        }
        
        final private function caricaRighe(string $fileName) {
            $this->righe = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (false === $this->righe) {
                throw new Exception(
                    sprintf("Errore leggendo il file %s", $fileName)
                );
            }
        }
        
        private function recuperaInformazioni() {
            $this->numeroRighe = count($this->righe);
            
            foreach ($this->righe as $riga) {
                
                // numero scontrini validi
                if (preg_match('/:F:1.{11}(.{13}).{9}(.*)$/', $riga, $matches)) {
                    $this->numeroScontrini++;
                    $this->totale += $matches[2];
                    if (preg_match('/046\d{10}/', $matches[1])) {
                        $this->numeroScontriniNimis++;
                        $this->totaleNimis += $matches[2];
                    }
                }
            }
        }
        
        private function caricaScontrini() {
            $righe = array();
            foreach ($this->righe as $riga) {
                if (preg_match('/^.{23}(\d{4}):(\d{3}):H:1/', $riga, $matches)) {
                    array_push($righe, $riga);
                } elseif (preg_match('/:F:1/', $riga)) {
                    array_push($righe, $riga);
                    array_push($this->scontrini, new Scontrino($righe)); 
                } else {
                    array_push($righe, $riga);
                }
            }
        }
        
        public function mostraInformazioni() {
            echo "numero righe     : ".$this->numeroRighe."\n";
            echo "scontrini Nimis  : ".$this->numeroScontriniNimis."\n";
            echo "totale Nimis     : ".($this->totaleNimis/100)."\n";
            echo "scontrini        : ".$this->numeroScontrini."\n";
            echo "totale           : ".($this->totale/100)."\n";
        }
    
        function __destruct() {}
    }
?>