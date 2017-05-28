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
     * Verifica se todos os dados foram corretamente informados
     *
     * @param $o
     */
    private function validaInit($o) {
        // TODO
        return true;
    }


    /**
     * Função que realiza a validação e a inicialização da replicação
     *
     * @param $body = Corpo do POST recebido
     */
    public function init($body) {
        parse_str($body,$o);
        $valido = $this->validaInit($o);
        if ($valido) {

            $nome = $o['nome'];
            $sigla = $o['sigla'];
            $hz = $o['hz'];
            $host = $o['host'];
            $usuario = $o['usuario'];
            $senha = $o['senha'];
            $porta = $o['porta'];
            $arqlog = $o['arqlog'];
            $poslog = $o['poslog'];

            $sql = "insert into locais (nome,sigla,banco,hz,MASTER_HOST,MASTER_USER,MASTER_PASSWORD,MASTER_PORT,MASTER_CHANNEL) values";
            $sql .= "('$nome','$sigla','protegemed_$sigla',$hz,'$host','$usuario','$senha',$porta,'protegemed_$sigla');";

            $this->Persistencia->directQuery($sql);
            $this->Persistencia->directQuery("create database protegemed_$sigla");
            exec("mysql -u".$this->Persistencia->getUser()." -p".$this->Persistencia->getPassw()." protegemed_$sigla < /var/www/html/PHProtegeMed/scripts/protegemed.sql");

            $slaveSQL = "CHANGE MASTER TO MASTER_HOST = \"$host\", ";
            $slaveSQL .= "MASTER_USER = \"$usuario\", ";
            $slaveSQL .= "MASTER_PASSWORD = \"$senha\", ";
            $slaveSQL .= "MASTER_PORT = $porta, ";
            $slaveSQL .= "MASTER_LOG_FILE = \"$arqlog\", ";
            $slaveSQL .= "MASTER_LOG_POS = $poslog FOR CHANNEL \"protegemed_$sigla\"";
            $this->Persistencia->directQuery($slaveSQL);

            $startSlaveSQL = "START SLAVE FOR CHANNEL \"protegemed_$sigla\"";
            $this->Persistencia->directQuery($startSlaveSQL);

            $msg = "sucesso";
            $msg = $slaveSQL;

        } else {
            $msg = "Falha na validação dos dados informados";
        }
        $error = array(
            'error' => $msg
        );
        return json_encode($error);
    }

    /**
     * Abre carregamento do bootstrap
     *
     */
    public function bootstrap_open() {
        $_return = '';
        $_return .= '<link href="../assets/css/bootstrap.min.css" rel="stylesheet">';
        $_return .= '<nav class="navbar navbar-default">';
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
        $_return .= '<script src="../assets/js/jquery.min.js"></script>';
        $_return .= '<script src="../assets/js/jquery-validate.min.js"></script>';
        $_return .= '<script src="../assets/js/bootstrap.min.js"></script>';
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

    /**
     * Carrega o HTML para o formulário de inicialização
     *
     */
    public function formularioInit() {
        echo $this->bootstrap_open();
        echo '<form id="formInit" class="form-horizontal">';
        echo '    <div class="form-group">';
        echo '        <label for="nome" class="col-md-4 control-label">Nome local</label>';
        echo '        <div class="col-md-4">';
        echo '            <input type="text" class="form-control" id="nome" name="nome" tabindex="1">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="sigla" class="col-md-4 control-label">Sigla</label>';
        echo '        <div class="col-md-2">';
        echo '            <input type="text" class="form-control" id="sigla" name="sigla" tabindex="2">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="hz" class="col-md-4 control-label">Frequência de corrente (Hz)</label>';
        echo '        <div class="col-md-2">';
        echo '            <input type="text" class="form-control" id="hz" name="hz" tabindex="3">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="host" class="col-md-4 control-label">Servidor (Host)</label>';
        echo '        <div class="col-md-4">';
        echo '            <input type="text" class="form-control" id="host" name="host" tabindex="4">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="usuario" class="col-md-4 control-label">Usuário BD</label>';
        echo '        <div class="col-md-3">';
        echo '            <input type="text" class="form-control" id="usuario" name="usuario" tabindex="5">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="senha" class="col-md-4 control-label">Senha BD</label>';
        echo '        <div class="col-md-3">';
        echo '            <input type="password" class="form-control" id="senha" name="senha" tabindex="6">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="porta" class="col-md-4 control-label">Porta</label>';
        echo '        <div class="col-md-2">';
        echo '            <input type="text" class="form-control" id="porta" name="porta" tabindex="7">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="arqlog" class="col-md-4 control-label">Arquivo de Log</label>';
        echo '        <div class="col-md-4">';
        echo '            <input type="text" class="form-control" id="arqlog" name="arqlog" tabindex="7">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <label for="poslog" class="col-md-4 control-label">Posição do Log</label>';
        echo '        <div class="col-md-2">';
        echo '            <input type="text" class="form-control" id="poslog" name="poslog" tabindex="7">';
        echo '        </div>';
        echo '    </div>';
        echo '    <div class="form-group">';
        echo '        <div class="col-md-offset-4 col-md-2">';
        echo '            <button id="btnSubmit" type="button" class="btn btn-primary" tabindex="8">Inicializar</button>';
        echo '        </div>';
        echo '    </div>';
        echo '</form>';
        echo $this->bootstrap_close();
        echo '<script src="../assets/scripts/Inicializador.js"></script>';
    }



}