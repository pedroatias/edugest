<?php
// app/views/admin/docentes/index.php
$docentes = $data['docentes'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Docentes', 'active' => 'docentes']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Gestión de Docentes</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Docentes</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-md-8">
              <form class="form-inline" method="GET">
                <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="Buscar docente..." value="<?= htmlspecialchars($data['fq'] ?? '') ?>">
                <button class="btn btn-sm btn-primary">Buscar</button>
              </form>
            </div>
            <div class="col-md-4 text-right">
              <a href="/admin/docentes/nuevo" class="btn btn-success btn-sm"><i class="fas fa-user-plus"></i> Nuevo Docente</a>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-sm">
            <thead class="thead-light">
              <tr><th>Nombre</th><th>Cédula</th><th>Email</th><th>Asignaturas</th><th>Secciones</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
              <?php foreach($docentes as $d): ?>
              <tr>
                <td>
                  <img src="<?= !empty($d['foto']) ? '/uploads/fotos/'.$d['foto'] : '/assets/img/avatar.png' ?>" width="32" height="32" class="img-circle mr-2">
                  <strong><?= htmlspecialchars($d['nombre']) ?></strong>
                </td>
                <td><?= htmlspecialchars($d['cedula'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['email'] ?? '-') ?></td>
                <td><span class="badge badge-info"><?= $d['num_asignaturas'] ?? 0 ?></span></td>
                <td><span class="badge badge-secondary"><?= $d['num_secciones'] ?? 0 ?></span></td>
                <td><span class="badge badge-<?= $d['activo'] ? 'success':'secondary' ?>"><?= $d['activo'] ? 'Activo':'Inactivo' ?></span></td>
                <td>
                  <a href="/admin/docentes/edit/<?= $d['id'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                  <a href="/admin/docentes/asignaturas/<?= $d['id'] ?>" class="btn btn-xs btn-info" title="Asignar materias"><i class="fas fa-book"></i></a>
                  <?php if($d['activo']): ?>
                    <a href="/admin/docentes/desactivar/<?= $d['id'] ?>" class="btn btn-xs btn-secondary" onclick="return confirm('Desactivar docente?')"><i class="fas fa-ban"></i></a>
                  <?php else: ?>
                    <a href="/admin/docentes/activar/<?= $d['id'] ?>" class="btn btn-xs btn-success"><i class="fas fa-check"></i></a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(!$docentes): ?><tr><td colspan="7" class="text-center text-muted py-3">Sin docentes registrados</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>