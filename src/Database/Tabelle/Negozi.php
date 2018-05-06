<?php
    namespace Database\Tabelle;

	class Negozi {
        private $pdo = null;

        public function __construct($pdo) {
        	try {
                $this->pdo = $pdo;
                
				self::creaTabella();

            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function creaTabella() {
        	try {
                $sql = "CREATE TABLE IF NOT EXISTS `archivi`.`negozi` (
                        `codice` varchar(4) NOT NULL DEFAULT '',
                        `codice_interno` varchar(4) NOT NULL,
                        `societa` varchar(2) NOT NULL,
                        `societa_descrizione` varchar(100) NOT NULL DEFAULT '',
                        `negozio` varchar(2) NOT NULL,
                        `negozio_descrizione` varchar(100) NOT NULL DEFAULT '',
                        `tipo` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1=sede, 2=magazzino, 3=vendita',
                        `ip` varchar(15) NOT NULL,
                        `ip_mtx` varchar(15) NOT NULL,
                        `utente` varchar(50) NOT NULL,
                        `password` varchar(50) NOT NULL,
                        `percorso` varchar(255) NOT NULL,
                        `data_inizio` date DEFAULT NULL,
                        `data_fine` date DEFAULT NULL,
                        `abilita` tinyint(1) NOT NULL DEFAULT '1',
                        `recupero_anagdafi` tinyint(1) NOT NULL DEFAULT '0',
                        `invio_dati_gre` tinyint(1) NOT NULL DEFAULT '0',
                        `invio_dati_copre` tinyint(1) NOT NULL DEFAULT '0',
                        `codice_ca` varchar(10) NOT NULL DEFAULT '',
                        `codice_mt` varchar(6) NOT NULL DEFAULT '',
                        `chalco` tinyint(1) NOT NULL DEFAULT '0',
                        `rootUser` varchar(50) NOT NULL DEFAULT '',
                        `rootPassword` varchar(50) NOT NULL DEFAULT '',
                        PRIMARY KEY (`codice`),
                        KEY `codice_interno` (`codice_interno`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                $this->pdo->prepare($sql)->execute();
                
				return true;
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function ricerca(array $query) {
            try {
                $sql = "select `codice` `negozioCodice`, `codice_interno` `negozioCodiceInterno`, negozio_descrizione `negozioDescrizione` 
                        from archivi.negozi ";
                if (array_key_exists('negozioCodice', $query)) {
                    $sql .= "where `codice` = '".$query['negozioCodice']."'";
                }

                $data = [];
                $recordsCount = 0;
                $stmt = $this->pdo->prepare( $sql );
                $stmt->execute();
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {
                    $negozioCodice = $row['negozioCodice'];
                    unset($row['negozioCodice']);
                    $data[$negozioCodice] = $row;
                    $recordsCount++;
                }
                $stmt = null;
                
                return array("recordsTotal"=>$recordsCount,"data"=>$data);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function __destruct() {
			unset($this->pdo);
        }

    }
?>
