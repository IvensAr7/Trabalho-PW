<?php

require_once "../config/conexao.php";
require_once "../includes/funcoes.php";

iniciarSessaoSegura();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    validarCsrf();

    $nome = trim($_POST["nome"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $senha = trim($_POST["senha"] ?? "");
    $confirmarSenha = trim($_POST["confirmar_senha"] ?? "");

    // VALIDACOES

    if (empty($nome) || empty($email) || empty($senha) || empty($confirmarSenha)) {

        redirecionarComFlash("login.php?tab=criar", "error", "Preencha todos os campos.");

    }

    $tamNome = function_exists("mb_strlen") ? mb_strlen($nome, "UTF-8") : strlen($nome);
    $tamEmail = function_exists("mb_strlen") ? mb_strlen($email, "UTF-8") : strlen($email);

    if ($tamNome > 100) {
        redirecionarComFlash("login.php?tab=criar", "error", "O nome deve ter no maximo 100 caracteres.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        redirecionarComFlash("login.php?tab=criar", "error", "Email invalido.");

    }

    if ($tamEmail > 100) {
        redirecionarComFlash("login.php?tab=criar", "error", "O email deve ter no maximo 100 caracteres.");
    }

    if (strlen($senha) < 8) {

        redirecionarComFlash("login.php?tab=criar", "error", "A senha deve ter no minimo 8 caracteres.");

    }

    if ($senha !== $confirmarSenha) {

        redirecionarComFlash("login.php?tab=criar", "error", "A confirmacao de senha nao confere.");

    }

    // VERIFICAR EMAIL EXISTENTE

    $sql = "SELECT id_user FROM usuarios WHERE email = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {

        redirecionarComFlash("login.php?tab=criar", "error", "Este email ja esta cadastrado.");

    }

    // CRIPTOGRAFAR SENHA

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $fotoPerfil = salvarAvatarBase64($_POST["avatar_desenho"] ?? null) ?? "default.png";

    // INSERT

    $sql = "INSERT INTO usuarios
        (
            nome,
            email,
            senha,
            foto_perfil
        )
        VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $nome,
        $email,
        $senhaHash,
        $fotoPerfil
    ]);

    unset($_SESSION["csrf_token"]);
    header("Location: login.php");

    exit;
}
