<?php
// app/views/admin/inscripciones/index.php
$inscripciones = $data['inscripciones'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Solicitudes de Inscripción', 'active' => 'inscripciones']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Solicitudes de Inscripción</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Inscripciones</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <form class="form-inline" method="GET">
            <select name="estado" class="form-control form-control-sm mr-2">
              <option value="">Todos los estados</option>
              <?php foreach(['pendiente','en_revision','aprobada','rechazada'] as $e): ?>
                <option value="<?= $e ?>" <?= ($data['fe'] ?? '') === $e ? 'selected':'' ?>><?= ucfirst(str_replace('_',' ',$e)) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="Buscar nombre..." value="<?= htmlspecialchars($data['fq'] ?? '') ?>">
            <button class="btn btn-sm btn-primary">Filtrar</button>
          </form>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-sm">
            <thead class="thead-light">
              <tr><th>#</th><th>Nombre del Estudiante</th><th>Representante</th><th>Grado Solicitado</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
              <?php foreach($inscripciones as $i): ?>
              <tr>
                <td><?= $i['id'] ?></td>
                <td><?= htmlspecialchars($i['nombre_estudiante']) ?></td>
                <td>
                  <?= htmlspecialchars($i['rep_nombre']) ?><br>
                  <small class="text-muted"><?= htmlspecialchars($i['rep_email']) ?></small>
                </td>
                <td><?= htmlspecialchars($i['grado_solicitado']) ?></td>
                <td><?= date('d/m/Y', strtotime($i['created_at'])) ?></td>
                <td>
                  <span class="badge badge-<?= ['pendiente'=>'secondary','en_revision'=>'info','aprobada'=>'success','rechazada'=>'danger'][$i['estado']] ?? 'light' ?>">
                    <?= ucfirst(str_replace('_',' ',$i['estado'])) ?>
                  </span>
                </td>
                <td>
                  <a href="/admin/inscripciones/ver/<?= $i['id'] ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i> Ver</a>
                  <?php if($i['estado'] === 'pendiente' || $i['estado'] === 'en_revision'): ?>
                    <a href="/admin/inscripciones/aprobar/<?= $i['id'] ?>" class="btn btn-xs btn-success" onclick="return confirm('Aprobar esta inscripción?')"><i class="fas fa-check"></i></a>
                    <a href="/admin/inscripciones/rechazar/<?= $i['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Rechazar esta inscripción?')"><i class="fas fa-times"></i></a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(!$inscripciones): ?><tr><td colspan="7" class="text-center text-muted py-3">Sin solicitudes de inscripción</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>