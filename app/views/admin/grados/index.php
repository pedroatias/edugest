<?php
// app/views/admin/grados/index.php
$grados = $data['grados'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Grados y Secciones', 'active' => 'grados']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Grados y Secciones</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Grados</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Nuevo Grado</h3></div>
            <form action="/admin/grados/store" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre del Grado *</label>
                  <input type="text" name="nombre" class="form-control" placeholder="Ej: 1° Primaria" required>
                </div>
                <div class="form-group">
                  <label>Nivel</label>
                  <select name="nivel" class="form-control">
                    <option value="inicial">Inicial</option>
                    <option value="primaria">Primaria</option>
                    <option value="secundaria">Secundaria</option>
                    <option value="bachillerato">Bachillerato</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Orden (para ordenar en listas)</label>
                  <input type="number" name="orden" class="form-control" value="1" min="1">
                </div>
              </div>
              <div class="card-footer"><button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Agregar Grado</button></div>
            </form>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Grados Registrados</h3></div>
            <div class="card-body p-0">
              <table class="table table-hover">
                <thead class="thead-light">
                  <tr><th>Nombre</th><th>Nivel</th><th>Secciones</th><th>Estudiantes</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                  <?php foreach($grados as $g): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($g['nombre']) ?></strong></td>
                    <td><?= ucfirst($g['nivel'] ?? '-') ?></td>
                    <td>
                      <?php foreach($g['secciones'] ?? [] as $s): ?>
                        <span class="badge badge-info mr-1"><?= htmlspecialchars($s['nombre']) ?> <small>(<?= $s['num_estudiantes'] ?>)</small></span>
                      <?php endforeach; ?>
                      <a href="/admin/secciones/nueva?grado_id=<?= $g['id'] ?>" class="btn btn-xs btn-outline-secondary ml-1" title="Agregar sección"><i class="fas fa-plus"></i></a>
                    </td>
                    <td><?= $g['total_estudiantes'] ?? 0 ?></td>
                    <td>
                      <a href="/admin/grados/edit/<?= $g['id'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                      <a href="/admin/grados/delete/<?= $g['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Eliminar grado? Solo si no tiene estudiantes.')"><i class="fas fa-trash"></i></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$grados): ?><tr><td colspan="5" class="text-center text-muted py-3">Sin grados registrados</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>