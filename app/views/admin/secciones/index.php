<?php
// app/views/admin/secciones/index.php
$secciones = $data['secciones'] ?? [];
$grados = $data['grados'] ?? [];
$grado_sel = $data['grado_sel'] ?? null;
?>
<?php $this->layout('layouts/admin', ['title' => 'Secciones', 'active' => 'grados']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Gestión de Secciones</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item"><a href="/admin/grados">Grados</a></li>
          <li class="breadcrumb-item active">Secciones</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Nueva Sección</h3></div>
            <form action="/admin/secciones/store" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Grado *</label>
                  <select name="grado_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach($grados as $g): ?>
                      <option value="<?= $g['id'] ?>" <?= ($grado_sel == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Nombre de la Sección *</label>
                  <input type="text" name="nombre" class="form-control" placeholder="Ej: A, B, C o Sección 1" required maxlength="10">
                  <small class="text-muted">Ej: A → genera 1°A, B → genera 1°B</small>
                </div>
                <div class="form-group">
                  <label>Cupo Máximo</label>
                  <input type="number" name="cupo_maximo" class="form-control" value="30" min="1" max="100">
                </div>
                <div class="form-group">
                  <label>Docente Tutor</label>
                  <select name="docente_id" class="form-control">
                    <option value="">-- Sin asignar --</option>
                    <?php foreach($data['docentes'] ?? [] as $d): ?>
                      <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="card-footer"><button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Crear Sección</button></div>
            </form>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Secciones Registradas</h3>
              <div class="card-tools">
                <form class="form-inline" method="GET">
                  <select name="grado_id" class="form-control form-control-sm mr-2">
                    <option value="">Todos los grados</option>
                    <?php foreach($grados as $g): ?>
                      <option value="<?= $g['id'] ?>" <?= ($grado_sel == $g['id']) ? 'selected':'' ?>><?= htmlspecialchars($g['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-sm btn-primary">Filtrar</button>
                </form>
              </div>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Sección</th><th>Grado</th><th>Tutor</th><th>Cupo</th><th>Inscritos</th><th>Acc.</th></tr>
                </thead>
                <tbody>
                  <?php foreach($secciones as $s): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($s['grado_nombre'].' '.$s['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($s['grado_nombre']) ?></td>
                    <td><?= htmlspecialchars($s['docente_nombre'] ?? 'Sin asignar') ?></td>
                    <td><?= $s['cupo_maximo'] ?? '-' ?></td>
                    <td>
                      <?= $s['num_estudiantes'] ?>
                      <?php if($s['cupo_maximo'] && $s['num_estudiantes'] >= $s['cupo_maximo']): ?>
                        <span class="badge badge-danger ml-1">Lleno</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="/admin/secciones/edit/<?= $s['id'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                      <a href="/admin/estudiantes?seccion_id=<?= $s['id'] ?>" class="btn btn-xs btn-info" title="Ver estudiantes"><i class="fas fa-users"></i></a>
                      <a href="/admin/secciones/delete/<?= $s['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Eliminar sección?')"><i class="fas fa-trash"></i></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$secciones): ?><tr><td colspan="6" class="text-center text-muted py-3">Sin secciones registradas</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>