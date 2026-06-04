<?php
require_once "../includes/funcoes.php";

iniciarSessaoSegura();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

$titulo = "Acesse sua conta";
require_once "../includes/header.php";
?>


<canvas id="mouse-trail"></canvas>

<main class=" login-page">
    <section class="login-brand-panel" aria-label="NEXO">
        <div class="login-brand-card">
            <div class="login-logo-frame">
                <img src="../assets/image/logotipo.png" alt="NEXO">
            </div>

            <h1 class="login-brand-title">Projetos, tarefas e ideias em um só flu<img src="../assets/image/logo.png" class="logo-text" alt="NEXO">o.</h1>

            <p class="login-brand-copy">
                Organize o trabalho com clareza, acompanhe o progresso e mantenha cada etapa conectada ao seu time.
            </p>
        </div>
    </section>

    <section class="login-auth-panel" aria-label="Acesso">
        <div class="login-auth-card">
            <div class="login-tabs" role="tablist" aria-label="Alternar formulario">
                <button
                    type="button"
                    class="active"
                    id="btnEntrar"
                    role="tab"
                    aria-selected="true"
                    aria-controls="formEntrar"
                    onclick="switchTab('entrar')"
                >
                    Entrar
                </button>
                <button
                    type="button"
                    class="inactive"
                    id="btnCriar"
                    role="tab"
                    aria-selected="false"
                    aria-controls="formCriar"
                    onclick="switchTab('criar')"
                >
                    Criar
                </button>
            </div>

            <div class="login-form-shell">
                <div class="login-rings" aria-hidden="true">
                    <?php for ($i = 0; $i < 7; $i++): ?>
                        <span class="login-ring"></span>
                    <?php endfor; ?>
                </div>

                <form
                    action="autenticar.php"
                    method="POST"
                    id="formEntrar"
                    class="login-form"
                    role="tabpanel"
                    aria-labelledby="btnEntrar"
                >
                    <?= csrfInput(); ?>

                    <div class="login-field">
                        <label for="emailEntrar" class="login-label">Email</label>
                        <input
                            type="email"
                            id="emailEntrar"
                            name="email"
                            class="login-input"
                            maxlength="100"
                            autocomplete="username"
                            required
                        >
                    </div>

                    <div class="login-field">
                        <label for="senha" class="login-label">Senha</label>
                        <div class="login-password-wrap">
                            <input
                                type="password"
                                id="senha"
                                name="senha"
                                class="login-input"
                                minlength="8"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="login-eye" id="toggleSenha" aria-label="Mostrar ou ocultar senha">
                                <i class="fi fi-sr-eye-crossed"></i>
                            </button>
                        </div>
                    </div>

                    <div class="login-submit-row">
                        <button type="submit" class="btn btn-primary login-submit">Avancar</button>
                    </div>
                </form>

                <form
                    action="cadastrar.php"
                    method="POST"
                    id="formCriar"
                    class="login-form hidden"
                    role="tabpanel"
                    aria-labelledby="btnCriar"
                    hidden
                >
                    <?= csrfInput(); ?>

                    <!-- ETAPA 1 -->
                    <div id="cadastroEtapa1">

                        <div class="login-field">
                            <label for="nomeCriar" class="login-label">Nome</label>
                            <input
                                type="text"
                                id="nomeCriar"
                                name="nome"
                                class="login-input"
                                maxlength="100"
                                autocomplete="name"
                                required
                            >
                        </div>

                        <div class="login-field">
                            <label for="emailCriar" class="login-label">Email</label>
                            <input
                                type="email"
                                id="emailCriar"
                                name="email"
                                class="login-input"
                                maxlength="100"
                                autocomplete="username"
                                required
                            >
                        </div>

                        <div class="login-field">
                            <label for="senhaCriar" class="login-label">Senha</label>

                            <div class="login-password-wrap">
                                <input
                                    type="password"
                                    id="senhaCriar"
                                    name="senha"
                                    class="login-input"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="login-eye"
                                    id="toggleSenhaCriar"
                                >
                                    <i class="fi fi-sr-eye-crossed"></i>
                                </button>
                            </div>
                        </div>

                        <div class="login-field">
                            <label for="confirmarSenhaCriar" class="login-label">Confirmar senha</label>

                            <div class="login-password-wrap">
                                <input
                                    type="password"
                                    id="confirmarSenhaCriar"
                                    name="confirmar_senha"
                                    class="login-input"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >
                            </div>
                        </div>

                        <div class="login-submit-row">
                            <button
                                type="button"
                                class="btn btn-primary login-submit"
                                onclick="avancarCadastro()"
                            >
                                Avançar
                            </button>
                        </div>

                    </div>

                    <!-- ETAPA 2 -->
                    <div id="cadastroEtapa2" hidden>

                        <h3 class="avatar-step-title">
                            Escolha seu avatar
                        </h3>

                        <div class="avatar-choice">

                            <label>
                                <input
                                    type="radio"
                                    name="tipo_avatar"
                                    value="default"
                                    checked
                                >
                                Avatar padrão
                            </label>

                            <label>
                                <input
                                    type="radio"
                                    name="tipo_avatar"
                                    value="personalizado"
                                >
                                Criar avatar personalizado
                            </label>

                        </div>

                        <div
                            id="avatarEditor"
                            class="avatar-editor"
                            hidden
                        >

                            <p class="text-md font-medium text-slate-700">
                                Desenhe o <span id="nomeAvatar" class="font-bold italic"></span>:
                            </p>
                            <div class="avatar-canvas-wrap">
                                <canvas
                                    id="avatarCanvas"
                                    width="280"
                                    height="280"
                                ></canvas>
                            </div>

                            <div class="avatar-tools">

                                <input
                                    type="color"
                                    id="avatarCor"
                                    value="#000000"
                                >

                                <input
                                    type="range"
                                    id="avatarEspessura"
                                    min="1"
                                    max="20"
                                    value="4"
                                >

                                <button
                                    type="button"
                                    class="btn"
                                    id="btnDesfazer"
                                >
                                    <i class="fa-solid fa-rotate-left" style="color: #305cde;"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn"
                                    id="btnLimpar"
                                >
                                    <i class="fa-solid fa-trash-can" style="color: #305cde;"></i>
                                </button>

                            </div>

                        </div>

                        <div class="avatar-actions">

                            <button
                                type="button"
                                class="btn"
                                onclick="voltarCadastro()"
                            >
                                Voltar
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Criar Conta
                            </button>

                        </div>

                    </div>
                
                <input
                    type="hidden"
                    name="avatar_desenho"
                    id="avatarDesenho"
                >
                
                </form>
            </div>
        </div>
    </section>
</main>


<script>
  function limparFormulariosLogin() {
    document.querySelectorAll('#formEntrar, #formCriar').forEach(function (form) {
      form.reset();
    });

    document.querySelectorAll('input[type="password"]').forEach(function (input) {
      input.type = 'password';
    });

    const toggleSenha = document.getElementById('toggleSenha');
    const toggleSenhaCriar = document.getElementById('toggleSenhaCriar');

    if (toggleSenha) {
      toggleSenha.innerHTML = '<i class="fi fi-sr-eye-crossed"></i>';
    }

    if (toggleSenhaCriar) {
      toggleSenhaCriar.innerHTML = '<i class="fi fi-sr-eye-crossed"></i>';
    }
  }

  window.addEventListener('pageshow', function (event) {
    const navegacao = performance.getEntriesByType('navigation')[0];
    const voltouPeloHistorico = event.persisted || (navegacao && navegacao.type === 'back_forward');

    if (voltouPeloHistorico) {
      limparFormulariosLogin();
    }
  });

  function switchTab(tab) {
    const formEntrar = document.getElementById('formEntrar');
    const formCriar  = document.getElementById('formCriar');
    const btnEntrar  = document.getElementById('btnEntrar');
    const btnCriar   = document.getElementById('btnCriar');

    if (tab === 'entrar') {
      formEntrar.classList.remove('hidden');
      formCriar.classList.add('hidden');
      formEntrar.hidden = false;
      formCriar.hidden = true;
      btnEntrar.classList.replace('inactive', 'active');
      btnCriar.classList.replace('active', 'inactive');
      btnEntrar.setAttribute('aria-selected', 'true');
      btnCriar.setAttribute('aria-selected', 'false');
    } else {
      formCriar.classList.remove('hidden');
      formEntrar.classList.add('hidden');
      formCriar.hidden = false;
      formEntrar.hidden = true;
      btnCriar.classList.replace('inactive', 'active');
      btnEntrar.classList.replace('active', 'inactive');
      btnCriar.setAttribute('aria-selected', 'true');
      btnEntrar.setAttribute('aria-selected', 'false');
    }
  }

  function configurarToggleSenha(inputId, buttonId) {
    const input = document.getElementById(inputId);
    const button = document.getElementById(buttonId);

    if (!input || !button) {
      return;
    }

    button.addEventListener('click', function () {
      const deveMostrar = input.type === 'password';
      input.type = deveMostrar ? 'text' : 'password';
      button.innerHTML = deveMostrar
        ? '<i class="fi fi-sr-eye"></i>'
        : '<i class="fi fi-sr-eye-crossed"></i>';
    });
  }

  configurarToggleSenha('senha', 'toggleSenha');
  configurarToggleSenha('senhaCriar', 'toggleSenhaCriar');

  function avancarCadastro() {

        const nome = document.getElementById('nomeCriar');
        const email = document.getElementById('emailCriar');
        const senha = document.getElementById('senhaCriar');
        const confirmarSenha = document.getElementById('confirmarSenhaCriar');

        if (
            !nome.checkValidity() ||
            !email.checkValidity() ||
            !senha.checkValidity() ||
            !confirmarSenha.checkValidity()
        ) {
            nome.reportValidity();
            email.reportValidity();
            senha.reportValidity();
            confirmarSenha.reportValidity();
            return;
        }

        if (senha.value !== confirmarSenha.value) {
            confirmarSenha.setCustomValidity('A confirmacao de senha nao confere.');
            confirmarSenha.reportValidity();
            confirmarSenha.setCustomValidity('');
            return;
        }

        document.getElementById('nomeAvatar').textContent =
            nome.value;

        document.getElementById('cadastroEtapa1').hidden = true;
        document.getElementById('cadastroEtapa2').hidden = false;
    }

function voltarCadastro() {
    document.getElementById('cadastroEtapa2').hidden = true;
    document.getElementById('cadastroEtapa1').hidden = false;
}

document.querySelectorAll(
    'input[name="tipo_avatar"]'
).forEach(function (radio) {

    radio.addEventListener('change', function () {

        const editor =
            document.getElementById('avatarEditor');

        editor.hidden =
            this.value !== 'personalizado';

    });

});

const avatarEspessura =
    document.getElementById(
        "avatarEspessura"
    );

const avatarCanvas =
    document.getElementById("avatarCanvas");

const avatarCor =
    document.getElementById("avatarCor");

if (avatarCanvas && avatarCor) {

    const ctx =
        avatarCanvas.getContext("2d");

    const strokes = [];

    let desenhando = false;
    let currentStroke = null;

    function getMousePos(event) {

        const rect =
            avatarCanvas.getBoundingClientRect();

        return {
            x: event.clientX - rect.left,
            y: event.clientY - rect.top
        };
    }

    function drawStroke(stroke) {

        const points = stroke.points;

        if (points.length < 2) {
            return;
        }

        ctx.beginPath();

        ctx.moveTo(
            points[0].x,
            points[0].y
        );

        for (
            let i = 1;
            i < points.length - 1;
            i++
        ) {

            const current = points[i];
            const next = points[i + 1];

            const midX =
                (current.x + next.x) / 2;

            const midY =
                (current.y + next.y) / 2;

            ctx.quadraticCurveTo(
                current.x,
                current.y,
                midX,
                midY
            );
        }

        const last =
            points[points.length - 1];

        ctx.lineTo(
            last.x,
            last.y
        );

        ctx.lineWidth = stroke.width;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        ctx.strokeStyle =
            stroke.color;

        ctx.stroke();
    }

    function redraw() {

        ctx.clearRect(
            0,
            0,
            avatarCanvas.width,
            avatarCanvas.height
        );

        ctx.fillStyle = "#FFFFFF";

        ctx.fillRect(
            0,
            0,
            avatarCanvas.width,
            avatarCanvas.height
        );

        for (const stroke of strokes) {
            drawStroke(stroke);
        }
    }

    redraw();

    avatarCanvas.addEventListener(
        "mousedown",
        function (event) {

            desenhando = true;

            currentStroke = {
                color: avatarCor.value,
                width: parseInt(
                    avatarEspessura.value
                ),
                points: []
            };

            currentStroke.points.push(
                getMousePos(event)
            );

            strokes.push(
                currentStroke
            );

        }
    );

    avatarCanvas.addEventListener(
        "mousemove",
        function (event) {

            if (
                !desenhando ||
                !currentStroke
            ) {
                return;
            }

            currentStroke.points.push(
                getMousePos(event)
            );

            redraw();

        }
    );

    avatarCanvas.addEventListener(
        "mouseup",
        function () {

            desenhando = false;
            currentStroke = null;

        }
    );

    avatarCanvas.addEventListener(
        "mouseleave",
        function () {

            desenhando = false;
            currentStroke = null;

        }
    );

    const btnDesfazer =
        document.getElementById(
            "btnDesfazer"
        );

    btnDesfazer.addEventListener(
        "click",
        function () {

            if (strokes.length > 0) {

                strokes.pop();

                redraw();
            }

        }
    );

    const btnLimpar =
        document.getElementById(
            "btnLimpar"
        );

    btnLimpar.addEventListener(
        "click",
        function () {

            strokes.length = 0;

            redraw();

        }
    );

    const formCriar =
    document.getElementById("formCriar");

    const avatarDesenho =
        document.getElementById("avatarDesenho");

    formCriar.addEventListener(
    "submit",
    function () {

        avatarDesenho.value =
            avatarCanvas.toDataURL(
                "image/png"
            );

        console.log(
            avatarDesenho.value.length
        );

    }
);

}
</script>



<?php require_once "../includes/footer.php"; ?>
