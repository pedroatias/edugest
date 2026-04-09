<?php
// app/views/admin/financiero/conceptos.php
$conceptos = $data['conceptos'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Conceptos de Cobro', 'active' => 'financiero']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Conceptos de Cobro</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Conceptos</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-5">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Nuevo Concepto</h3></div>
            <form action="/admin/financiero/conceptos/store" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Nombre del Concepto *</label>
                  <input type="text" name="nombre" class="form-control" placeholder="Ej: Mensualidad Enero" required>
                </div>
                <div class="form-group">
                  <label>Monto *</label>
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                    <input type="number" name="monto" class="form-control" step="0.01" min="0" required>
                  </div>
                </div>
                <div class="form-group">
                  <label>Aplica a</label>
                  <select name="aplica_grado_id" class="form-control">
                    <option value="">Todos los grados</option>
                    <?php foreach($data['grados'] ?? [] as $g): ?>
                      <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Descripción</label>
                  <textarea name="descripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-check">
                  <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" checked>
                  <label class="form-check-label" for="activo">Activo</label>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Agregar Concepto</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Conceptos Registrados</h3></div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Nombre</th><th>Monto</th><th>Grado</th><th>Estado</th><th>Acc.</th></tr>
                </thead>
                <tbody>
                  <?php foreach($conceptos as $c): ?>
                  <tr>
                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                    <td>$ <?= number_format($c['monto'],0,',','.') ?></td>
                    <td><?= htmlspecialchars($c['grado_nombre'] ?? 'Todos') ?></td>
                    <td><span class="badge badge-<?= $c['activo'] ? 'success':'secondary' ?>"><?= $c['activo'] ? 'Activo':'Inactivo' ?></span></td>
                    <td>
                      <a href="/admin/financiero/conceptos/edit/<?= $c['id'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                      <a href="/admin/financiero/conceptos/delete/<?= $c['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Eliminar?')"><i class="fas fa-trash"></i></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$conceptos): ?><tr><td colspan="5" class="text-center text-muted py-3">Sin conceptos registrados</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>