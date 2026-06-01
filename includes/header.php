<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $titulo ?? 'Sistema'; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>


    <link rel="stylesheet" href="../assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/4.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <script src="https://kit.fontawesome.com/9e914e4c6c.js" crossorigin="anonymous"></script>

    <style>
        * {
            cursor: url('../assets/cursor/lapis.png') 1 1, auto;
        }

        button:hover, a:hover, .card:hover {
            cursor: url("../assets/cursor/lapis-hover.png") 1 31, pointer;
        }

        [data-kanban-card]:hover,
        [data-kanban-card]:hover * {
            cursor: url("../assets/cursor/mao-aberta.cur"), grab !important;
        }

    </style>
</head>
<?php date_default_timezone_set('America/Sao_Paulo'); ?>
<body class="min-h-screen bg-slate-50 text-slate-900">
