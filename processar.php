<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método não permitido.');
}

function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$erros = [];

// Reject array-shaped inputs for scalar fields instead of passing them to trim().
foreach (['nome', 'email', 'experiencia', 'turno', 'mensagem'] as $campo) {
    if (isset($_POST[$campo]) && !is_string($_POST[$campo])) {
        $erros[] = 'O campo ' . $campo . ' é inválido.';
    }
}

$nome = trim(is_string($_POST['nome'] ?? null) ? $_POST['nome'] : '');
$email = trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : '');
$experiencia = trim(is_string($_POST['experiencia'] ?? null) ? $_POST['experiencia'] : '');
$turno = trim(is_string($_POST['turno'] ?? null) ? $_POST['turno'] : '');
$mensagem = trim(is_string($_POST['mensagem'] ?? null) ? $_POST['mensagem'] : '');
$mensagem = str_replace("\r\n", "\n", $mensagem);
$oficinas = $_POST['oficinas'] ?? [];

$experienciasPermitidas = [
    'iniciante' => 'Iniciante',
    'intermediario' => 'Intermediário',
    'avancado' => 'Avançado',
];
$turnosPermitidos = [
    'manha' => 'Manhã',
    'noite' => 'Noite',
];
$oficinasPermitidas = [
    'html_css' => 'HTML & CSS',
    'js_dom' => 'JavaScript DOM',
    'php_backend' => 'PHP Backend',
];

if (mb_strlen($nome, 'UTF-8') < 3) {
    $erros[] = 'O nome deve possuir pelo menos 3 caracteres.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um e-mail válido.';
}
if (!in_array($experiencia, array_keys($experienciasPermitidas), true)) {
    $erros[] = 'Selecione um nível de experiência válido.';
}
if (!in_array($turno, array_keys($turnosPermitidos), true)) {
    $erros[] = 'Selecione um turno válido.';
}
if (!is_array($oficinas)) {
    $erros[] = 'A seleção de oficinas é inválida.';
    $oficinas = [];
} elseif (count($oficinas) === 0) {
    $erros[] = 'Selecione pelo menos uma oficina.';
}

$oficinasValidas = [];
foreach ($oficinas as $oficina) {
    if (!is_string($oficina) || !in_array($oficina, array_keys($oficinasPermitidas), true)) {
        $erros[] = 'Foi informada uma oficina inválida.';
        break;
    }
    $oficinasValidas[] = $oficinasPermitidas[$oficina];
}
if (mb_strlen($mensagem, 'UTF-8') > 200) {
    $erros[] = 'A mensagem deve possuir no máximo 200 caracteres.';
}
if ($erros) {
    http_response_code(422);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $erros ? 'Dados inválidos' : 'Inscrição recebida' ?> — Tech Experience 2026</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="result-page">
    <a class="skip-link" href="#conteudo">Pular para o conteúdo principal</a>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="index.html#inicio" aria-label="Tech Experience 2026 — Início">
                <span class="brand-mark" aria-hidden="true">&lt;/&gt;</span>
                <span>Tech<span class="brand-accent">Experience</span><small>2026</small></span>
            </a>
            <a class="button button-outline" href="index.html#inscricao">Voltar ao formulário</a>
        </div>
    </header>
    <main class="result-main" id="conteudo">
        <section class="container result-card" aria-labelledby="result-title">
            <?php if ($erros): ?>
                <span class="result-icon is-error" aria-hidden="true">!</span>
                <p class="section-kicker">VERIFIQUE SEUS DADOS</p>
                <h1 id="result-title">Existem dados inválidos.</h1>
                <p class="result-intro">Confira os itens abaixo e tente novamente.</p>
                <ul class="result-list">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <span class="result-icon" aria-hidden="true">✓</span>
                <p class="section-kicker">INSCRIÇÃO RECEBIDA</p>
                <h1 id="result-title">Sua inscrição foi recebida!</h1>
                <p class="result-intro">Confira os dados que você enviou para o Tech Experience 2026.</p>
                <dl class="result-details">
                    <div class="result-row"><dt>Nome completo</dt><dd><?= e($nome) ?></dd></div>
                    <div class="result-row"><dt>E-mail</dt><dd><?= e($email) ?></dd></div>
                    <div class="result-row"><dt>Experiência</dt><dd><?= e($experienciasPermitidas[$experiencia]) ?></dd></div>
                    <div class="result-row"><dt>Turno</dt><dd><?= e($turnosPermitidos[$turno]) ?></dd></div>
                    <div class="result-row"><dt>Oficinas</dt><dd><?= e(implode(', ', array_unique($oficinasValidas))) ?></dd></div>
                    <?php if ($mensagem !== ''): ?>
                        <div class="result-row"><dt>Motivação</dt><dd><?= e($mensagem) ?></dd></div>
                    <?php endif; ?>
                </dl>
            <?php endif; ?>
            <a class="button button-primary" href="index.html#inscricao">Voltar ao formulário <span aria-hidden="true">↗</span></a>
        </section>
    </main>
    <footer class="result-footer"><div class="container">Projeto acadêmico — Tech Experience 2026.</div></footer>
</body>
</html>
