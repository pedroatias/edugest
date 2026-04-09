<?php
// app/views/admin/boletines/index.php
$boletines = $data['boletines'] ?? [];
$periodos = $data['periodos'] ?? [];
$grados = $data['grados'] ?? [];
?>
<?php $this->layout('layouts/admin', ['title' => 'Boletines', 'active' => 'boletines']) ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Boletines de Calificaciones</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin">Inicio</a></li>
          <li class="breadcrumb-item active">Boletines</li>
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
                <select name="periodo_id" class="form-control form-control-sm mr-2">
                  <option value="">Todos los períodos</option>
                  <?php foreach($periodos as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($data['fp'] ?? '') == $p['id'] ? 'selected':'' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <select name="grado_id" class="form-control form-control-sm mr-2">
                  <option value="">Todos los grados</option>
                  <?php foreach($grados as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= ($data['fg'] ?? '') == $g['id'] ? 'selected':'' ?>><?= htmlspecialchars($g['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-primary">Filtrar</button>
              </form>
            </div>
            <div class="col-md-4 text-right">
              <a href="/admin/boletines/generar" class="btn btn-warning btn-sm"><i class="fas fa-cogs"></i> Generar Boletines</a>
              <a href="/admin/boletines/publicar" class="btn btn-success btn-sm"><i class="fas fa-globe"></i> Publicar Todo</a>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-sm">
            <thead class="thead-light">
              <tr><th>Estudiante</th><th>Grado</th><th>Período</th><th>Promedio</th><th>Estado</th><th>Publicado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
              <?php foreach($boletines as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['est_nombres'].' '.$b['est_apellidos']) ?></td>
                <td><?= htmlspecialchars($b['grado'].' '.$b['seccion']) ?></td>
                <td><?= htmlspecialchars($b['periodo_nombre']) ?></td>
                <td><strong><?= number_format($b['promedio'],1) ?></strong></td>
                <td><span class="badge badge-<?= $b['promedio'] >= 6 ? 'success':'danger' ?>"><?= $b['promedio'] >= 6 ? 'Aprobado':'Reprobado' ?></span></td>
                <td>
                  <?php if($b['publicado']): ?>
                    <span class="badge badge-success"><i class="fas fa-check"></i> Publicado</span>
                  <?php else: ?>
                    <span class="badge badge-secondary">Borrador</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="/admin/boletines/ver/<?= $b['id'] ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                  <a href="/admin/boletines/pdf/<?= $b['id'] ?>" class="btn btn-xs btn-danger" title="Descargar PDF"><i class="fas fa-file-pdf"></i></a>
                  <?php if(!$b['publicado']): ?>
                    <a href="/admin/boletines/publicar/<?= $b['id'] ?>" class="btn btn-xs btn-success" title="Publicar"><i class="fas fa-globe"></i></a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(!$boletines): ?><tr><td colspan="7" class="text-center text-muted py-3">Sin boletines generados</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>