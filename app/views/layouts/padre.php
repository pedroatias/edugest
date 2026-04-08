<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - EduGest' : 'EduGest' ?></title>
    <?= csrfMeta() ?>
    <meta name="vapid-key" content="<?= VAPID_PUBLIC_KEY ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script>const APP_URL = '<?= APP_URL ?>';</script>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="logo-area">
        <h5><i class="fas fa-graduation-cap me-2"></i>EduGest</h5>
        <small class="text-white-50"><?= e(Session::userFullName()) ?></small>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a href="<?= url('inicio') ?>" class="nav-link <?= isActive('inicio') ?>">
                <i class="fas fa-home"></i> Inicio
            </a>
        </li>
        <div class="nav-section">Academico</div>
        <li class="nav-item">
            <a href="<?= url('academico') ?>" class="nav-link <?= isActive('academico') ?>">
                <i class="fas fa-book"></i> Academico
            </a>
        </li>
        <div class="nav-section">Finanzas</div>
        <li class="nav-item">
            <a href="<?= url('pagos') ?>" class="nav-link <?= isActive('pagos') ?>">
                <i class="fas fa-credit-card"></i> Pagos y Cartera
            </a>
        </li>
        <div class="nav-section">Documentos</div>
        <li class="nav-item">
            <a href="<?= url('boletines') ?>" class="nav-link <?= isActive('boletines') ?>">
                <i class="fas fa-file-pdf"></i> Boletines
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= url('matriculas') ?>" class="nav-link <?= isActive('matriculas') ?>">
                <i class="fas fa-user-graduate"></i> Matriculas
            </a>
        </li>
    </ul>
</nav>

<!-- Main -->
<div class="main-content">
    <!-- Topbar -->
    <div class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <span class="inst-name"><?= e(Session::get('inst_nombre','Instituto')) ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="anio-badge"><?= date('Y') ?></span>
            <!-- Bell -->
            <div class="dropdown position-relative">
                <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger notif-badge" id="notif-badge" style="display:none">0</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end notif-dropdown" id="notif-list"></ul>
            </div>
            <!-- User -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i><?= e(Session::userFullName()) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i>Cambiar Contrasena</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesion</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <?= flashAlert() ?>
        <?php $content(); ?>
    </div>
</div>

<!-- Session timeout modal -->
<div class="modal fade" id="SessionTimeOut" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h6 class="modal-title"><i class="fas fa-clock me-2"></i>Sesion por vencer</h6>
            </div>
            <div class="modal-body small">Mueva el mouse o haga clic para mantener la sesion activa.</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<script>
$('#sidebarToggle').click(() => $('#sidebar').toggleClass('show'));
</script>
</body>
</html>