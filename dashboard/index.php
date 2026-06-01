<?php

require_once "../includes/verificar_login.php";
require_once "../config/conexao.php";

$idUser = $_SESSION["usuario_id"];

$sql = "SELECT
            p.*,
            COALESCE((SELECT COUNT(*) FROM tarefas t WHERE t.id_projeto = p.id_projeto), 0) AS total_tarefas,
            COALESCE((SELECT COUNT(*) FROM tarefas t WHERE t.id_projeto = p.id_projeto AND t.status = 'Feito'), 0) AS tarefas_concluidas
        FROM projetos p
        WHERE p.id_user = ?
        ORDER BY p.id_projeto DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idUser]);
$projetos = $stmt->fetchAll(PDO::FETCH_OBJ);

$colunas = [
    "A Fazer" => [],
    "Fazendo" => [],
    "Feito"   => []
];

foreach ($projetos as $projeto) {
    $status = $projeto->status ?? "A Fazer";
    if (!array_key_exists($status, $colunas)) $status = "A Fazer";
    $colunas[$status][] = $projeto;
}

$totalProjetos  = count($projetos);
$totalTarefas   = (int) array_sum(array_column($projetos, "total_tarefas"));
$tarefasFeitas  = (int) array_sum(array_column($projetos, "tarefas_concluidas"));
$projetosFeitos = count($colunas["Feito"]);

function calcularProgressoDashboard(int $feitas, int $total): int {
    return $total > 0 ? (int) round(($feitas / $total) * 100) : 0;
}

$titulo = "Dashboard";
require_once "../includes/header.php";
require_once "../includes/funcoes.php";
?>

<canvas id="mouse-trail"></canvas>

<div class="notebook">
    <div class="spiral-bar" aria-hidden="true">
        <?php for ($i = 0; $i < 24; $i++): ?>
            <span class="spiral-ring"></span>
        <?php endfor; ?>
    </div>

    <main class="nb-inner">

        <header class="nb-header">
            <div class="nb-brand-area">
                <div class="nb-logo-card">
                    <img src="../assets/image/logotipo.png" alt="NEXO">
                </div>
                <div class="nb-heading-copy">
                    <h1 class="nb-title">Olá, <?= htmlspecialchars($_SESSION["usuario_nome"]); ?></h1>
                    <p class="nb-subtitle">Seus projetos de hoje &mdash; <?= date("d/m/Y"); ?></p>
                </div>
            </div>

            <div class="nb-actions">
                <a href="../crud_projetos/cadastro.php" class="btn-novo">
                    <i class="fi fi-sr-plus-small" aria-hidden="true"></i>
                    Novo projeto
                </a>
                <a href="../crud_usuarios/perfil.php" class="btn-perfil">
                    <img
                        src="../uploads/avatares/<?= htmlspecialchars($_SESSION["usuario_foto"]); ?>"
                        alt="Foto de <?= htmlspecialchars($_SESSION["usuario_nome"]); ?>"
                        width="28" height="28"
                    >
                    <?= htmlspecialchars($_SESSION["usuario_nome"]); ?>
                </a>
            </div>
        </header>

        <section class="nb-stats" aria-label="Resumo dos projetos">
            <div class="stat-stamp">
                <span class="stat-number"><?= $totalProjetos; ?></span>
                <span class="stat-label">projetos</span>
            </div>
            <div class="stat-stamp amber">
                <span class="stat-number"><?= count($colunas["A Fazer"]); ?></span>
                <span class="stat-label">a fazer</span>
            </div>
            <div class="stat-stamp blue">
                <span class="stat-number"><?= count($colunas["Fazendo"]); ?></span>
                <span class="stat-label">fazendo</span>
            </div>
            <div class="stat-stamp green">
                <span class="stat-number"><?= $projetosFeitos; ?></span>
                <span class="stat-label">feitos</span>
            </div>
        </section>

        <?php render($projetos, 'projeto'); ?>

        <footer class="nb-footer">
            <span class="nb-footer-brand">
                <img src="../assets/image/logotipo.png" alt="NEXO">
                <span><?= date("Y"); ?></span>
            </span>
            <span class="nb-footer-page">pág. 001</span>
            <a href="../crud_usuarios/logout.php" class="btn-logout">encerrar sessão</a>
        </footer>

    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".card-progress-fill[data-target]").forEach(el => {
        const target = parseFloat(el.dataset.target) || 0;
        el.style.width = "0%";
        el.style.transition = "none";
        requestAnimationFrame(() => requestAnimationFrame(() => {
            el.style.transition = "width 700ms cubic-bezier(.4,0,.2,1)";
            el.style.width = target + "%";
        }));
    });
});
</script>

<?php $confirmarLogoutAoVoltar = true; ?>
<?php require_once "../includes/footer.php"; ?>
