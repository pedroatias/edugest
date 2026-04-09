<?php
// app/views/admin/pagos/nuevo.php
$estudiantes = $data['estudiantes'] ?? [];
$conceptos = $data['conceptos'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Registrar Pago Manual', 'active' => 'pagos']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Registrar Pago Manual</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/admin/pagos">Pagos</a></li>
            <li class="breadcrumb-item active">Nuevo</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary col-md-8">
        <div class="card-header"><h3 class="card-title">Datos del Pago</h3></div>
        <form action="/admin/pagos/store" method="POST" enctype="multipart/form-data">
          <div class="card-body">
            <div class="form-group">
              <label>Estudiante *</label>
              <select name="estudiante_id" class="form-control select2" required>
                <option value="">-- Buscar estudiante --</option>
                <?php foreach($estudiantes as $e): ?>
                  <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombres'].' '.$e['apellidos'].' - '.$e['grado'].' '.$e['seccion']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Concepto de Pago *</label>
              <select name="concepto_id" class="form-control" required>
                <option value="">-- Seleccione concepto --</option>
                <?php foreach($conceptos as $c): ?>
                  <option value="<?= $c['id'] ?>" data-monto="<?= $c['monto'] ?>"><?= htmlspecialchars($c['nombre']) ?> - $ <?= number_format($c['monto'],0,',','.') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Monto *</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                <input type="number" name="monto" id="monto" class="form-control" step="0.01" required>
              </div>
              <small class="text-muted">El monto se completa automáticamente al seleccionar concepto</small>
            </div>
            <div class="form-group">
              <label>Fecha de Pago *</label>
              <input type="date" name="fecha_pago" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
              <label>Medio de Pago</label>
              <select name="medio_pago" class="form-control">
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia Bancaria</option>
                <option value="cheque">Cheque</option>
                <option value="wompi">Wompi</option>
                <option value="otro">Otro</option>
              </select>
            </div>
            <div class="form-group">
              <label>Referencia / N. Comprobante</label>
              <input type="text" name="referencia" class="form-control" placeholder="Opcional">
            </div>
            <div class="form-group">
              <label>Comprobante (imagen/PDF)</label>
              <input type="file" name="comprobante" class="form-control-file" accept="image/*,.pdf">
            </div>
            <div class="form-group">
              <label>Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="3"></textarea>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Pago</button>
            <a href="/admin/pagos" class="btn btn-secondary ml-2">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<script>
document.querySelector('[name=concepto_id]').addEventListener('change', function(){
  const opt = this.options[this.selectedIndex];
  const m = opt.dataset.monto;
  if(m) document.getElementById('monto').value = m;
});
</script>