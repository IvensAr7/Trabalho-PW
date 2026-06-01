<?php

require_once "../includes/verificar_login.php";
require_once "../config/conexao.php";

$id_user = $_SESSION["usuario_id"];

/* ── Stats de projetos ──────────────────────────── */
$stmt = $pdo->prepare("
    SELECT
        COUNT(*)                              AS total,
        SUM(status = 'em_andamento')          AS ativos,
        SUM(status = 'concluido')             AS concluidos,
        SUM(status = 'a_fazer')               AS a_fazer
    FROM projetos
    WHERE id_user = ?
");
$stmt->execute([$id_user]);
$stats = $stmt->fetch(PDO::FETCH_OBJ);

/* ── Stats de tarefas ───────────────────────────── */
$stmt2 = $pdo->prepare("
    SELECT
        COUNT(*)                              AS total,
        SUM(t.status = 'concluida')           AS concluidas
    FROM tarefas t
    JOIN projetos p ON t.id_projeto = p.id_projeto
    WHERE p.id_user = ?
");
$stmt2->execute([$id_user]);
$tarefas = $stmt2->fetch(PDO::FETCH_OBJ);

/* ── Projetos recentes ──────────────────────────── */
$stmt3 = $pdo->prepare("
    SELECT
        p.titulo AS nome, p.status,
        COALESCE((SELECT COUNT(*) FROM tarefas t WHERE t.id_projeto = p.id_projeto), 0)                                    AS total_t,
        COALESCE((SELECT COUNT(*) FROM tarefas t WHERE t.id_projeto = p.id_projeto AND t.status = 'concluida'), 0)         AS ok_t
    FROM projetos p
    WHERE p.id_user = ?
    ORDER BY p.id_projeto DESC
    LIMIT 5
");
$stmt3->execute([$id_user]);
$recentes = $stmt3->fetchAll(PDO::FETCH_OBJ);

/* ── Helpers ────────────────────────────────────── */
$total      = (int)($stats->total      ?? 0);
$ativos     = (int)($stats->ativos     ?? 0);
$concluidos = (int)($stats->concluidos ?? 0);
$a_fazer    = (int)($stats->a_fazer    ?? 0);
$tt         = (int)($tarefas->total    ?? 0);
$tc         = (int)($tarefas->concluidas ?? 0);
$pct_global = $tt > 0 ? round(($tc / $tt) * 100) : 0;

function pct(int $v, int $total): int {
    return $total > 0 ? (int)round(($v / $total) * 100) : 0;
}

$status_cfg = [
    'a_fazer'      => ['label' => 'A fazer',      'cor' => '#d97706', 'bg' => '#fef3c7'],
    'em_andamento' => ['label' => 'Em andamento', 'cor' => '#2563eb', 'bg' => '#dbeafe'],
    'concluido'    => ['label' => 'Concluído',    'cor' => '#16a34a', 'bg' => '#dcfce7'],
];

?>
<?php $titulo = "Perfil"; require_once "../includes/header.php"; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">

<style>
    body {
    background:
        radial-gradient(circle at top left, rgba(37,99,235,.12), transparent 36rem),
        linear-gradient(135deg, #F8FBFF 0%, #EEF4FF 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1rem;
    font-family: system-ui, sans-serif;
}
</style>

<div class="nb-perfil">

    <!-- Barra colorida topo -->
    <div class="nb-perfil-topbar"></div>

    <!-- Espiral -->
    <div class="spiral-bar">
        <?php for ($i = 0; $i < 20; $i++): ?>
            <div class="spiral-ring"></div>
        <?php endfor; ?>
    </div>

    <div class="nb-perfil-inner">

        <!-- ══ ESQUERDO: caderninho ══════════════════ -->
        <div class="nb-perfil-left">

            <!-- Polaroid -->
            <div class="polaroid-anchor">
                <div class="washi"></div>
                <div class="polaroid">
                    <img
                        src="../uploads/avatares/<?= htmlspecialchars($_SESSION['usuario_foto']) ?>"
                        alt="Foto de <?= htmlspecialchars($_SESSION['usuario_nome']) ?>"
                        width="116" height="116"
                    >
                    <span class="polaroid-caption">— eu —</span>
                </div>
            </div>

            <!-- Nome -->
            <h1 class="nb-perfil-name">
                <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
            </h1>

            <!-- Campos -->
            <div class="nb-perfil-field">
                <span class="nb-perfil-label">Nome</span>
                <div class="nb-perfil-value">
                    <i class="fi fi-rr-user"></i>
                    <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
                </div>
            </div>

            <div class="nb-perfil-field">
                <span class="nb-perfil-label">E-mail</span>
                <div class="nb-perfil-value">
                    <i class="fi fi-rr-envelope"></i>
                    <?= htmlspecialchars($_SESSION['usuario_email']) ?>
                </div>
            </div>

            <!-- Ações -->
            <div class="nb-perfil-divider"><span>ações</span></div>

            <a href="../dashboard/index.php" class="nb-perfil-btn nb-perfil-btn-primary">
                <i class="fi fi-rr-apps"></i> Dashboard
            </a>
            <a href="logout.php" class="nb-perfil-btn nb-perfil-btn-ghost">
                <i class="fi fi-rr-sign-out-alt"></i> Encerrar sessão
            </a>

            <!-- Perigo -->
            <div class="danger-wrap">
                <span class="danger-lbl">⚠ zona de perigo</span>
                <form action="excluir.php" method="POST">
                    <button
                        type="submit"
                        class="nb-perfil-btn nb-perfil-btn-danger"
                        onclick="return confirm('Tem certeza? Esta ação não pode ser desfeita.')"
                    >
                        <i class="fi fi-rr-trash"></i> Excluir cadastro
                    </button>
                </form>
            </div>

        </div><!-- /nb-perfil-left -->

        <!-- ══ DIREITO: relatório ══════════════════════ -->
        <div class="nb-perfil-right">

            <!-- Cabeçalho -->
            <div class="report-header">
                <h2 class="report-title">Relatório de atividade</h2>
                <span class="report-date"><?= date('d \d\e M\. \d\e Y') ?></span>
            </div>

            <!-- Métricas -->
            <div class="report-metrics">
                <div class="metric-card total">
                    <div class="metric-num"><?= $total ?></div>
                    <div class="metric-lbl">Projetos</div>
                </div>
                <div class="metric-card amber">
                    <div class="metric-num"><?= $a_fazer ?></div>
                    <div class="metric-lbl">A fazer</div>
                </div>
                <div class="metric-card blue">
                    <div class="metric-num"><?= $ativos ?></div>
                    <div class="metric-lbl">Andamento</div>
                </div>
                <div class="metric-card green">
                    <div class="metric-num"><?= $concluidos ?></div>
                    <div class="metric-lbl">Concluídos</div>
                </div>
            </div>

            <!-- Distribuição de status -->
            <?php if ($total > 0): ?>
            <div class="dist-section">
                <span class="dist-label">Distribuição por status</span>
                <div class="dist-bar">
                    <div class="dist-bar-seg" style="width:<?= pct($a_fazer,    $total) ?>%; background:#d97706;"></div>
                    <div class="dist-bar-seg" style="width:<?= pct($ativos,     $total) ?>%; background:#2563eb;"></div>
                    <div class="dist-bar-seg" style="width:<?= pct($concluidos, $total) ?>%; background:#16a34a;"></div>
                </div>
                <div class="dist-legend">
                    <div class="dist-legend-item">
                        <div class="dist-dot" style="background:#d97706;"></div>
                        A fazer (<?= pct($a_fazer, $total) ?>%)
                    </div>
                    <div class="dist-legend-item">
                        <div class="dist-dot" style="background:#2563eb;"></div>
                        Em andamento (<?= pct($ativos, $total) ?>%)
                    </div>
                    <div class="dist-legend-item">
                        <div class="dist-dot" style="background:#16a34a;"></div>
                        Concluído (<?= pct($concluidos, $total) ?>%)
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Progresso global de tarefas -->
            <?php if ($tt > 0): ?>
            <div class="tasks-section">
                <div class="tasks-header">
                    <span class="tasks-title">Progresso de tarefas</span>
                    <span class="tasks-pct"><?= $pct_global ?>%</span>
                </div>
                <div class="tasks-bar">
                    <div class="tasks-fill" style="width:<?= $pct_global ?>%;"></div>
                </div>
                <span class="tasks-sub">
                    <?= $tc ?> de <?= $tt ?> tarefas concluídas em todos os projetos
                </span>
            </div>
            <?php endif; ?>

            <!-- Projetos recentes -->
            <?php if (!empty($recentes)): ?>
            <div class="recent-section">
                <span class="recent-title">Projetos recentes</span>
                <div class="recent-list">
                    <?php foreach ($recentes as $p):
                        $cfg     = $status_cfg[$p->status] ?? $status_cfg['a_fazer'];
                        $p_total = (int)$p->total_t;
                        $p_ok    = (int)$p->ok_t;
                        $p_pct   = $p_total > 0 ? round(($p_ok / $p_total) * 100) : 0;
                    ?>
                    <div class="recent-item">
                        <div class="recent-status-dot" style="background:<?= $cfg['cor'] ?>;"></div>
                        <span class="recent-name"><?= htmlspecialchars($p->nome) ?></span>
                        <div class="recent-right">
                            <?php if ($p_total > 0): ?>
                            <div class="recent-mini-bar">
                                <div class="recent-mini-fill"
                                     style="width:<?= $p_pct ?>%;background:<?= $cfg['cor'] ?>;"></div>
                            </div>
                            <?php endif; ?>
                            <span class="recent-count">
                                <?php if ($p_total > 0): ?>
                                    <?= $p_ok ?>/<?= $p_total ?>
                                <?php else: ?>
                                    sem tarefas
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <img src="../assets/image/logo.png" alt="" class="perfil-watermark" aria-hidden="true">
                                    
        </div><!-- /nb-right -->
    </div><!-- /nb-inner -->
</div><!-- /notebook -->

<?php require_once "../includes/footer.php"; ?>