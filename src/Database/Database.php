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
        
        public $articox2;
        public $barartx2;
        public $negozi;
        public $anagdafi;
        public $dimensioni;

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
            
                // archivi
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `archivi`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                $this->articox2 = new Articox2($this->pdo);
                $this->barartx2 = new Barartx2($this->pdo);
                $this->negozi = new Negozi($this->pdo);
                            
                // dc
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dc`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                $this->anagdafi = new Anagdafi($this->pdo);
                    
                // dimensioni
                // ----------------------------------------------------------
                $stmt = $this->pdo->prepare("create database if not exists `dimensioni`;");
                $stmt->execute() or die(print_r($this->pdo->errorInfo(), true));
                $this->dimensioni = new Dimensioni($this->pdo);  
            
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
