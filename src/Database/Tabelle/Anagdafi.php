<?php
    namespace Database\Tabelle;

	class Anagdafi {
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
                $sql = "CREATE TABLE IF NOT EXISTS `dc`.`anagdafi` (
                        `data` date NOT NULL,
                        `anno` smallint(5) unsigned NOT NULL DEFAULT '0',
                        `codice` varchar(7) NOT NULL DEFAULT '',
                        `negozio` varchar(4) NOT NULL DEFAULT '',
                        `bloccato` varchar(1) NOT NULL DEFAULT '',
                        `dataBlocco` date DEFAULT NULL,
                        `tipo` varchar(3) NOT NULL DEFAULT '',
                        `prezzoOfferta` decimal(9,2) NOT NULL DEFAULT '0.00',
                        `dataFineOfferta` date DEFAULT NULL,
                        `prezzoVendita` decimal(9,2) NOT NULL DEFAULT '0.00',
                        `prezzoVenditaLocale` decimal(9,2) NOT NULL DEFAULT '0.00',
                        `dataRiferimento` date NOT NULL,
                        PRIMARY KEY (`data`,`codice`,`negozio`),
                        KEY `codice` (`anno`,`codice`,`negozio`,`bloccato`,`dataBlocco`,`tipo`,`prezzoOfferta`,`dataFineOfferta`,`prezzoVendita`,`prezzoVenditaLocale`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                $this->pdo->prepare($sql)->execute();
                
				return true;
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function ricerca(array $query) {
            try {
                
                if (array_key_exists('codiceNegozio', $query) and array_key_exists('data', $query)) {
                    
                    $negozio = $query['codiceNegozio'];
                    $data = $query['data'];
                
                    $tempTableName = uniqid('temp', true);
                    
                    // creo la tabella temporanea
                    $stmt = $this->pdo->prepare("   create table `dc`.`$tempTableName` (PRIMARY KEY(negozio,codice,data)) ENGINE=MEMORY 
                                                    select a.`negozio`, a.`codice`,max(a.`data`) `data`
                                                    from `dc`.anagdafi as a
                                                    where a.`negozio`= '$negozio' and a.`data`<= '$data'
                                                    group by 1,2;");
                    if ($stmt->execute()) {
                        $stmt = $this->pdo->prepare("   select a.*
                                                        from `dc`.`$tempTableName` as d join `dc`.anagdafi as a on d.`negozio`=a.`negozio`
                                                        and d.`codice`=a.`codice` and d.`data`=a.`data`
                                                        order by a.`codice`;");
                        if ($stmt->execute()) {
                            $arrayData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                            $this->pdo->prepare("drop table if exists `dc`.`$tempTableName`;")->execute();
                            
                            $recordsCount = count($arrayData);
                            $data = [];
                            foreach ($arrayData as $prezzoLocale) {
                                $codice = $prezzoLocale['codice'];
                                unset($prezzoLocale['codice']);
                                $data[$codice] = $prezzoLocale;
                            }
                            unset($arrayData);
                            
                            return array( "recordsTotal" => $recordsCount, "data" => $data );
                        }
                        $this->pdo->prepare("drop table if exists `dc`.`$tempTableName`;")->execute();
                    }
                }
                
                return array( "recordsTotal" => 0, "data" => [] );
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function __destruct() {
			unset($this->pdo);
        }

    }
?>
