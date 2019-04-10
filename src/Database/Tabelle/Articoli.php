<?php


namespace Database\Tabelle;


class Articoli {

    private $pdo = null;

    private $articox2 = null;
    private $barartx2 = null;
    private $dimensioni = null;

    public function __construct($pdo) {
        try {
            $this->pdo = $pdo;

            $this->articox2 = new Articox2($pdo);
            $this->barartx2 = new Barartx2($pdo);
            $this->dimensioni = new Dimensioni($pdo);

        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function caricaAnagraficaArticoli() {

    }

}