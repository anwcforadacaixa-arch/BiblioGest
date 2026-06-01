<?php 

require_once "config/db.php";

$nome = "Administrador";
$email = "admin@bibliogest.ao";
$senha = password_hash("admin123", PASSWORD_DEFAULT);
$perfil = "admin";

$stmt = $pdo -> prepare ("INSERT INTO utilizadores (nome, email, senha, perfil) VALUES (?,?,?,?)");
$stmt -> execute([$nome, $email, $senha, $perfil]);

echo "Utilizador criado com sucesso!";
?>