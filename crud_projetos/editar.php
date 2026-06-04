<?php
require_once "../includes/verificar_login.php";
require_once "../config/conexao.php";

$idProjeto = $_GET["id"] ?? null;
$idUser = $_SESSION["usuario_id"];

if (!$idProjeto) {
    header("Location: ../dashboard/index.php");
    exit;
}

$sql = "SELECT * FROM projetos
        WHERE id_projeto = ?
        AND id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idProjeto, $idUser]);
$projeto = $stmt->fetch(PDO::FETCH_OBJ);

if (!$projeto) {
    redirecionarComFlash("../dashboard/index.php", "error", "Projeto nao encontrado.");
}

$titulo = "Editar Projeto";
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
                <h1 class="form-title">Editar Projeto</h1>
                <p class="form-subtitle">Atualize a página do projeto sem perder o fluxo.</p>
            </header>

            <form action="atualizar.php" method="POST">
                <?= csrfInput(); ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($projeto->id_projeto); ?>">

                <div class="form-group">
                    <label for="titulo" class="form-label">Título do projeto</label>
                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        class="form-control"
                        value="<?= htmlspecialchars($projeto->titulo); ?>"
                        maxlength="100"
                        minlength="3"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        class="form-control"
                        rows="4"
                        maxlength="1000"
                    ><?= htmlspecialchars($projeto->descricao); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="A Fazer" <?= $projeto->status == "A Fazer" ? "selected" : ""; ?>>A Fazer</option>
                        <option value="Fazendo" <?= $projeto->status == "Fazendo" ? "selected" : ""; ?>>Fazendo</option>
                        <option value="Feito" <?= $projeto->status == "Feito" ? "selected" : ""; ?>>Feito</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data_entrega" class="form-label">Data de entrega</label>
                    <input
                        type="date"
                        id="data_entrega"
                        name="data_entrega"
                        class="form-control"
                        value="<?= htmlspecialchars($projeto->data_entrega); ?>"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Atualizar projeto
                    </button>

                    <a href="../crud_projetos/projeto.php?id=<?= htmlspecialchars($projeto->id_projeto); ?>" class="btn btn-secondary">
                        Cancelar e voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>
