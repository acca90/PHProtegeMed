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
    private function preQuery() {
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
     * Prepara query para consulta em todos os bancos de dados
     *
     * @param $preQuery
     * @param $sql
     * @return string
     */
    private function prepareSQL($preQuery, $sql) {
        $newSql = array();
        foreach($preQuery as $db) {
            $value = array($db,$this->dbAdmin);
            array_push($newSql,str_replace($this->tags,$value, $sql));
        }
        return join(' union ',$newSql) . $this->order_by();
    }

    /**
     * Define ordenação das consultas
     *
     * @return string
     */
    private function order_by() {
        return '';
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
        $preQuery = $this->preQuery();
        if ($preQuery != null && count($preQuery) > 0) {
            $finalQuery = $this->prepareSQL($preQuery,$sql);
            $connection = $this->con_open();
            if ($connection != null) {
                $result = mysqli_query($connection,$finalQuery);
                mysqli_close($connection);
                return $result;
            }
        }
        return null;
    }

    /**
     * Função que realiza queries sem preparação especial
     *
     * @param $sql
     *
     */
    public function directQuery($sql) {
        $connection = $this->con_open(true);
        $result = mysqli_query($connection,$sql);
        mysqli_close($connection);
        return $result;
    }



}