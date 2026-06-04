<?php

require_once "../includes/verificar_login.php";
require_once "../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    validarCsrf();

    $idProjeto = $_POST["id"] ?? null;
    $titulo = trim($_POST["titulo"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $status = trim($_POST["status"] ?? "");
    $dataEntrega = $_POST["data_entrega"] ?? null;
    $idUser = $_SESSION["usuario_id"];

    if (!$idProjeto || empty($titulo) || empty($dataEntrega) || !in_array($status, ["A Fazer", "Fazendo", "Feito"])) {
        redirecionarComFlash("../dashboard/index.php", "error", "Dados invalidos.");
    }

    $tamTitulo = function_exists("mb_strlen") ? mb_strlen($titulo, "UTF-8") : strlen($titulo);
    $tamDescricao = function_exists("mb_strlen") ? mb_strlen($descricao, "UTF-8") : strlen($descricao);

    if ($tamTitulo > 100) {
        redirecionarComFlash("../dashboard/index.php", "error", "O titulo do projeto deve ter no maximo 100 caracteres.");
    }

    if ($tamDescricao > 1000) {
        redirecionarComFlash("../dashboard/index.php", "error", "A descricao deve ter no maximo 1000 caracteres.");
    }

    $dataValida = DateTimeImmutable::createFromFormat('!Y-m-d', $dataEntrega);
    $errosData = DateTimeImmutable::getLastErrors();

    if ($dataValida === false || ($errosData !== false && (($errosData['warning_count'] ?? 0) > 0 || ($errosData['error_count'] ?? 0) > 0))) {
        redirecionarComFlash("../dashboard/index.php", "error", "Data de entrega invalida.");
    }

    try {

        $sql = "UPDATE projetos
                SET titulo = ?,
                    descricao = ?,
                    status = ?,
                    data_entrega = ?
                WHERE id_projeto = ?
                AND id_user = ?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $titulo,
            $descricao,
            $status,
            $dataEntrega,
            $idProjeto,
            $idUser
        ]);

        header("Location: ../dashboard/index.php");
        exit;

    } catch (PDOException $e) {

        error_log("Erro ao atualizar projeto: " . $e->getMessage());
        redirecionarComFlash("../dashboard/index.php", "error", "Erro ao atualizar projeto.");

    }
}

header("Location: ../dashboard/index.php");
exit;
