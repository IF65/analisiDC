<?php
    namespace Database\Tabelle;

	class Dimensioni {
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
                $this->pdo->prepare($sql)->execute();
                
				return true;
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function ricerca(array $query) {
            try {
                $sql = "select `CODICE_ARTICOLO` `articoloCodice`, ifnull(`REPARTO_CASSE`, 1) `repartoCodice`
                        from dimensioni.articolo ";
                if (array_key_exists('articoloCodice', $query)) {
                    $sql .= "where `CODICE_ARTICOLO` = '".$query['articoloCodice']."'";
                }

                $data = [];
                $recordsCount = 0;
                $stmt = $this->pdo->prepare( $sql );
                $stmt->execute();
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {
                    $articoloCodice = $row['articoloCodice'];
                    unset($row['articoloCodice']);
                    $data[$articoloCodice] = $row;
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
