<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($title) ?> - <?= ($APP_NAME) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f0f2f5;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.875rem 1.25rem;
            margin: 0.375rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        .main-content {
            padding: 2rem;
        }
        .card {
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: none;
            border-radius: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f0f2f5;
            font-weight: 600;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.25rem;
        }
        .card-body {
            padding: 1.5rem;
        }
        .table-responsive {
            border-radius: 0.5rem;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
        }
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
        }
        .btn-outline-primary:hover {
            background-color: #667eea;
            border-color: #667eea;
        }
        .alert {
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
        }
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
            color: #856404;
        }
        .badge {
            padding: 0.5em 0.75em;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .foto-alumno {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .foto-alumno-lg {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .stats-card {
            border-left: 5px solid #667eea;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        }
        .stats-card:hover {
            border-left-color: #764ba2;
        }
        .stats-card .stats-icon {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stats-card .stats-number {
            font-size: 2.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 2px solid #e0e0e0;
            padding: 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            .main-content {
                padding: 1rem;
            }
        }
    </style>
    <link rel="stylesheet" href="/css/theme.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <div class="d-flex align-items-center justify-content-between px-3">
                        <a href="/" class="d-flex align-items-center text-white text-decoration-none">
                            <i class="bi bi-mortarboard-fill me-2 fs-4"></i>
                            <span class="fs-5 fw-bold">SIGA</span>
                        </a>
                        <button class="btn btn-outline-secondary d-md-none nav-toggle" type="button" aria-controls="primaryNavigation" aria-expanded="false" aria-label="Abrir o cerrar navegación">
                            <i class="bi bi-list" aria-hidden="true"></i>
                        </button>
                    </div>
                    <hr class="text-white">
                    <div class="collapse d-md-block" id="primaryNavigation">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'dashboard/index.html'): ?>active<?php endif; ?>" href="/">
                                <i class="bi bi-house-door-fill"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'alumnos/index.html' || $content == 'alumnos/form.html' || $content == 'alumnos/ver.html' || $content == 'alumnos/buscar.html'): ?>active<?php endif; ?>" href="/alumnos">
                                <i class="bi bi-people-fill"></i>
                                Alumnos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'notas/index.html' || $content == 'notas/form.html' || $content == 'notas/historial.html' || $content == 'notas/buscar.html'): ?>active<?php endif; ?>" href="/notas">
                                <i class="bi bi-journal-text"></i>
                                Notas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'reportes/alumnos.html' || $content == 'reportes/notas.html' || $content == 'reportes/alumno.html'): ?>active<?php endif; ?>" href="/reportes/alumnos">
                                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                                Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <hr class="text-white">
                            <span class="text-white-50 small px-3">Catálogos</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'catalogos/carreras/index.html' || $content == 'catalogos/carreras/form.html'): ?>active<?php endif; ?>" href="/carreras">
                                <i class="bi bi-book-fill"></i>
                                Carreras
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'catalogos/cursos/index.html' || $content == 'catalogos/cursos/form.html'): ?>active<?php endif; ?>" href="/cursos">
                                <i class="bi bi-book-half"></i>
                                Cursos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'catalogos/semestres/index.html' || $content == 'catalogos/semestres/form.html'): ?>active<?php endif; ?>" href="/semestres">
                                <i class="bi bi-calendar3"></i>
                                Semestres
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'catalogos/secciones/index.html' || $content == 'catalogos/secciones/form.html'): ?>active<?php endif; ?>" href="/secciones">
                                <i class="bi bi-grid-3x3"></i>
                                Secciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($content == 'ajustes/index.html'): ?>active<?php endif; ?>" href="/ajustes">
                                <i class="bi bi-sliders2"></i>
                                <span data-i18n="Ajustes">Ajustes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <hr class="text-white">
                            <a class="nav-link text-warning" href="/logout">
                                <i class="bi bi-box-arrow-right"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= ($error)."
" ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($success) && $success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= ($success)."
" ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Confirmación para acciones de desactivación
        function confirmDesactivacion(message) {
            return confirm(message || '¿Está seguro de que desea desactivar este registro?');
        }
        
        // Vista previa de imagen
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.querySelector('.nav-toggle')?.addEventListener('click', function() {
            const navigation = document.getElementById('primaryNavigation');
            const expanded = navigation.classList.toggle('show');
            this.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });
    </script>
    <script src="/js/settings.js" defer></script>
</body>
</html>
