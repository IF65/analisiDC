<?php
    namespace Dc;
    
    require_once(__DIR__."/Scontrino.php");
    
    use Dc\Scontrino;
    
    class Dc {
        
        // tutti le variabili sono valorizzate tenendo conto solo degli scontrini validi
        private $numeroRighe = 0; // rughe del file di testo
        private $numeroReferenze = 0;
        private $numeroScontrini = 0; // sono contati solo
        private $numeroScontriniNimis = 0;
        private $totale = 0;
        private $totaleNimis = 0;
        private $numeroPezzi = 0;
        
        private $scontrini = array();
        private $plu = array();
        
        function __construct(string $fileName) {
            try {
                $righe = $this->caricaRighe($fileName);
                if (count($righe) > 0) {
                    $this->caricaScontrini($righe);
                    if(count($this->scontrini) > 0) {
                        $this->recuperaInformazioni();
                    }
                }
            } catch (Exception $e) {
                die("Errore:,".$e->getMessage()."\n");
            }
        }
        
        final private function caricaRighe(string $fileName) {
            $righe = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (false === $this->righe) {
                throw new Exception(
                    sprintf("Errore leggendo il file %s", $fileName)
                );
            }
            return $righe;
        }
        
        private function recuperaInformazioni() {
            foreach ($this->scontrini as $scontrino) {
                // informazioni di testata
                $this->numeroRighe += $scontrino->numeroRighe;
                $this->numeroScontrini++;
                $this->totale += $scontrino->totale;
                if ($scontrino->nimis) {
                    $this->numeroScontriniNimis++;
                    $this->totaleNimis += $scontrino->totale;
                }
                $this->$numeroPezzi += $scontrino->numeroPezzi;
                
                // analizzo i plu venduti
                foreach ($scontrino->vendite as $vendita) {
                    if (array_key_exists($vendita->plu, $this->plu)) {
                        $this->plu[$vendita->plu]['quantita'] += $vendita->quantita;
                    } else {
                        $this->plu[$vendita->plu] = ['quantita' => $vendita->quantita];
                    }
                }
            }
            
            // e li riordino 
            ksort($this->plu, SORT_STRING);
        }
        
        private function caricaScontrini($righe) {
            foreach ($righe as $riga) {
                if (preg_match('/^\d{4}:\d{3}:\d{6}:\d{6}:\d{4}:\d{3}:.:1/', $riga)) {
                    if (preg_match('/^.{31}:H:1/', $riga, $matches)) {
                        $righeScontrino = [$riga];
                    } elseif (preg_match('/^.{31}:F:1/', $riga)) {
                        $righeScontrino[] = $riga;
                        $this->scontrini[] = New Scontrino($righeScontrino);
                    } else {
                        $righeScontrino[] = $riga;
                    }
                }
            }
        }
        
        public function mostraInformazioni() {
            echo "numero righe     : ".$this->numeroRighe."\n";
            echo "scontrini        : ".$this->numeroScontrini."\n";
            echo "scontrini Nimis  : ".$this->numeroScontriniNimis."\n";           
            echo "totale           : ".$this->totale."\n";
            echo "totale Nimis     : ".$this->totaleNimis."\n";
            
            foreach( $this->plu as $key => $row) {
                echo sprintf("barcode: %13s quantita': %.3f\n", $key, $row['quantita']);
            }
        }
    
        function __destruct() {}
    }
?>