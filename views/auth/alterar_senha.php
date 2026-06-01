<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/assets/img/logo.png">
    <title>BiblioGest — Alterar Senha</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body>

    <div class="login-container">
        <div class="login-box">

            <h1>BiblioGest</h1>
            <p class="subtitulo">Por segurança, define uma nova senha</p>

            <div id="mensagem-erro" class="erro escondido"></div>
            <div id="mensagem-sucesso" class="escondido"
                style="background:#e8f5e9;color:#2e7d32;padding:10px;
                border-radius:6px;font-size:13px;margin-bottom:16px;">
            </div>

            <div class="campo">
                <label>Nova Senha</label>
                <input type="password" id="nova_senha" placeholder="Mínimo 6 caracteres">
            </div>

            <div class="campo">
                <label>Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" placeholder="Repete a nova senha">
            </div>

            <button id="btn-alterar">Alterar Senha</button>

        </div>
    </div>

    <script src="/assets/js/alterar_senha.js"></script>
</body>
</html>