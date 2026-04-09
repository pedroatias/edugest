<?php
// app/views/admin/notificaciones/index.php
$notifs = $data['notificaciones'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Notificaciones Push', 'active' => 'notificaciones']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Notificaciones Push</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Notificaciones</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-5">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Enviar Notificación</h3></div>
            <form action="/admin/notificaciones/enviar" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Título *</label>
                  <input type="text" name="titulo" class="form-control" placeholder="Título de la notificación" required maxlength="100">
                </div>
                <div class="form-group">
                  <label>Mensaje *</label>
                  <textarea name="mensaje" class="form-control" rows="3" required maxlength="250" placeholder="Cuerpo de la notificación..."></textarea>
                </div>
                <div class="form-group">
                  <label>Destinatarios</label>
                  <select name="destinatario" class="form-control">
                    <option value="todos">Todos los padres/representantes</option>
                    <option value="grado">Por grado</option>
                    <option value="admin">Solo administradores</option>
                  </select>
                </div>
                <div class="form-group" id="div_grado" style="display:none">
                  <label>Grado</label>
                  <select name="grado_id" class="form-control">
                    <?php foreach($data['grados'] ?? [] as $g): ?>
                      <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>URL de acción (opcional)</label>
                  <input type="url" name="url" class="form-control" placeholder="https://...">
                </div>
                <div class="form-group">
                  <label>Tipo</label>
                  <select name="tipo" class="form-control">
                    <?php foreach(['general','pago','academico','circular','boletin'] as $t): ?>
                      <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Enviar Notificación</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Historial de Notificaciones</h3>
              <div class="card-tools">
                <span class="badge badge-info"><?= count($notifs) ?> enviadas</span>
              </div>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Título</th><th>Tipo</th><th>Destinatarios</th><th>Enviados</th><th>Fecha</th></tr>
                </thead>
                <tbody>
                  <?php foreach($notifs as $n): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($n['titulo']) ?></strong><br>
                      <small class="text-muted"><?= htmlspecialchars(substr($n['mensaje'],0,60)).'...' ?></small>
                    </td>
                    <td><span class="badge badge-secondary"><?= htmlspecialchars($n['tipo']) ?></span></td>
                    <td><?= htmlspecialchars($n['destinatario'] ?? 'todos') ?></td>
                    <td><?= $n['enviados'] ?? '?' ?></td>
                    <td><small><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$notifs): ?><tr><td colspan="5" class="text-center text-muted py-3">Sin notificaciones enviadas</td></tr><?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script>
document.querySelector('[name=destinatario]').addEventListener('change', function(){
  document.getElementById('div_grado').style.display = this.value === 'grado' ? 'block' : 'none';
});
</script>