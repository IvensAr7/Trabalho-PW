<?php

require_once "../includes/verificar_login.php";
require_once "../config/conexao.php";
require_once "../includes/funcoes.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validarCsrf();

    $idTarefa = $_POST["id"] ?? null;
    $idProjeto = $_POST["id_projeto"] ?? null;
    $titulo = trim($_POST["titulo"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $prioridade = trim($_POST["prioridade"] ?? "");
    $status = trim($_POST["status"] ?? "");
    $idUser = $_SESSION["usuario_id"];

    if (!$idTarefa || !$idProjeto || empty($titulo)) {
        redirecionarComFlash("../crud_projetos/projeto.php?id=" . urlencode((string) $idProjeto), "error", "Dados invalidos.");
    }

    $tamTitulo = function_exists("mb_strlen") ? mb_strlen($titulo, "UTF-8") : strlen($titulo);
    $tamDescricao = function_exists("mb_strlen") ? mb_strlen($descricao, "UTF-8") : strlen($descricao);

    if ($tamTitulo > 100) {
        redirecionarComFlash("../crud_projetos/projeto.php?id=" . urlencode((string) $idProjeto), "error", "O titulo da tarefa deve ter no maximo 100 caracteres.");
    }

    if ($tamDescricao > 1000) {
        redirecionarComFlash("../crud_projetos/projeto.php?id=" . urlencode((string) $idProjeto), "error", "A descricao deve ter no maximo 1000 caracteres.");
    }

    if (!in_array($prioridade, ["Baixa", "Media", "Alta"]) || !in_array($status, ["A Fazer", "Fazendo", "Feito"])) {
        redirecionarComFlash("../crud_projetos/projeto.php?id=" . urlencode((string) $idProjeto), "error", "Dados invalidos.");
    }

    try {

        $sql = "UPDATE tarefas
                INNER JOIN projetos ON projetos.id_projeto = tarefas.id_projeto
                SET tarefas.titulo = ?,
                    tarefas.descricao = ?,
                    tarefas.prioridade = ?,
                    tarefas.status = ?
                WHERE tarefas.id_tarefa = ?
                AND tarefas.id_projeto = ?
                AND projetos.id_user = ?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $titulo,
            $descricao,
            $prioridade,
            $status,
            $idTarefa,
            $idProjeto,
            $idUser
        ]);

        atualizarStatusProjeto($pdo, $idProjeto, $idUser);

        header("Location: ../crud_projetos/projeto.php?id=" . $idProjeto);
        exit;

    } catch (PDOException $e) {

        error_log("Erro ao atualizar tarefa: " . $e->getMessage());
        redirecionarComFlash("../crud_projetos/projeto.php?id=" . urlencode((string) $idProjeto), "error", "Erro ao atualizar tarefa.");

    }
}

header("Location: ../dashboard/index.php");
exit;
