<?php
// app/views/admin/circulares/index.php
$circulares = $data['circulares'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Circulares', 'active' => 'circulares']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Circulares Institucionales</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Circulares</li>
        </ol></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-5">
          <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Nueva Circular</h3></div>
            <form action="/admin/circulares/store" method="POST" enctype="multipart/form-data">
              <div class="card-body">
                <div class="form-group">
                  <label>Título *</label>
                  <input type="text" name="titulo" class="form-control" required placeholder="Título de la circular">
                </div>
                <div class="form-group">
                  <label>Contenido *</label>
                  <textarea name="contenido" class="form-control" rows="6" required placeholder="Contenido de la circular..."></textarea>
                </div>
                <div class="form-group">
                  <label>Destinatarios</label>
                  <select name="destinatario" class="form-control">
                    <option value="todos">Todos los representantes</option>
                    <option value="grado">Por grado específico</option>
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
                  <label>Adjunto (PDF/imagen, opcional)</label>
                  <input type="file" name="adjunto" class="form-control-file" accept=".pdf,image/*">
                </div>
                <div class="form-check">
                  <input type="checkbox" name="enviar_push" class="form-check-input" id="push" value="1" checked>
                  <label class="form-check-label" for="push">Enviar notificación push al publicar</label>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Publicar Circular</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Circulares Publicadas</h3></div>
            <div class="card-body p-0">
              <table class="table table-hover table-sm">
                <thead class="thead-light">
                  <tr><th>Título</th><th>Destinatario</th><th>Publicada</th><th>Acc.</th></tr>
                </thead>
                <tbody>
                  <?php foreach($circulares as $c): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($c['titulo']) ?></strong>
                      <?php if($c['adjunto']): ?><i class="fas fa-paperclip text-muted ml-1" title="Tiene adjunto"></i><?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($c['destinatario'] ?? 'todos') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                    <td>
                      <a href="/admin/circulares/ver/<?= $c['id'] ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                      <a href="/admin/circulares/delete/<?= $c['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Eliminar circular?')"><i class="fas fa-trash"></i></a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(!$circulares): ?><tr><td colspan="4" class="text-center text-muted py-3">Sin circulares publicadas</td></tr><?php endif; ?>
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