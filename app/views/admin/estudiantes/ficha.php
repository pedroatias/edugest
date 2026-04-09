<?php
// app/views/admin/estudiantes/ficha.php
$est = $data['estudiante'];
$pagos = $data['pagos'] ?? [];
$calificaciones = $data['calificaciones'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Ficha del Estudiante', 'active' => 'estudiantes']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Ficha del Estudiante</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/admin/estudiantes">Estudiantes</a></li>
            <li class="breadcrumb-item active">Ficha</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">
                <img class="profile-user-img img-fluid img-circle" src="<?= !empty($est['foto']) ? '/uploads/fotos/'.$est['foto'] : '/assets/img/avatar.png' ?>" alt="Foto">
              </div>
              <h3 class="profile-username text-center"><?= htmlspecialchars($est['nombres'].' '.$est['apellidos']) ?></h3>
              <p class="text-muted text-center"><?= htmlspecialchars($est['grado_nombre'].' - '.$est['seccion_nombre']) ?></p>
              <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Cédula</b><span class="float-right"><?= htmlspecialchars($est['cedula'] ?? '-') ?></span></li>
                <li class="list-group-item"><b>Nacimiento</b><span class="float-right"><?= $est['fecha_nacimiento'] ?? '-' ?></span></li>
                <li class="list-group-item"><b>Estado</b><span class="float-right badge badge-<?= $est['activo'] ? 'success' : 'danger' ?>"><?= $est['activo'] ? 'Activo' : 'Inactivo' ?></span></li>
              </ul>
              <a href="/admin/estudiantes/edit/<?= $est['id'] ?>" class="btn btn-primary btn-block"><i class="fas fa-edit"></i> Editar</a>
            </div>
          </div>
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Representante</h3></div>
            <div class="card-body">
              <strong><?= htmlspecialchars($est['rep_nombre'] ?? '-') ?></strong><br>
              <small class="text-muted"><?= htmlspecialchars($est['rep_parentesco'] ?? '') ?></small><br>
              <i class="fas fa-phone mr-1"></i><?= htmlspecialchars($est['rep_telefono'] ?? '-') ?><br>
              <i class="fas fa-envelope mr-1"></i><?= htmlspecialchars($est['rep_email'] ?? '-') ?><br>
              <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($est['rep_direccion'] ?? '-') ?>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="card">
            <div class="card-header p-2">
              <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#pagos" data-toggle="tab">Pagos</a></li>
                <li class="nav-item"><a class="nav-link" href="#notas" data-toggle="tab">Calificaciones</a></li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content">
                <div class="tab-pane active" id="pagos">
                  <table class="table table-sm table-hover">
                    <thead><tr><th>Concepto</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                      <?php foreach($pagos as $p): ?>
                      <tr>
                        <td><?= htmlspecialchars($p['concepto']) ?></td>
                        <td>$ <?= number_format($p['monto'], 0, ',', '.') ?></td>
                        <td><span class="badge badge-<?= $p['estado'] === 'verificado' ? 'success' : ($p['estado'] === 'pendiente' ? 'warning' : 'info') ?>"><?= ucfirst($p['estado']) ?></span></td>
                        <td><?= $p['fecha_pago'] ?? $p['created_at'] ?></td>
                      </tr>
                      <?php endforeach; ?>
                      <?php if(!$pagos): ?><tr><td colspan="4" class="text-center text-muted">Sin registros de pago</td></tr><?php endif; ?>
                    </tbody>
                  </table>
                </div>
                <div class="tab-pane" id="notas">
                  <table class="table table-sm table-hover">
                    <thead><tr><th>Asignatura</th><th>Período</th><th>Nota</th><th>Estado</th></tr></thead>
                    <tbody>
                      <?php foreach($calificaciones as $c): ?>
                      <tr>
                        <td><?= htmlspecialchars($c['asignatura_nombre']) ?></td>
                        <td><?= htmlspecialchars($c['periodo_nombre']) ?></td>
                        <td><strong><?= number_format($c['nota_final'], 1) ?></strong></td>
                        <td><span class="badge badge-<?= $c['nota_final'] >= 6 ? 'success' : 'danger' ?>"><?= $c['nota_final'] >= 6 ? 'Aprobado' : 'Reprobado' ?></span></td>
                      </tr>
                      <?php endforeach; ?>
                      <?php if(!$calificaciones): ?><tr><td colspan="4" class="text-center text-muted">Sin calificaciones</td></tr><?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>