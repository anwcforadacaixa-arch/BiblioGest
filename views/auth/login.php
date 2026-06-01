<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Login</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body>

    <div class="login-container">
        <div class="login-box">


            <img class="logo" src="/assets/img/logo.png" alt="">

            <div id="mensagem-erro" class="erro escondido"></div>

            <div class="campo">
                <label>Email ou Nº Estudante</label>
                <input type="text" id="identificador"
                    placeholder="email@teuemail.ao ou 2021001">
            </div>

            <div class="campo">
                <label>Senha</label>
                <input type="password" id="senha" placeholder="••••••••••">
            </div>

            <button id="btn-login">Entrar</button>

        </div>
    </div>

    <script src="/assets/js/login.js"></script>
</body>
</html>