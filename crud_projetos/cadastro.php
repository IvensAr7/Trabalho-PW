<?php
require_once "../includes/verificar_login.php";

$titulo = "Criar Projeto";
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
                <h1 class="form-title">Novo Projeto</h1>
                <p class="form-subtitle">Preencha os dados para iniciar uma nova página.</p>
            </header>

            <form action="cadastrar.php" method="POST">
                <?= csrfInput(); ?>

                <div class="form-group">
                    <label for="titulo" class="form-label">Título do projeto</label>
                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        class="form-control"
                        placeholder="Ex: Redesign do site corporativo"
                        maxlength="100"
                        minlength="3"
                        autocomplete="off"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        class="form-control"
                        placeholder="Detalhes, objetivos e anotacoes iniciais..."
                        maxlength="1000"
                        rows="4"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="data_entrega" class="form-label">Data de entrega</label>
                    <input
                        type="date"
                        id="data_entrega"
                        name="data_entrega"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        min="<?= date('Y-m-d') ?>"
                        required
                    >
                </div>

                <div class="form-hint">
                    Dica: destaque prazos curtos para enxergar rapidamente o que precisa de atenção.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Criar projeto
                    </button>
                    <a href="../dashboard/index.php" class="btn btn-secondary">
                        Cancelar e voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>
