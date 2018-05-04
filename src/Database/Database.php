<?php
    namespace Database;

	use \PDO;

    class Database {

        protected $pdo = null;

        public function __construct($sqlDetails) {
            $conStr = sprintf("mysql:host=%s", $sqlDetails['host']);
            try {
                $this->pdo = new PDO($conStr, $sqlDetails['user'], $sqlDetails['password']);

                self::createDatabase($sqlDetails['db']);

            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function createDatabase($db) {
        	try {
            //creo gli schemi che compongono il database
            
            // archivi
            // ----------------------------------------------------------
            $stmt = $this->pdo->prepare("create database if not exists `archivi`;");
            $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
             
            $sql = "CREATE TABLE IF NOT EXISTS `archivi`.`articox2` (
                    `REC-ARTICOX2` varchar(728) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `FILLER1` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `KIAVE-ART2` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `CODSOC-ART2` decimal(2,0) DEFAULT NULL,
                    `COD-ART2` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                    `NEW-FAM-ART2` decimal(3,0) DEFAULT NULL,
                    `FILLER2` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `COZE-ART2` decimal(1,0) DEFAULT NULL,
                    `FAM-ART2` decimal(2,0) DEFAULT NULL,
                    `CODICE-ART2` decimal(3,0) DEFAULT NULL,
                    `CIN-ART2` decimal(1,0) DEFAULT NULL,
                    `DES-ART2` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `CONF-ART2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `CAP-ART2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PEZZAT-ART2` decimal(6,3) DEFAULT NULL,
                    `UM-ART2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `QTA-ART2` decimal(7,2) DEFAULT NULL,
                    `PZ-ART2` decimal(4,0) DEFAULT NULL,
                    `PRBA-ART2` decimal(7,0) DEFAULT NULL,
                    `SC1-ART2` decimal(4,2) DEFAULT NULL,
                    `SC2-ART2` decimal(4,2) DEFAULT NULL,
                    `SC3-ART2` decimal(4,2) DEFAULT NULL,
                    `SC4-ART2` decimal(4,2) DEFAULT NULL,
                    `SCMERC-ART2` decimal(3,0) DEFAULT NULL,
                    `SCEXT-ART2` decimal(4,2) DEFAULT NULL,
                    `SCINLI-ART2` decimal(5,0) DEFAULT NULL,
                    `SCVAL-ART2` decimal(4,2) DEFAULT NULL,
                    `CCSS-ART2` decimal(4,0) DEFAULT NULL,
                    `ENCC-ART2` decimal(1,0) DEFAULT NULL,
                    `COSTI-ART2` decimal(4,0) DEFAULT NULL,
                    `IVA-ART2` decimal(4,2) DEFAULT NULL,
                    `PRCOFIN-ART2` decimal(7,0) DEFAULT NULL,
                    `PRVECASH-ART2` decimal(7,0) DEFAULT NULL,
                    `PRVEIF-ART2` decimal(7,0) DEFAULT NULL,
                    `VUOTI-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `GIAC-ART2` decimal(8,2) DEFAULT NULL,
                    `CODFOR-ART2` decimal(6,0) DEFAULT NULL,
                    `DAT-ART2-UORD` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `GG-ART2-UORD` decimal(2,0) DEFAULT NULL,
                    `MM-ART2-UORD` decimal(2,0) DEFAULT NULL,
                    `AA-ART2-UORD` decimal(4,0) DEFAULT NULL,
                    `NUMORD-ART2` decimal(6,0) DEFAULT NULL,
                    `SCMIN-ART2` decimal(4,0) DEFAULT NULL,
                    `ROTA-ART2` decimal(5,0) DEFAULT NULL,
                    `MEDSEM-ART2` decimal(6,0) DEFAULT NULL,
                    `USCSETT-ART2` decimal(4,0) DEFAULT NULL,
                    `SETT1-ART2` decimal(4,0) DEFAULT NULL,
                    `SETT2-ART2` decimal(4,0) DEFAULT NULL,
                    `SETT3-ART2` decimal(4,0) DEFAULT NULL,
                    `SETT4-ART2` decimal(4,0) DEFAULT NULL,
                    `PRCOMED-ART2` decimal(7,0) DEFAULT NULL,
                    `RICCASH-ART2` decimal(4,2) DEFAULT NULL,
                    `COSTOCES-ART2` decimal(7,3) DEFAULT NULL,
                    `ACQNETAZI-ART2-E` decimal(7,2) DEFAULT NULL,
                    `TOTFATT-ART2` decimal(7,0) DEFAULT NULL,
                    `SCFA-ART2` decimal(4,2) DEFAULT NULL,
                    `LIBERO-ART2` decimal(2,0) DEFAULT NULL,
                    `COFIAZI-ART2-E` decimal(7,2) DEFAULT NULL,
                    `OSF-ART2` decimal(1,0) DEFAULT NULL,
                    `OSN-ART2` decimal(1,0) DEFAULT NULL,
                    `AN-ART2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PNCASH-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PNDETT-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `SCPER-ART2` decimal(4,2) DEFAULT NULL,
                    `SOMMAQTAARR-ART2` decimal(10,2) DEFAULT NULL,
                    `QTACONSIF-ART2` decimal(10,2) DEFAULT NULL,
                    `FATTIF-ART2` decimal(7,0) DEFAULT NULL,
                    `SEGNVPEZ-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `ISFOR-ART2` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `SEGNEG-ART2` decimal(1,0) DEFAULT NULL,
                    `DATELIM-ART2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `GGELIM-ART2` decimal(2,0) DEFAULT NULL,
                    `MMELIM-ART2` decimal(2,0) DEFAULT NULL,
                    `AAELIM-ART2` decimal(4,0) DEFAULT NULL,
                    `BLOCCOVARIAZ-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `REPA-ART2` decimal(2,0) DEFAULT NULL,
                    `MARCHIO-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `FILLER3` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `CODMAG-ART2` decimal(2,0) DEFAULT NULL,
                    `GRIGLIA-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PROVENIENZA-ART2` varchar(28) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PROVEN-ART2` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `VARIETA-ART2` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `CAT-PROD-ART2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `CALIBRO-ART2` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `SEGN-CALPRO-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PZ-RICH-LATT-ART2` decimal(3,0) DEFAULT NULL,
                    `TIPPIA-ART2` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `FILLER4` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `TOT-PZ-RESI-ART2` decimal(10,2) DEFAULT NULL,
                    `PRZ-ACQ-NETTO-ART2` decimal(7,0) DEFAULT NULL,
                    `SHELF-LIFE-ART2` decimal(4,0) DEFAULT NULL,
                    `SCAD62-ART2` decimal(3,0) DEFAULT NULL,
                    `FILLER5` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PEZ-VEN-CASH-ART2` decimal(3,0) DEFAULT NULL,
                    `SEGNO-IVAMU-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `CODCIN-PADRE-ART2` decimal(7,0) DEFAULT NULL,
                    `PVDETT-CONS-ART2` decimal(7,0) DEFAULT NULL,
                    `VUOTO-DETT-ART2` decimal(7,0) DEFAULT NULL,
                    `PRBA-ART2-E` decimal(7,2) DEFAULT NULL,
                    `SCINLI-ART2-E` decimal(4,2) DEFAULT NULL,
                    `CCSS-ART2-E` decimal(4,2) DEFAULT NULL,
                    `COSTI-ART2-E` decimal(4,2) DEFAULT NULL,
                    `PRCOFIN-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PRVECASH-ART2-E` decimal(7,2) DEFAULT NULL,
                    `SN-CASH-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PRVEIF-ART2-E` decimal(7,2) DEFAULT NULL,
                    `SN-DETT-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PRCOMED-ART2-E` decimal(7,2) DEFAULT NULL,
                    `ULTPCOFI-ART2-E` decimal(7,2) DEFAULT NULL,
                    `LIFO-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PRMERCAT-ART2-E` decimal(7,2) DEFAULT NULL,
                    `TOTFATT-ART2-E` decimal(10,2) DEFAULT NULL,
                    `TOTFAT-IF-ART2-E` decimal(10,2) DEFAULT NULL,
                    `CODFOR-UORD-ART2` decimal(6,0) DEFAULT NULL,
                    `FILLER6` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVCASHA-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-CASHA-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVCASHB-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-CASHB-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVCASHC-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-CASHC-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVDETTD-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-DETTD-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVDETTE-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-DETTE-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVDETTF-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-DETTF-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVDETTB-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-DETTB-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVDETTC-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PN-DETTC-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `COD-IVA-ART2` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `FILLER7` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `GIORNISCAD-ART2` decimal(4,0) DEFAULT NULL,
                    `PVDETTCONS-ART2-E` decimal(7,2) DEFAULT NULL,
                    `VALORE-VUO-ART2-E` decimal(7,2) DEFAULT NULL,
                    `PRZ-ACQ-NETTO-ART2-E` decimal(7,2) DEFAULT NULL,
                    `SEGNOINGR-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `SEGNOTRACC-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PERCORSO-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `SOTTOPERC-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `MARCHIOPERC-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `SEGNOPERCORSO-ART2` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `PVDETTOS-ART2-E` decimal(7,2) DEFAULT NULL,
                    `SEGNOSIF-ART2-E` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `TOTPZUSCOS-ART2` decimal(10,2) DEFAULT NULL,
                    `TOTUSPVCOS-ART2-E` decimal(9,2) DEFAULT NULL,
                    `TOTUSPVIOS-ART2-E` decimal(9,2) DEFAULT NULL,
                    `NVOLTEOS-ART2` decimal(2,0) DEFAULT NULL,
                    `DATA-SCA-OS` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `AAOS-ART2` decimal(4,0) DEFAULT NULL,
                    `MMOS-ART2` decimal(2,0) DEFAULT NULL,
                    `GGOS-ART2` decimal(2,0) DEFAULT NULL,
                    `TAGLIA-ART2` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `COLORE-ART2` decimal(3,0) DEFAULT NULL,
                    `MODELLO-ART2` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `DATA-INS` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `AAINS-ART2` decimal(4,0) DEFAULT NULL,
                    `MMINS-ART2` decimal(2,0) DEFAULT NULL,
                    `TOT-CART-OM-ART2` decimal(7,2) DEFAULT NULL,
                    `ART-PRINC-ART2` decimal(7,0) DEFAULT NULL,
                    `DATASCAD-ART2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `AASCAD-ART2` decimal(4,0) DEFAULT NULL,
                    `MMSCAD-ART2` decimal(2,0) DEFAULT NULL,
                    `GGSCAD-ART2` decimal(2,0) DEFAULT NULL,
                    `NEWPRBA-ART2-E` decimal(9,4) DEFAULT NULL,
                    `NEWALTRCO-ART2-E` decimal(5,3) DEFAULT NULL,
                    `COSTIEXTR-ART2` decimal(6,2) DEFAULT NULL,
                    `COSTIEXTR-ART2-E` decimal(6,4) DEFAULT NULL,
                    `NEWCCSS-ART2-E` decimal(4,3) DEFAULT NULL,
                    `PERCORSOFUTURO-ART2` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `DATAAGGIA-ART2` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                    `AAAGGIA-ART2` decimal(4,0) DEFAULT NULL,
                    `MMAGGIA-ART2` decimal(2,0) DEFAULT NULL,
                    `GGAGGIA-ART2` decimal(2,0) DEFAULT NULL,
                    PRIMARY KEY (`COD-ART2`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
            $stmt = $this->pdo->prepare($sql);
            $test = $stmt->execute();
            
            $sql = "CREATE TABLE IF NOT EXISTS `archivi`.`barartx2` (
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
                $stmt = $this->pdo->prepare($sql);
                $test = $stmt->execute();
                
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
                $stmt = $this->pdo->prepare($sql);
                $test = $stmt->execute();
                        
                // dc
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dc`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                        
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
                $stmt = $this->pdo->prepare($sql);
                $test = $stmt->execute();
                
                // dimensioni
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dimensioni`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                        
                $sql = "CREATE TABLE IF NOT EXISTS `dimensioni`.`articolo` (
                        `SOCIETA` decimal(2,0) DEFAULT NULL,
                        `CODICE_ARTICOLO` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                        `SETTORE` decimal(1,0) DEFAULT NULL,
                        `FAMIGLIA3` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `FAMIGLIA2` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `DESCRIZIONE_FAMIGLIA` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `SOTTOREPARTO` decimal(4,0) DEFAULT NULL,
                        `REPARTO_CASSE` decimal(2,0) DEFAULT NULL,
                        `SOTTOFAMIGLIA` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PEZZI_X_CARTONE` decimal(4,0) DEFAULT NULL,
                        `CARTONI_X_STRATO` decimal(2,0) DEFAULT NULL,
                        `STRATI_X_PALLET` decimal(2,0) DEFAULT NULL,
                        `CARTONI_X_PALLET` decimal(4,0) DEFAULT NULL,
                        `VOLUME` decimal(6,0) DEFAULT NULL,
                        `TIPO_RICHIESTA` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `TIPO_ASSORTIMENTO` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CODICE_STAGIONE` decimal(1,0) DEFAULT NULL,
                        `DESCRIZIONE` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CONFEZIONE` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CAPACITA` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PEZZATURA` decimal(6,3) DEFAULT NULL,
                        `UM` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PEZZI_X_CARTONE2` decimal(4,0) DEFAULT NULL,
                        `IVA` decimal(4,2) DEFAULT NULL,
                        `CODICE_FORNITORE` decimal(6,0) DEFAULT NULL,
                        `PROVENIENZA` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `VARIETA` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CATEGORIA` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CALIBRO` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `IVA_MULTIPLA` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `CODICE_PADRE` decimal(7,0) DEFAULT NULL,
                        `GIORNI_SCADENZA` decimal(4,0) DEFAULT NULL,
                        `TAGLIA` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `GRIGLIA` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `MODELLO` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `DATA_INSERIMENTO` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `ANNO_INSERIMENTO` decimal(4,0) DEFAULT NULL,
                        `MESE_INSERIMENTO` decimal(2,0) DEFAULT NULL,
                        `DATA_SCADENZA` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `ANNO_SCADENZA` decimal(4,0) DEFAULT NULL,
                        `MESE_SCADENZA` decimal(2,0) DEFAULT NULL,
                        `GIORNO_SCADENZA` decimal(2,0) DEFAULT NULL,
                        `ELIMINATO` decimal(1,0) DEFAULT NULL,
                        `DATA_ELIMINATO` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `GIORNO_ELIMINATO` decimal(2,0) DEFAULT NULL,
                        `MESE_ELIMINATO` decimal(2,0) DEFAULT NULL,
                        `ANNO_ELIMINATO` decimal(4,0) DEFAULT NULL,
                        `REPARTO` decimal(2,0) DEFAULT NULL,
                        `MARCHIO` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `HA_INGREDIENTI` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `HA_TRACCIABILITA` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PERCORSO` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `SOTTOPERCORSO` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `MARCHIO_PERCORSO` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `UM_LUNGHEZZA_PEZZO` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LUNGHEZZA_PEZZO` decimal(6,3) DEFAULT NULL,
                        `UM_LARGHEZZA_PEZZO` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LARGHEZZA_PEZZO` decimal(6,3) DEFAULT NULL,
                        `UM_ALTEZZA_PEZZO` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `ALTEZZA_PEZZO` decimal(6,3) DEFAULT NULL,
                        `UM_PESO_LORDO_PEZZO` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PESO_LORDO_PEZZO` decimal(6,3) DEFAULT NULL,
                        `UM_VOLUME_PEZZO` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `VOLUME_PEZZO` decimal(7,3) DEFAULT NULL,
                        `UM_LUNGHEZZA_CARTONE` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LUNGHEZZA_CARTONE` decimal(6,3) DEFAULT NULL,
                        `UM_LARGHEZZA_CARTONE` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LARGHEZZA_CARTONE` decimal(6,3) DEFAULT NULL,
                        `UM_ALTEZZA_CARTONE` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `ALTEZZA_CARTONE` decimal(6,3) DEFAULT NULL,
                        `UM_PESO_LORDO_CARTONE` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PESO_LORDO_CARTONE` decimal(6,3) DEFAULT NULL,
                        `UM_VOLUME_CARTONE` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `VOLUME_CARTONE` decimal(7,3) DEFAULT NULL,
                        `UM_LUNGHEZZA_PALLET` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LUNGHEZZA_PALLET` decimal(6,3) DEFAULT NULL,
                        `UM_LARGHEZZA_PALLET` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `LARGHEZZA_PALLET` decimal(6,3) DEFAULT NULL,
                        `UM_ALTEZZA_PALLET` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `ALTEZZA_PALLET` decimal(6,3) DEFAULT NULL,
                        `UM_PESO_LORDO_PALLET` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `PESO_LORDO_PALLET` decimal(6,3) DEFAULT NULL,
                        `UM_VOLUME_PALLET` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `VOLUME_PALLET` decimal(7,3) DEFAULT NULL,
                        `CATEGORIA_IRI` decimal(3,0) DEFAULT NULL,
                        `CATEGORIA_NIELSEN` decimal(3,0) DEFAULT NULL,
                        `CATEGORIA_ITALMARK` decimal(4,0) DEFAULT NULL,
                        `CATEGORIA_INTERMEDIA` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
                        `MARCA_INTERMEDIA` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
                        `ARTICOLO_RIFERIMENTO` decimal(7,0) DEFAULT NULL,
                        `DESCRIZIONE_GIORNALE` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
                        `DESCRIZIONE_SOTTOFAMIGLIA` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        `RAGIONE_SOCIALE_FORNITORE` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        `IN_RILEVAZIONE` decimal(1,0) DEFAULT '0',
                        `COMPRATORE` decimal(2,0) DEFAULT '0',
                        `NOME_COMPRATORE` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                        `MARGINE_2_MIN_CASH` decimal(5,2) DEFAULT '18.00',
                        `MARGINE_2_MIN_DETT` decimal(5,2) DEFAULT '18.00',
                        `SIMULABILE` int(11) DEFAULT '0',
                        `CODICE_RAGGRUPPAMENTO` varchar(8) COLLATE utf8_unicode_ci DEFAULT '',
                        `CAPOGRUPPO` decimal(1,0) DEFAULT '0',
                        `PANIERE_1` int(10) unsigned NOT NULL DEFAULT '0',
                        `PANIERE_2` int(10) unsigned NOT NULL DEFAULT '0',
                        `PEZZI_CASH` int(11) DEFAULT '0',
                        `ESPOSITORE` int(11) DEFAULT '0',
                        `PRIVATE_LABLE` int(11) DEFAULT '0',
                        `MULTIPLI_RICHIESTA` int(11) DEFAULT '0',
                        `IDMONDO` varchar(1) COLLATE utf8_unicode_ci DEFAULT '',
                        `DESCRIZIONE_MONDO` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
                        `IDREPARTO` varchar(2) COLLATE utf8_unicode_ci DEFAULT '',
                        `DESCRIZIONE_REPARTO` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
                        `IDSOTTOREPARTO` varchar(4) COLLATE utf8_unicode_ci DEFAULT '',
                        `DESCRIZIONE_SOTTOREPARTO` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_codice` varchar(10) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_descrizione_completa` varchar(300) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_idreparto` varchar(2) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_desc_reparto` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_idsettore` varchar(4) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_desc_settore` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_idcategoria` varchar(6) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_desc_categoria` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_idtipo` varchar(8) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_desc_tipo` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_idsegmento` varchar(10) COLLATE utf8_unicode_ci DEFAULT '',
                        `IRI_desc_segmento` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
                        PRIMARY KEY (`CODICE_ARTICOLO`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                $stmt = $this->pdo->prepare($sql);
                $test = $stmt->execute();
                    
                return true;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }

        
        public function prezziDelGiorno($data, $negozio) {
        	try {
                
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
                        
                        return array("recordsTotal"=>count($arrayData),"data"=>$arrayData);
                    }
                    $this->pdo->prepare("drop table if exists `dc`.`$tempTableName`;")->execute();
                }
                
                return array("recordsTotal"=>0,"data"=>null);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }
        
        public function anagraficaArticoli() {
        	try {                
                $stmt = $this->pdo->prepare("select
                                                a.`COD-ART2` `codice`,
                                                a.`DES-ART2` `descrizione`,
                                                ifnull(d.`REPARTO_CASSE`,1) `reparto`,
                                                case when a.`COD-IVA-ART2` = 2100 then 2200 else a.`COD-IVA-ART2` end `codiceIva` ,
                                                a.`IVA-ART2` `aliquotaIva`
                                            from archivi.articox2 as a left join dimensioni.articolo as d on a.`COD-ART2`=d.`CODICE_ARTICOLO`
                                            order by 1;");
                if ($stmt->execute()) {
                     $arrayData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                     return array("recordsTotal"=>count($arrayData),"data"=>$arrayData);
                }
                
                return array("recordsTotal"=>0,"data"=>null);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }
        
        public function barcode() {
        	try {                
                $stmt = $this->pdo->prepare("select b.`CODCIN-BAR2` `codice`, b.`BAR13-BAR2` `barcode` from archivi.barartx2 as b order by 1; ");
                if ($stmt->execute()) {
                     $arrayData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                     return array("recordsTotal"=>count($arrayData),"data"=>$arrayData);
                }
                
                return array("recordsTotal"=>0,"data"=>null);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }
        
        public function __destruct() {
            $this->pdo = null;
        }
    }
?>
