<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $titulo_pagina ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/imagens/favicon.ico') ?>" type="image/x-icon">
    <link rel="icon" href="<?= base_url('assets/imagens/favicon.ico') ?>" type="image/x-icon">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/jqvmap/jqvmap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/dist/css/adminlte.min.css') ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/daterangepicker/daterangepicker.css') ?>">
    <!-- summernote -->
    <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/summernote/summernote-bs4.min.css') ?>">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/DataTables/datatables.min.css') ?>">
    <!-- Selectize -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/selectize/css/selectize.bootstrap4.css') ?>">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="<?= base_url('/assets/imagens/loader.png') ?>" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?= base_url('home') ?>" class="brand-link text-center">
                <img src="<?= base_url('assets/imagens/logo_glometal_120.png') ?>" class="img-fluid " alt="Logo">
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                        <li class="nav-item menu-close">
                            <a href="#" class="nav-link">
                                <i class="far fa-edit"></i>
                                <p>
                                    Cadastros
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= base_url('usuarios') ?>"
                                        class="nav-link <?= ($titulo_pagina == 'Usuários' ? 'active' : '') ?>">
                                        <i class="nav-icon"></i>
                                        <p>Usuários</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('clientes') ?>"
                                        class="nav-link <?= ($titulo_pagina == 'Clientes' ? 'active' : '') ?>">
                                        <i class="nav-icon"></i>
                                        <p>Clientes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('fornecedores') ?>"
                                        class="nav-link <?= ($titulo_pagina == 'Fornecedores' ? 'active' : '') ?>">
                                        <i class="nav-icon"></i>
                                        <p>Fornecedores</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('tipos_materiais') ?>"
                                        class="nav-link <?= ($titulo_pagina == 'Tipos de Materiais' ? 'active' : '') ?>">
                                        <i class="nav-icon"></i>
                                        <p>Tipo de Materiais</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('fornos') ?>"
                                        class="nav-link <?= ($titulo_pagina == 'Fornos' ? 'active' : '') ?>">
                                        <i class="nav-icon"></i>
                                        <p>Fornos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('entradas') ?>" class="nav-link <?= ($titulo_pagina == 'Entradas' ? 'active' : '') ?>">
                                <i class="fas fa-arrow-right"></i>
                                <p>Entradas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('processos') ?>"
                                class="nav-link <?= ($titulo_pagina == 'Processos' ? 'active' : '') ?>">
                                <i class="fas fa-hammer"></i>
                                <p>Forno</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('separacao') ?>" class="nav-link <?= ($titulo_pagina == 'Saídas' ? 'active' : '') ?>">
                                <i class="fas fa-arrow-left"></i>
                                <p>Saídas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/login/encerrar') ?>" class="nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <p>Sair</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $titulo_pagina ?></h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">