<?php
header("Content-type: text/html; charset=utf-8");
require_once("vendor/autoload.php");
require_once("classes/Persistencia.php");
require_once("classes/Controller.php");

//Create Your container
$c = new \Slim\Container();

//Override the default Not Found Handler
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $Controller = new Controller();
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write($Controller->get404($request->getMethod() == 'GET'));

    };
};

// instantiate the App object
$app = new \Slim\App($c);

/**
 * INDEX
 */
$app->get('/', function ($request, $response, $args) {
    $Controller = new Controller();
    echo $Controller->bootstrap_open();
    echo 'Woops, nada aqui';
    echo $Controller->bootstrap_close();
});

/**
 * Listagem da tabela marca
 *
 */
$app->get('/marca', function ($request, $response, $args) {
    $Controller = new Controller();
    echo $Controller->getContatos($table = true);
});

$app->post('/marca', function () {
    $Controller = new Controller();
    $Controller->getContatos();
});

/**
 * INIT
 * URL utilizada para o envio de informações necessárias para inicializar
 * a construção do banco e a replicação de uma nova instância
 *
 * @param nome = Nome da institução
 * @param sigla = Sigla utilizada pela insituição
 * @param hz = frequência de onda utilizada na instância
 * @param host = host/ip da instância
 * @param user = Usuário do banco
 * @param senha = Senha do usuário do banco
 * @param porta = Porta de conexão com o banco
 *
 */
$app->get('/init', function ($request, $response, $args) {
    $Controller = new Controller();
    $Controller->formularioInit();
});

$app->post('/init', function ($request) {
    $Controller = new Controller();
    $body = $request->getBody()->getSize();
    if ($body != null) {
        echo $Controller->init($request->getBody());
    } else {
        $error = array(
            'error' => 'Parâmetros de inicialização não informados'
        );
        return json_encode($error,JSON_UNESCAPED_UNICODE);
    }

});


// Run application
$app->run();