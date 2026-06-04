<?php

require_once "../includes/verificar_login.php";
require_once "../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    validarCsrf();

    $titulo = trim($_POST["titulo"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $idUser = $_SESSION["usuario_id"];
    $dataEntrega = $_POST["data_entrega"] ?? null;
    $dataCriacao = date("Y-m-d H:i:s");

    if (empty($titulo) || empty($dataEntrega)) {
        redirecionarComFlash("cadastro.php", "error", "Preencha todos os campos.");
    }

    $tamTitulo = function_exists("mb_strlen") ? mb_strlen($titulo, "UTF-8") : strlen($titulo);
    $tamDescricao = function_exists("mb_strlen") ? mb_strlen($descricao, "UTF-8") : strlen($descricao);

    if ($tamTitulo > 100) {
        redirecionarComFlash("cadastro.php", "error", "O titulo do projeto deve ter no maximo 100 caracteres.");
    }

    if ($tamDescricao > 1000) {
        redirecionarComFlash("cadastro.php", "error", "A descricao deve ter no maximo 1000 caracteres.");
    }

    $dataValida = DateTimeImmutable::createFromFormat('!Y-m-d', $dataEntrega);
    $errosData = DateTimeImmutable::getLastErrors();

    if ($dataValida === false || ($errosData !== false && (($errosData['warning_count'] ?? 0) > 0 || ($errosData['error_count'] ?? 0) > 0))) {
        redirecionarComFlash("cadastro.php", "error", "Data de entrega invalida.");
    }

    $hoje = new DateTimeImmutable('today');
    if ($dataValida < $hoje) {
        redirecionarComFlash("cadastro.php", "error", "A data de entrega nao pode ser anterior a hoje.");
    }

    $sql = "INSERT INTO projetos
            (
                titulo,
                descricao,
                id_user,
                data_entrega,
                data_criacao
            )
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $titulo,
        $descricao,
        $idUser,
        $dataEntrega,
        $dataCriacao
    ]);

    header("Location: ../dashboard/index.php");
    exit;
}

header("Location: cadastro.php");
exit;
