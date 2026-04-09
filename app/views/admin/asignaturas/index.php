<?php
// app/views/admin/asignaturas/index.php
$asignaturas = $data['asignaturas'] ?? [];
$grados = $data['grados'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Asignaturas', 'active' => 'asignaturas']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Gestión de Asignaturas</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Asignaturas</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Nueva Asignatura</h3></div>
            <form action="/admin/asignaturas/store" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre *</label>
                  <input type="text" name="nombre" class="form-control" placeholder="Ej: Matemáticas" required>
                </div>
                <div class="form-group">
                  <label>Código</label>
                  <input type="text" name="codigo" class="form-control" placeholder="Ej: MAT-01" maxlength="20">
                </div>
                <div class="form-group">
                  <label>Aplica a Grado</label>
                  <select name="grado_id" class="form-control">
                    <option value="">Todos los grados</option>
                    <?php foreach($grados as $g): ?>
                      <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Descripción</label>
                  <textarea name="descripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                  <label>Color (en reportes)</label>
                  <input type="color" name="color" class="form-control" value="#007bff">
                </div>
              </div>
              <div class="card-footer"><button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Agregar</button></div>
            </form>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Asignaturas Registradas</h3></div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Nombre</th><th>Código</th><th>Grado</th><th>Docentes asignados</th><th>Acc.</th></tr>
                </thead>
                <tbody>
                  <?php foreach($asignaturas as $a): ?>
                  <tr>
                    <td>
                      <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:<?= htmlspecialchars($a['color'] ?? '#007bff') ?>;margin-right:6px;"></span>
                      <strong><?= htmlspecialchars($a['nombre']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($a['codigo'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($a['grado_nombre'] ?? 'Todos') ?></td>
                    <td><?= $a['num_docentes'] ?? 0 ?></td>
                    <td>
                      <a href="/admin/asignaturas/edit/<?= $a['id'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                      <a href="/admin/asignaturas/delete/<?= $a['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Eliminar asignatura?')"><i class="fas fa-trash"></i></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$asignaturas): ?><tr><td colspan="5" class="text-center text-muted py-3">Sin asignaturas</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>