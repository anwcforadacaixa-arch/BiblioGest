<?php

    namespace App\Middleware;

    use App\Helpers\Response;

    class AdminMiddleware {

        public static function handle(): void {

            AuthMiddleware::handle();
            
            if ($_SESSION["perfil"] !== "admin") {

                Response::forbidden();

            }

        }
        
    }