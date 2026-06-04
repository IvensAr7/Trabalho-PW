<?php

require_once "../config/conexao.php";
require_once "../includes/funcoes.php";

iniciarSessaoSegura();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    validarCsrf();

    $email = strtolower(trim($_POST["email"] ?? ""));
    $senha = trim($_POST["senha"] ?? "");

    if (empty($email) || empty($senha)) {

        redirecionarComFlash("login.php", "error", "Preencha todos os campos.");

    }

    $sql = "SELECT * FROM usuarios WHERE email = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$email]);

    $usuario = $stmt->fetch();

    if ($usuario) {

        if (password_verify($senha, $usuario->senha)) {

            session_regenerate_id(true);

            $_SESSION["usuario_id"] = $usuario->id_user;
            $_SESSION["usuario_nome"] = $usuario->nome;
            $_SESSION["usuario_email"] = $usuario->email;
            $_SESSION["usuario_foto"] = $usuario->foto_perfil ?? "default.png";

            header("Location: ../dashboard/index.php");

            exit;

        } else {

            redirecionarComFlash("login.php", "error", "Email ou senha invalidos.");

        }

    } else {

        redirecionarComFlash("login.php", "error", "Email ou senha invalidos.");

    }

}
