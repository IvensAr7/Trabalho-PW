<?php
require_once "../includes/verificar_login.php";
require_once "../config/conexao.php";

$idTarefa = $_GET["id"] ?? null;
$idUser = $_SESSION["usuario_id"];

if (!$idTarefa) {
    header("Location: ../dashboard/index.php");
    exit;
}

$sql = "SELECT tarefas.* FROM tarefas
        INNER JOIN projetos ON projetos.id_projeto = tarefas.id_projeto
        WHERE tarefas.id_tarefa = ?
        AND projetos.id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idTarefa, $idUser]);
$tarefa = $stmt->fetch(PDO::FETCH_OBJ);

if (!$tarefa) {
    redirecionarComFlash("../dashboard/index.php", "error", "Tarefa nao encontrada.");
}

$titulo = "Editar Tarefa";
require_once "../includes/header.php";
?>

<main class="">
    <div class="form-notebook">
        <div class="spiral-bar" aria-hidden="true">
            <?php for ($i = 0; $i < 12; $i++): ?>
                <span class="spiral-ring"></span>
            <?php endfor; ?>
        </div>

        <div class="form-inner">
            <header class="form-header">
                <h1 class="form-title">Editar Tarefa</h1>
                <p class="form-subtitle">Ajuste os detalhes da tarefa e mantenha o projeto atualizado.</p>
            </header>

            <form action="atualizar.php" method="POST">
                <?= csrfInput(); ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($tarefa->id_tarefa); ?>">
                <input type="hidden" name="id_projeto" value="<?= htmlspecialchars($tarefa->id_projeto); ?>">

                <div class="form-group">
                    <label for="titulo" class="form-label">Titulo da tarefa</label>
                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        class="form-control"
                        value="<?= htmlspecialchars($tarefa->titulo); ?>"
                        maxlength="100"
                        minlength="3"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="descricao" class="form-label">Descricao</label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        class="form-control"
                        rows="4"
                        maxlength="1000"
                    ><?= htmlspecialchars($tarefa->descricao); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prioridade" class="form-label">Prioridade</label>
                    <select id="prioridade" name="prioridade" class="form-control" required>
                        <option value="Baixa" <?= $tarefa->prioridade == "Baixa" ? "selected" : ""; ?>>Baixa</option>
                        <option value="Media" <?= $tarefa->prioridade == "Media" ? "selected" : ""; ?>>Media</option>
                        <option value="Alta" <?= $tarefa->prioridade == "Alta" ? "selected" : ""; ?>>Alta</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="A Fazer" <?= $tarefa->status == "A Fazer" ? "selected" : ""; ?>>A Fazer</option>
                        <option value="Fazendo" <?= $tarefa->status == "Fazendo" ? "selected" : ""; ?>>Fazendo</option>
                        <option value="Feito" <?= $tarefa->status == "Feito" ? "selected" : ""; ?>>Feito</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Atualizar tarefa
                    </button>

                    <a href="../crud_projetos/projeto.php?id=<?= htmlspecialchars($tarefa->id_projeto); ?>" class="btn btn-secondary">
                        Cancelar e voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>
