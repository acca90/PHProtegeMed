<?php

/**
 * Class Controller
 *
 * Classe que armazena o lógica para consultas do banco
 *
 */
class Controller {

    private $Persistencia;

    public function __construct() {
        $this->Persistencia = new Persistencia();
    }

    /**
     * @return mixed
     */
    public function getContatos($table = false) {

        $sql = '';
        $sql .= ' select * from $db$.marca ';
        $sql .= ' inner join $dbAdmin$.locais local ';
        $sql .= ' on local.banco =  \'$db$\'';

        $query = $this->Persistencia->query($sql);
        if ($query != null) {
            $_return = array();
            while ($row = $query->fetch_array(MYSQLI_ASSOC))
                array_push($_return,$row);
            if ($table) {
                echo $this->bootstrap_open();
                $this->to_table($_return);
                echo $this->bootstrap_close();
                return;
            }
            echo json_encode($_return,JSON_UNESCAPED_UNICODE);
        }
        return null;
    }

    /**
     * Converte arrays em tabelas
     *
     * @param $_return
     *
     */
    private function to_table($data) {
        $montar_header = true;
        echo "<div class='panel panel-default'><div class=\"panel-body\"><table class='table table-striped table-hover table-condensed'>";
        foreach ($data as $row) {
            if ($montar_header) {
                echo "<tr>";
                while ($tag = current($row)) {
                    echo '<th>'.key($row).'</th>';
                    next($row);
                }
                echo "</tr>";
                $montar_header = false;
            }

            echo "<tr>";
            foreach ($row as $column) {
                echo "<td>$column</td>";
            }
            echo "</tr>";
        }
        echo "</table></div></div>";
    }

    /**
     * Abre carregamento do bootstrap
     *
     */
    public function bootstrap_open() {
        $_return = '';
        $_return .= '<link href="//netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">';
        $_return .= ' <nav class="navbar navbar-default">';
        $_return .= '    <div class="container-fluid">';
        $_return .= '        <div class="navbar-header">';
        $_return .= '           <a class="navbar-brand" href="#">ProtegeMed</a>';
        $_return .= '        </div>';
        $_return .= '    </div>';
        $_return .= '</nav>';
        $_return .= '<div class="container-fluid"><div class="col-md-12">';
        return $_return;
    }

    /**
     * Encerra carregamento do bootstrap
     *
     */
    public function bootstrap_close() {
        $_return = '';
        $_return .= '</div></div>';
        $_return .= '<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>';
        $_return .= '<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>';
        return $_return;
    }

    /**
     * Retorna página 404
     *
     */
    public function get404($isGet) {
        if ($isGet) {
            $s = $this->bootstrap_open();
            $s .= '<div class="alert alert-warning"><h1><i class="glyphicon glyphicon-exclamation-sign"></i> <b>404</b> Página inexistente</h1></div>';
            $s .= $this->bootstrap_close();
            return $s;
        } else {
            $e404 = array(
                'error' => 404
            );
            return json_encode($e404);
        }
    }

}