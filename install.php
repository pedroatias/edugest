<?php
/**
 * EduGest - Asistente de Instalacion
 * Eliminar este archivo despues de instalar
 */
define('INSTALL_MODE', true);
$step = (int)($_GET['step'] ?? 1);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        // Test DB connection
        $host   = $_POST['db_host'] ?? 'localhost';
        $name   = $_POST['db_name'] ?? '';
        $user   = $_POST['db_user'] ?? '';
        $pass   = $_POST['db_pass'] ?? '';
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            // Save config
            $config = file_get_contents(__DIR__ . '/app/config/config.php');
            $config = str_replace("define('DB_HOST', 'localhost')", "define('DB_HOST', '$host')", $config);
            $config = str_replace("define('DB_NAME', 'tu_base_de_datos')", "define('DB_NAME', '$name')", $config);
            $config = str_replace("define('DB_USER', 'tu_usuario')", "define('DB_USER', '$user')", $config);
            $config = str_replace("define('DB_PASS', 'tu_contrasena')", "define('DB_PASS', '$pass')", $config);
            $appUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/install.php');
            $config = str_replace("define('APP_URL', 'http://localhost/edugest')", "define('APP_URL', '" . rtrim($appUrl,'/') . "')", $config);
            file_put_contents(__DIR__ . '/app/config/config.php', $config);
            header('Location: install.php?step=3');
            exit;
        } catch (PDOException $e) {
            $error = 'Error de conexion: ' . $e->getMessage();
        }
    }
    if ($step === 3) {
        // Run SQL
        $config = include __DIR__ . '/app/config/config.php';
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $sql = file_get_contents(__DIR__ . '/sql/edugest_schema.sql');
            $pdo->exec($sql);
            header('Location: install.php?step=4');
            exit;
        } catch (Exception $e) {
            $error = 'Error al crear tablas: ' . $e->getMessage();
        }
    }
    if ($step === 4) {
        // Create admin user
        $nombres   = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $password  = $_POST['password'] ?? '';
        define('ROOT_PATH', __DIR__);
        define('APP_PATH', __DIR__ . '/app');
        require_once APP_PATH . '/config/config.php';
        require_once APP_PATH . '/config/database.php';
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        Database::execute("UPDATE usuarios SET username=?,password=?,nombres=?,apellidos=?,email=? WHERE id=1",
            [$username, $hash, $nombres, $apellidos, $email]);
        $instNombre = trim($_POST['inst_nombre'] ?? 'Mi Institucion');
        Database::execute("UPDATE instituciones SET nombre=?,email=? WHERE id=1", [$instNombre, $email]);
        header('Location: install.php?step=5');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EduGest - Instalacion</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-7">
<div class="card shadow">
<div class="card-header bg-primary text-white">
    <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>EduGest - Asistente de Instalacion</h4>
</div>
<div class="card-body">

<!-- Progress -->
<div class="mb-4">
<div class="d-flex justify-content-between mb-2">
    <?php for($i=1;$i<=5;$i++): ?>
    <div class="text-center" style="width:18%">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center <?= $i<=$step?'bg-primary text-white':'bg-light border' ?>" style="width:35px;height:35px;font-size:.85rem">
            <?php if($i<$step): ?><i class="fas fa-check"></i><?php else: echo $i; endif; ?>
        </div>
        <div class="small mt-1 text-muted"><?= ['','Requisitos','Base Datos','Tablas','Configurar','Listo'][$i] ?></div>
    </div>
    <?php endfor; ?>
</div>
<div class="progress" style="height:6px">
    <div class="progress-bar bg-primary" style="width:<?= (($step-1)/4)*100 ?>%"></div>
</div>
</div>

<?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<?php if($step === 1): ?>
<h5><i class="fas fa-server me-2"></i>Requisitos del Servidor</h5>
<?php
$checks = [
    'PHP 8.0+'    => version_compare(PHP_VERSION, '8.0.0', '>='),
    'PDO'         => extension_loaded('pdo'),
    'PDO MySQL'   => extension_loaded('pdo_mysql'),
    'GD'          => extension_loaded('gd'),
    'mbstring'    => extension_loaded('mbstring'),
    'openssl'     => extension_loaded('openssl'),
    'mod_rewrite' => function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true,
];
$allOk = !in_array(false, $checks);
?>
<table class="table table-sm mt-3">
<?php foreach($checks as $name => $ok): ?>
<tr>
    <td><?= $name ?></td>
    <td><span class="badge <?= $ok?'bg-success':'bg-danger' ?>"><?= $ok?'OK':'FALTA' ?></span></td>
</tr>
<?php endforeach; ?>
</table>
<?php if($allOk): ?>
<a href="install.php?step=2" class="btn btn-primary">Siguiente <i class="fas fa-arrow-right ms-2"></i></a>
<?php else: ?>
<div class="alert alert-warning">Resuelva los requisitos antes de continuar.</div>
<?php endif; ?>

<?php elseif($step === 2): ?>
<h5><i class="fas fa-database me-2"></i>Configuracion de Base de Datos</h5>
<p class="text-muted small">Cree la base de datos en cPanel primero, luego ingrese los datos aqui.</p>
<form method="POST">
<div class="mb-3"><label class="form-label">Host</label><input name="db_host" class="form-control" value="localhost" required></div>
<div class="mb-3"><label class="form-label">Nombre de la BD</label><input name="db_name" class="form-control" placeholder="Ej: mibd_edugest" required></div>
<div class="mb-3"><label class="form-label">Usuario MySQL</label><input name="db_user" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Contrasena MySQL</label><input type="password" name="db_pass" class="form-control"></div>
<button class="btn btn-primary">Probar y Continuar <i class="fas fa-arrow-right ms-2"></i></button>
</form>

<?php elseif($step === 3): ?>
<h5><i class="fas fa-table me-2"></i>Crear Tablas</h5>
<p>Se crearan todas las tablas necesarias en la base de datos.</p>
<form method="POST">
<button class="btn btn-primary btn-lg w-100">Crear Tablas Ahora <i class="fas fa-play ms-2"></i></button>
</form>

<?php elseif($step === 4): ?>
<h5><i class="fas fa-cog me-2"></i>Configuracion Inicial</h5>
<form method="POST">
<div class="mb-3"><label class="form-label">Nombre de la Institucion</label><input name="inst_nombre" class="form-control" required></div>
<div class="row">
    <div class="col"><div class="mb-3"><label class="form-label">Nombres del Admin</label><input name="nombres" class="form-control" required></div></div>
    <div class="col"><div class="mb-3"><label class="form-label">Apellidos</label><input name="apellidos" class="form-control" required></div></div>
</div>
<div class="mb-3"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Usuario Admin</label><input name="username" class="form-control" value="admin" required></div>
<div class="mb-3"><label class="form-label">Contrasena</label><input type="password" name="password" class="form-control" minlength="8" required></div>
<button class="btn btn-primary">Finalizar Instalacion <i class="fas fa-check ms-2"></i></button>
</form>

<?php elseif($step === 5): ?>
<div class="text-center py-4">
<i class="fas fa-check-circle text-success fa-5x mb-3"></i>
<h4 class="text-success">Instalacion Completada</h4>
<p class="text-muted">EduGest se ha instalado correctamente.</p>
<div class="alert alert-warning">
    <strong>Importante:</strong> Elimine el archivo <code>install.php</code> del servidor por seguridad.
</div>
<a href="<?= isset($_SERVER['HTTPS'])?'https':'http' ?>://<?= $_SERVER['HTTP_HOST'] ?>/login" class="btn btn-primary btn-lg">
    <i class="fas fa-sign-in-alt me-2"></i>Ir al Login
</a>
</div>
<?php endif; ?>

</div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>