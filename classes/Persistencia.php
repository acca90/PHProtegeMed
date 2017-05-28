<?php

class Persistencia {

    private $host = 'localhost';
    private $user = 'root';
    private $passw = '123';
    private $db = 'INFORMATION_SCHEMA';
    private $dbAdmin = 'ProtegeMed';
    private $tags = array('$db$','$dbAdmin$');

    /**
     * Query que consulta todos os bancos de dados disponíveis
     *
     * @return array|null
     */
    public function preQuery() {
        $connection = $this->con_open();
        if ($connection != null) {
            $sql = 'SELECT SCHEMA_NAME from SCHEMATA WHERE SCHEMA_NAME LIKE "protegemed_%";';
            $result = mysqli_query($connection,$sql);
            $arraySchemas = array();
            while($row = $result->fetch_array()) {
                array_push($arraySchemas,$row['SCHEMA_NAME']);
            }
            mysqli_close($connection);
            return $arraySchemas;
        }
        return null;
    }


    /**
     * Abre conexoã com o information_schema
     *
     * @return mysqli|null
     */
    private function con_open($admin = false) {
        if ($connection = mysqli_connect($this->host, $this->user, $this->passw, ($admin ? $this->dbAdmin : $this->db) )) {
            mysqli_set_charset($connection,"utf8");
            return $connection;
        } else {
            return null;
        }
    }

    /**
     * Função para realizar consultas
     *
     * @param $sql
     * @return bool|mysqli_result|null
     */
    public function query($sql) {
        $connection = $this->con_open(true);
        $result = mysqli_query($connection,$sql);
        mysqli_close($connection);
        return $result;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassw() {
        return $this->passw;
    }

    /**
     * @return string
     */
    public function getDb() {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getDbAdmin() {
        return $this->dbAdmin;
    }

    /**
     * @return array
     */
    public function getTags() {
        return $this->tags;
    }





}