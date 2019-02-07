<?php namespace collectiv\core;

class MySQL extends \PDO implements Database {

    /**
     * The database connection details are hardcoded here. Oh, that's bad. I would get these from a .env file, but for
     * the purpose of this demo, this should suffice.
     */
    public function __construct() {
        try {
            parent::__construct('mysql:host=mysql;dbname=collectiv', 'collectiv', 'collectiv');
            parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * Completely resets the database to its original values.
     */
    public function reset() {
        $this->query(file_get_contents(__DIR__ . '/../../assets/default.sql'));
    }
}