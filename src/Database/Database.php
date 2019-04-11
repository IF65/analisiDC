<?php
    namespace Database;

	use \PDO;
    use Database\Tabelle\Articoli;

    class Database {

        protected $pdo = null;
        
        public $articoli = null;
        public $negozi = null;

        public function __construct(array $sqlDetails, $loadDb = True) {
            $this->loadDb = $loadDb;
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
                     
                // dimensioni
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dimensioni`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                
                // dc
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dc`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));

                $this->articoli = New Articoli($this->pdo);

                return true;
            } catch (PDOException $e) {
                die("DB ERROR: ". $e->getMessage());
            }
        }
        
        public function ricercaPrezziLocali(array $query) {
            $prezziLocali = $this->tableAnagdafi->ricerca($query);
            
            return (array) $prezziLocali;
        }
        
        public function __destruct() {
            $this->pdo = null;
        }
    }
?>
