<?php
    namespace Datacollect\Fatture;

    class Fattura {
        private $righeDc = [];
        protected $db;

        public $societa = '';
        public $sede = '';
        public $data = '';
        public $cassa = '';
        public $transazione = '';
        public $repartiIva = [];
        public $righe = [];
        public $totale = 0;
        public $pezzi = 0;
        public $pluPeso = false;
        
        private $vendite = [];
        private $barcode = '';
        private $ivaTipo = 3; //default la piu' alta
        
        // costanti
        private $ivaAliquota = [ 0 => 0, 1 => 4, 2 => 10, 3 => 22, 4 => 0, 5 => 0, 6 => 0, 7 => 0 ];
        private $ivaDescrizione = [ 0 => 'IVA 0', 1 => 'IVA 4', 2 => 'IVA 10', 3 => 'IVA 22', 4 => 'IVA 5', 5 => 'ES.GIFT.92', 6 => 'ES.GIFT 93/94', 7 => 'ES. VARI' ];

        function __construct(array $righe, &$db) {
            $this->db = $db;    
            $this->righeDc = preg_grep("/:(H|S|i|v|V|T|F):1/", $righe);
            $this->carica();
        }
        
        protected function carica() {
            foreach ($this->righeDc as $riga) {
                
                // testata transazione
                if (preg_match('/^(\d{2})(\d{2}):(\d{3}):(\d{2})(\d{2})(\d{2}):(\d{2})(\d{2})(\d{2}):(\d{4}):(\d{3}):H:1/', $riga, $matches)) {
                    if ($matches[1] == '06') {
                        $matches[1] = '36';
                    }
                    if ($matches[1] == '01' and $matches[2] == '51') {
                        $matches[1] = '31';
                    }
                    if ($matches[1] == '01' and $matches[2] == '52') {
                        $matches[1] = '31';
                    }
                    $this->societa = $matches[1];
                    $this->sede = $matches[1].$matches[2];
                    $this->cassa = $matches[3];
                    $this->data = '20'.$matches[4].'-'.$matches[5].'-'.$matches[6];
                    $this->transazione = $matches[10];
                }
        
                // Apro la vendita
                if (preg_match('/^.{31}:S:.{12}((\s|\d:?){13})/', $riga, $matches)) {
                    $barcode = preg_replace('/\s+/', '', $matches[1]);
                    
                    $articoloCodice = '';
                    $articoloDescrizione = '';
                    $repartoCodice = '';
                    $pluPeso = false;
                    if (key_exists($barcode, $this->db->barcode['data'])) {
                        $articoloCodice = $this->db->barcode['data'][$barcode]['articoloCodice'];
                        $articoloDescrizione = trim($this->db->articoli['data'][$articoloCodice]['articoloDescrizione']);
                        $repartoCodice = $this->db->dimensioni['data'][$articoloCodice]['repartoCodice'];
                    } else {
                        $barcode = substr($barcode, 0, 7);
                        if (key_exists($barcode, $this->db->barcode['data'])) {
                            $articoloCodice = $this->db->barcode['data'][$barcode]['articoloCodice'];
                            $articoloDescrizione = trim($this->db->articoli['data'][$articoloCodice]['articoloDescrizione']);
                            $repartoCodice = $this->db->dimensioni['data'][$articoloCodice]['repartoCodice'];
                            $pluPeso = true;
                        }
                    }
                }
                // leggo l'aliquota iva dalla prima riga informativa della vendita
                if (preg_match('/^.{31}:i:100:.{21}:(\d{11})/', $riga, $matches)) {
                    $ivaTipo = $matches[1]*1;
                }
                 // leggo l'indice della vendita dalla seconda riga informativa e chiudo la vendita
                if (preg_match('/^.{31}:i:101:.{21}:(\d{4})/', $riga, $matches)) {
                    $this->vendite[$matches[1]] = [ 'plu' => $barcode,
                                                    'ivaCodice' => $ivaTipo,
                                                    'ivaAliquota' => $this->ivaAliquota[$ivaTipo],
                                                    'ivaDescrizione' => $this->ivaDescrizione[$ivaTipo],
                                                    'articoloCodice' => $articoloCodice,
                                                    'articoloDescrizione' => $articoloDescrizione,
                                                    'pluPeso' => $pluPeso,
                                                    'repartoCassa' => '0001',
                                                    'repartoCodice' => '',
                                                    'unitaImballo' => 1];
                    $barcode = '';
                    $ivaTipo = 3; //default la piu' alta
                }
                
                // prima riga v
                if (preg_match('/^.{31}:v:100:.{21}((?:\+|\-)\d{4})(\d{7})(\d{7})/', $riga, $matches)) {
                    $quantita = $matches[1]*1;
                    $importoLordo = $matches[2]/100;
                    $importoIva = $matches[3]/100;
                }
                
                // secoda riga v
                if (preg_match('/^.{31}:v:101:.{21}:(\d{4})/', $riga, $matches)) {
                    $indice = $matches[1];
                    $this->vendite[$indice]['quantita'] = $quantita;
                    $this->vendite[$indice]['importoTotale'] = round($importoLordo,2);
                    $this->vendite[$indice]['impostaTotale'] = round($importoIva,2);
                    $this->vendite[$indice]['imponibileTotale'] = round($importoLordo - $importoIva,2);
                    $this->vendite[$indice]['importoUnitario'] = round($importoLordo,2);
                    if ( $quantita > 1) {
                        $this->vendite[$indice]['importoUnitario'] = round($importoLordo/$quantita,2);
                    }
                }
                
                // prima riga V
                if (preg_match('/^.{31}:V:1(\d)1.{31}((?:\+|\-)\d{9})/', $riga, $matches)) {
                    $ivaTipo = $matches[1]*1;
                    $imponibile = $matches[2]/100;
                }
                
                // seconda riga V
                if (preg_match('/^.{31}:V:1(\d)0.{31}((?:\+|\-)\d{9})/', $riga, $matches)) {
                    $imposta = $matches[2]/100;
                    
                    $this->repartiIva[$ivaTipo] = [
                                                    'aliquota' => $this->ivaAliquota[$ivaTipo],
                                                    'descrizione' => $this->ivaDescrizione[$ivaTipo],
                                                    'imponibile' => round($imponibile,2),
                                                    'imposta' => round($imposta,2)
                                                    ];
                    $lordo = 0;
                    $imposta = 0;
                    $ivaTipo = 3; //default la piu' alta
                }
                
                // totale transazione
                if (preg_match('/^.{31}:F:1.{27}((?:\+|\-)\d{5})((?:\+|\-)\d{9})$/', $riga, $matches)) {
                    $this->pezzi = $matches[1]*1;
                    $this->totale = $matches[2]/100;
                }
            }
            
            foreach ($this->vendite as $key => $vendita) {
                $this->righe[] = $vendita;
            }
        }
        
        function __destruct() {}
    }
?>
