<?php

require_once "../includes/verificar_login.php";
require_once "../includes/funcoes.php";
require_once "../config/conexao.php";

$idUser = $_SESSION["usuario_id"];

$stmt = $pdo->prepare("
    SELECT id_user, nome, email, senha, foto_perfil
    FROM usuarios
    WHERE id_user = ?
");
$stmt->execute([$idUser]);
$usuario = $stmt->fetch(PDO::FETCH_OBJ);

if (!$usuario) {
    redirecionarComFlash("perfil.php", "error", "Usuario nao encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validarCsrf();

    $nome = trim($_POST["nome"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $senhaAtual = (string) ($_POST["senha_atual"] ?? "");
    $novaSenha = (string) ($_POST["nova_senha"] ?? "");
    $confirmarSenha = (string) ($_POST["confirmar_senha"] ?? "");

    if ($nome === '' || $email === '') {
        redirecionarComFlash("editar.php", "error", "Preencha nome e email.");
    }

    $tamNome = function_exists("mb_strlen") ? mb_strlen($nome, "UTF-8") : strlen($nome);
    $tamEmail = function_exists("mb_strlen") ? mb_strlen($email, "UTF-8") : strlen($email);

    if ($tamNome > 100) {
        redirecionarComFlash("editar.php", "error", "O nome deve ter no maximo 100 caracteres.");
    }

    if ($tamEmail > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirecionarComFlash("editar.php", "error", "Email invalido.");
    }

    $sql = "SELECT id_user FROM usuarios WHERE email = ? AND id_user <> ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $idUser]);

    if ($stmt->fetch(PDO::FETCH_OBJ)) {
        redirecionarComFlash("editar.php", "error", "Este email ja esta cadastrado.");
    }

    $atualizarSenha = $senhaAtual !== '' || $novaSenha !== '' || $confirmarSenha !== '';

    if ($atualizarSenha) {
        if ($senhaAtual === '' || $novaSenha === '' || $confirmarSenha === '') {
            redirecionarComFlash("editar.php", "error", "Preencha a senha atual, a nova senha e a confirmacao.");
        }

        if (!password_verify($senhaAtual, $usuario->senha ?? '')) {
            redirecionarComFlash("editar.php", "error", "Senha atual incorreta.");
        }

        if (strlen($novaSenha) < 8) {
            redirecionarComFlash("editar.php", "error", "A nova senha deve ter no minimo 8 caracteres.");
        }

        if ($novaSenha !== $confirmarSenha) {
            redirecionarComFlash("editar.php", "error", "A confirmacao da nova senha nao confere.");
        }
    }

    try {
        if ($atualizarSenha) {
            $sql = "UPDATE usuarios
                    SET nome = ?, email = ?, senha = ?
                    WHERE id_user = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nome,
                $email,
                password_hash($novaSenha, PASSWORD_DEFAULT),
                $idUser
            ]);
        } else {
            $sql = "UPDATE usuarios
                    SET nome = ?, email = ?
                    WHERE id_user = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nome,
                $email,
                $idUser
            ]);
        }

        if ($atualizarSenha) {
            session_regenerate_id(true);
        }

        $_SESSION["usuario_nome"] = $nome;
        $_SESSION["usuario_email"] = $email;

        redirecionarComFlash("perfil.php", "success", "Perfil atualizado com sucesso.");
    } catch (PDOException $e) {
        error_log("Erro ao atualizar perfil: " . $e->getMessage());
        redirecionarComFlash("editar.php", "error", "Erro ao atualizar perfil.");
    }
}

$titulo = "Editar perfil";
require_once "../includes/header.php";
?>

<main class="form-page">
    <div class="form-notebook">
        <div class="spiral-bar" aria-hidden="true">
            <?php for ($i = 0; $i < 12; $i++): ?>
                <span class="spiral-ring"></span>
            <?php endfor; ?>
        </div>

        <div class="form-inner">
            <header class="form-header">
                <h1 class="form-title">Editar perfil</h1>
                <p class="form-subtitle">Atualize seus dados e, se quiser, troque a senha da conta.</p>
            </header>

            <form action="editar.php" method="POST">
                <?= csrfInput(); ?>

                <div class="form-group">
                    <label for="nome" class="form-label">Nome</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        class="form-control"
                        value="<?= htmlspecialchars($usuario->nome); ?>"
                        maxlength="100"
                        minlength="2"
                        autocomplete="name"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($usuario->email); ?>"
                        maxlength="100"
                        autocomplete="username"
                        required
                    >
                </div>

                <section class="form-section">
                    <h2 class="form-section__title">Troca de senha</h2>
                    <p class="form-section__text">Preencha apenas se quiser alterar a senha atual.</p>
                </section>

                <div class="form-group">
                    <label for="senha_atual" class="form-label">Senha atual</label>
                    <input
                        type="password"
                        id="senha_atual"
                        name="senha_atual"
                        class="form-control"
                        autocomplete="current-password"
                    >
                </div>

                <div class="form-group">
                    <label for="nova_senha" class="form-label">Nova senha</label>
                    <input
                        type="password"
                        id="nova_senha"
                        name="nova_senha"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>

                <div class="form-group">
                    <label for="confirmar_senha" class="form-label">Confirmar nova senha</label>
                    <input
                        type="password"
                        id="confirmar_senha"
                        name="confirmar_senha"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Salvar alteracoes</button>
                    <a href="perfil.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once "../includes/footer.php"; ?>
