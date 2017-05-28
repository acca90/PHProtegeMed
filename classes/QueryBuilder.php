<?php

class QueryBuilder {

    private $preQuery;
    private $identificadorLocal;
    private $campos;
    private $tabelas;
    private $condicao;
    private $ordem;
    private $dbAdmin;
    private $sql;

    public function __construct() {

        $persistencia = new Persistencia();
        $this->preQuery = $persistencia->preQuery();
        $this->dbAdmin = $persistencia->getDbAdmin();

        $this->identificadorLocal = null;
        $this->campos = null;
        $this->tabelas = null;
        $this->condicao = array();
        $this->ordem = null;
        $this->sql = null;

    }

    /**
     * @return null
     */
    public function getIdentificadorLocal() {
        return $this->identificadorLocal;
    }

    /**
     * @param null $identificadorLocal
     */
    public function setIdentificadorLocal($identificadorLocal) {
        $this->identificadorLocal = $identificadorLocal;
    }


    /**
     * @return null
     */
    public function getCampos() {
        if ($this->identificadorLocal == null) {
            return $this->campos;
        } else {
            $locaisCampos1 = array();
            $locaisCampos2 = explode(',',$this->identificadorLocal);
            foreach ($locaisCampos2 as $campo) {
                array_push($locaisCampos1,'local.'.ltrim($campo));
            }
            $this->campos .= ',' . implode(',',$locaisCampos1);
            return $this->campos;
        }
    }

    /**
     * Informa os campos que serão utilizados para a consulta
     *
     * @param $campos
     */
    public function setCampos($campos) {
        $this->campos = $campos;
    }

    /**
     * Informa as tabelas que serão consultadas
     *
     * @param $tabelas
     */
    public function getTabelas() {
        if ($this->identificadorLocal == null) {
            return $this->tabelas;
        } else {
            return $this->tabelas . ', $dbAdmin$.locais local';
        }
    }

    /**
     * @param null $tabelas
     */
    public function setTabelas($tabelas) {
        $splitTabelas = explode(',',$tabelas);
        $newTabelas = array();
        foreach ($splitTabelas as $tabela) {
            array_push($newTabelas,'$db$.' . ltrim($tabela));
        }
        $this->tabelas = implode(', ',$newTabelas);
    }

    /**
     * Informa as condições da consulta
     *
     * @param $condicoes
     */
    public function getCondicao() {
        if ($this->identificadorLocal == null) {
            return implode(' and ', $this->condicao);
        } else {
            array_push($this->condicao,' local.banco = \'$db$\' ');
            return implode(' and ', $this->condicao);
        }
    }

    /**
     * @param null $condicoes
     */
    public function setCondicao($condicao) {
        array_push($this->condicao,$condicao);
    }

    /**
     * @return null
     */
    public function getOrdem() {
        return $this->ordem;
    }

    /**
     * Informa os campos que serão utilizados para ordernação
     *
     * @param $campos
     */
    public function setOrdem($ordem) {
        $this->ordem = $ordem;
    }

    /**
     * Prepara query para consulta em todos os bancos de dados
     *
     * @param $preQuery
     * @param $sql
     */
    private function prepareSQL() {

        $sql_logico = '';
        $sql_logico .= ' select ' . $this->getCampos();
        $sql_logico .= ' from ' . $this->getTabelas();
        $sql_logico .= ' where ' . $this->getCondicao();

        $newSql = array();
        foreach($this->preQuery as $db) {
            $parte = str_replace('$db$', $db, $sql_logico);
            $parte = str_replace('$dbAdmin$', $this->dbAdmin, $parte);
            array_push($newSql, $parte);
        }

        $this->sql = implode(' UNION ', $newSql) . ($this->ordem != null ? 'order by ' . $this->ordem : '');
    }

    /**
     * Executa a consulta
     */
    public function getSQL() {
        $this->prepareSQL();
        return $this->sql;
    }



}