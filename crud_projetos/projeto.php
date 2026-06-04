<?php
require_once "../includes/verificar_login.php";
require_once "../includes/funcoes.php";
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

$sql = "SELECT tarefas.* FROM tarefas
        INNER JOIN projetos ON projetos.id_projeto = tarefas.id_projeto
        WHERE tarefas.id_projeto = ?
        AND projetos.id_user = ?
        ORDER BY tarefas.id_tarefa DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idProjeto, $idUser]);
$tarefas = $stmt->fetchAll(PDO::FETCH_OBJ);

if (count($tarefas) > 0) {
    $tarefasPorId = [];
    foreach ($tarefas as $tarefa) {
        $tarefa->subtarefas = [];
        $tarefasPorId[$tarefa->id_tarefa] = $tarefa;
    }

    $idsTarefas = array_keys($tarefasPorId);
    $placeholders = implode(",", array_fill(0, count($idsTarefas), "?"));

    $sql = "SELECT subtarefas.* FROM subtarefas
            INNER JOIN tarefas ON tarefas.id_tarefa = subtarefas.id_tarefa
            INNER JOIN projetos ON projetos.id_projeto = tarefas.id_projeto
            WHERE subtarefas.id_tarefa IN ($placeholders)
            AND projetos.id_user = ?
            ORDER BY subtarefas.id_subtarefa ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($idsTarefas, [$idUser]));

    foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $subtarefa) {
        $tarefasPorId[$subtarefa->id_tarefa]->subtarefas[] = $subtarefa;
    }
}

$statusClasse = [
    "A Fazer" => "status-amber",
    "Fazendo" => "status-blue",
    "Feito" => "status-green",
];
$classeStatus = $statusClasse[$projeto->status] ?? "status-amber";
$prazoEntrega = analisarPrazoEntrega($projeto->data_entrega ?? null, $projeto->status ?? null);

$titulo = "Projeto";
require_once "../includes/header.php";
?>

<canvas id="mouse-trail"></canvas>

<main class=" project-page">
    <a href="../dashboard/index.php" class="project-back">&larr; voltar ao dashboard</a>

    <article class="project-sheet">
        <div class="project-sheet__body">
            <header class="project-header">
                <h1 class="project-title">
                    <?= htmlspecialchars($projeto->titulo); ?>
                </h1>

                <div class="project-meta">
                    <span class="project-meta__item">
                        <span class="project-meta__label">Status</span>
                        <span class="status-pill <?= $classeStatus; ?>" data-project-status-value>
                            <?= htmlspecialchars($projeto->status); ?>
                        </span>
                    </span>

                    <span class="project-meta__item">
                        <span class="project-meta__label">Entrega</span>
                        <span class="project-meta__value">
                            <?php if (!empty($projeto->data_entrega)): ?>
                                <?= htmlspecialchars((new DateTime($projeto->data_entrega))->format('d/m/Y')); ?>
                            <?php else: ?>
                                Sem data definida
                            <?php endif; ?>
                        </span>
                    </span>
                </div>

                <?php if (!empty($prazoEntrega)): ?>
                    <div class="project-deadline-banner <?= htmlspecialchars($prazoEntrega['classe']); ?>">
                        <span class="project-deadline-banner__label">
                            <?= htmlspecialchars($prazoEntrega['rotulo']); ?>
                        </span>
                        <span class="project-deadline-banner__detail">
                            <?= htmlspecialchars($prazoEntrega['detalhe']); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </header>

            <section class="project-section">
                <h2 class="project-section__title">Descricao</h2>
                <p class="project-section__desc">
                    <?= nl2br(htmlspecialchars($projeto->descricao)); ?>
                </p>
            </section>

            <section class="project-section">
                <h2 class="project-section__title">Tarefas</h2>
                <div class="tasks-wrap">
                    <?php render($tarefas, "tarefa", $idProjeto); ?>
                </div>
            </section>

            <div class="actions">
                <a href="../crud_tarefas/adicionar.php?id_projeto=<?= $idProjeto; ?>" class="btn btn-primary">
                    + Adicionar tarefa
                </a>

                <a href="../crud_projetos/editar.php?id=<?= $idProjeto; ?>" class="btn btn-secondary">
                    Editar projeto
                </a>

                <a href="../dashboard/index.php" class="btn btn-ghost">
                    Cancelar
                </a>
            </div>
        </div>
    </article>
</main>

<?php require_once "../includes/footer.php"; ?>
