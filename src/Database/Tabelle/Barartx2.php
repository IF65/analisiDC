<?php
    namespace Database\Tabelle;

	class Barartx2 {
        private $pdo = null;

        public static $databaseName = 'archivi';
        public static $tableName = 'barartx2';

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
                $sql = "CREATE TABLE IF NOT EXISTS `$databaseName`.`$tableName` (
                        `REC-BARARTX2` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `FILLER1` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `KIA-BAR2` varchar(22) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CODSOC-BAR2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `BAR13-BAR2` decimal(13,0) NOT NULL DEFAULT '0',
                        `CODCIN-BAR2` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '\"\"',
                        `CODART-BAR2` decimal(6,0) DEFAULT NULL,
                        `CINART-BAR2` decimal(1,0) DEFAULT NULL,
                        `TCOD-BAR2` decimal(2,0) DEFAULT NULL,
                        `SEGELIM-BAR2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `DESCR-BAR2` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CAPAC-BAR2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PEZZAT-BAR2` decimal(6,3) DEFAULT NULL,
                        `SEGNUOV-BAR2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `SEGABBI-BAR2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PVIFA-BAR2` decimal(7,0) DEFAULT NULL,
                        `PVIF-BAR2-E` decimal(7,2) DEFAULT NULL,
                        `CODFOR-BAR2` decimal(6,0) DEFAULT NULL,
                        `SPECIFICA-PEZZO-BAR2` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LUNGHEZZA-PZ-BAR2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `UM-LUN-PZ-BAR2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LUNGHEZZA-LUN-PZ-BAR2` decimal(6,3) DEFAULT NULL,
                        `LARGHEZZA-PZ-BAR2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `UM-LAR-PZ-BAR2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LARGHEZZA-LAR-PZ-BAR2` decimal(6,3) DEFAULT NULL,
                        `ALTEZZA-PZ-BAR2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `UM-ALT-PZ-BAR2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `ALTEZZA-ALT-PZ-BAR2` decimal(6,3) DEFAULT NULL,
                        `PESOLORDO-PZ-BAR2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `UM-PES-PZ-BAR2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PESOLORDO-PES-PZ-BAR2` decimal(6,3) DEFAULT NULL,
                        `VOLUME-PZ-BAR2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `UM-VOL-PZ-BAR2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `VOLUME-VOL-PZ-BAR2` decimal(6,3) DEFAULT NULL,
                        `FILLER2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `SEGCANC-BAR2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        PRIMARY KEY (`BAR13-BAR2`,`CODCIN-BAR2`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                $this->pdo->prepare($sql)->execute();
                
				return true;
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function __destruct() {
			unset($this->pdo);
        }

    }
?>
