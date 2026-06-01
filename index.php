<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioGest — Login</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <div class="login-container">

        <div class="login-box">

            <img class="logo" src="/assets/img/logo.png" alt="">

            <!--<h1>BiblioGest</h1>
            <p class="subtitulo">Sistema de Gestão de Bibliotecas</p>-->

            <div class="campo">

                <label>Email</label>
                <input type="email" id="email" placeholder="o.teu@email.com">

            </div>
            
            <div class="campo">

                <label>Senha</label>
                <input type="password" id="senha" placeholder="••••••••••••">

            </div>

            <button id="btn-login">Entrar</button>

              <div id="mensagem-erro" class="erro escondido"></div>

        </div>

    </div>

    <script src="/assets/js/login.js"></script>

</body>
</html>