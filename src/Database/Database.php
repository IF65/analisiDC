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
        public $negozi;

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
