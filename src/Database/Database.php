<?php
    namespace Database;

	use \PDO;
    use Database\Tabelle\Articox2;
    use Database\Tabelle\Barartx2;
    use Database\Tabelle\Negozi;
    use Database\Tabelle\Anagdafi;
    use Database\Tabelle\Dimensioni;

    class Database {

        protected $pdo = null;
        
        public $articoli;
        public $barcode;
        public $dimensioni;
        public $negozi;
        
        private $tableArticoli;
        private $tableBarcode;
        private $tableNegozi;
        private $tableDimensioni;
        
        private $tableAnagdafi;

        public function __construct($sqlDetails) {
            $conStr = sprintf("mysql:host=%s", $sqlDetails['host']);
            try {
                $this->pdo = new PDO($conStr, $sqlDetails['user'], $sqlDetails['password']);

                self::createDatabase();

            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }

        public function createDatabase() {
        	try {
            
                // archivi
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `archivi`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                $this->tableArticoli = new Articox2($this->pdo);
                $this->tableBarcode = new Barartx2($this->pdo);
                $this->tableNegozi = new Negozi($this->pdo);
                 
                $this->articoli = $this->tableArticoli->ricerca([]);
                $this->barcode = $this->tableBarcode->ricerca([]);
                $this->negozi = $this->tableNegozi->ricerca([]);
                     
                // dimensioni
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dimensioni`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                $this->tableDimensioni = new Dimensioni($this->pdo);
                
                $this->dimensioni = $this->tableDimensioni->ricerca([]);
                
                // dc
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dc`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                $this->tableAnagdafi = new Anagdafi($this->pdo);
                    
                
            
                return true;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
        
        public function __destruct() {
            $this->pdo = null;
        }
    }
?>
