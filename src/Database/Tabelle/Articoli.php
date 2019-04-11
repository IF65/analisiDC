<?php


namespace Database\Tabelle;


class Articoli {

    private $pdo = null;

    private $articox2 = null;
    private $barartx2 = null;
    private $dimensioni = null;

    public $elencoArticoli = [];
    public $elencoBarcode = [];

    public function __construct($pdo) {
        try {
            $this->pdo = $pdo;

            $this->articox2 = new Articox2($pdo);
            $this->barartx2 = new Barartx2($pdo);
            $this->dimensioni = new Dimensioni($pdo);

            $this->elencoArticoli = $this->caricaElencoArticoli();
            $this->elencoBarcode = $this->caricaElencoBarcode();

        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function caricaElencoArticoli() {
        try {

            $articox2TableName = Articox2::$databaseName.'.'. Articox2::$tableName;
            $dimensioniTableName = Dimensioni::$databaseName.'.'. Dimensioni::$tableName;

            $sql = "select 
                        a.`COD-ART2` codice, 
                        a.`DES-ART2` descrizione, 
                        d.`IVA` aliquotaIva, 
                        d.`REPARTO` reparto ,
                        d.`SOTTOREPARTO` sottoreparto
                    from $articox2TableName as a join $dimensioniTableName as d on a.`COD-ART2`=d.`CODICE_ARTICOLO`";


            $result = [];
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {
                $result[$row['codice']] = $row;
            }
            $stmt = null;

            return $result;

        } catch (PDOException $e) {
            die($e->getMessage());
        }

    }

    public function caricaElencoBarcode() {
        try {
            $tableName = Barartx2::$databaseName.'.'. Barartx2::$tableName;

            $sql = "select 
                        b.`BAR13-BAR2` barcode,
                        b.`CODCIN-BAR2` codice
                    from $tableName as b ";


            $result = [];
            $stmt = $this->pdo->prepare( $sql );
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {
                $result[$row['barcode']] = $row['codice'];
            }
            $stmt = null;

            return $result;

        } catch (PDOException $e) {
            die($e->getMessage());
        }

    }

}