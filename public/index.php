<?php

    define('BASE_PATH', dirname(__DIR__));

    // Autoload do Composer
    require BASE_PATH . '/vendor/autoload.php';

    // Carregar variáveis de ambiente
    $env = BASE_PATH . '/.env';

    if (file_exists($env)) {

        foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {

            if (str_starts_with(trim($linha), '#')) continue;
            [$chave, $valor] = explode('=', $linha, 2);
            $_ENV[trim($chave)] = trim($valor);

        }
        
    }

    // Configurações da app
    $config = require BASE_PATH . '/config/app.php';
    date_default_timezone_set($config['timezone']);

    // Iniciar sessão
    session_start();

    // Router
    use App\Core\Router;
    $router = new Router();

    // Carregar rotas
    require BASE_PATH . '/api/routes.php';

    // Despachar
    $router->dispatch();