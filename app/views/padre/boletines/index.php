<?php
// app/views/padre/boletines/index.php
// View: Parent - Boletines (Report Cards)
?>
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold"><i class="fas fa-file-pdf me-2 text-info"></i>i>Boletines</h4>h4>
    </div>div>
</div>div>

<?php if (count($matriculas) > 1): ?>
  <div class="card mb-4">
      <div class="card-body py-2">
          <div class="d-flex align-items-center gap-3 flex-wrap">
              <span class="fw-semibold">Estudiante:</span>span>
              <?php foreach ($matriculas as $m): ?>
                  <a href="<?= url('padre/boletines?cod_est=' . $m['matricula_id']) ?>"
                     class="btn btn-sm <?= $m['matricula_id'] == $selected['matricula_id'] ? 'btn-info text-white' : 'btn-outline-info' ?>">
                      <?= htmlspecialchars($m['nombre_completo']) ?>
                  </a>a>
              <?php endforeach; ?>
          </div>div>
      </div>div>
  </div>div>
  <?php endif; ?>

<!-- Student card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-4">
            <?php if (!empty($selected['foto'])): ?>
                  <img src="<?= url('uploads/fotos/' . $selected['foto']) ?>"
                       class="rounded-circle" width="80" height="80"
                       style="object-fit:cover;" alt="Foto">
              <?php else: ?>
                  <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                       style="width:80px;height:80px;">
                      <i class="fas fa-user fa-2x text-white"></i>i>
                  </div>div>
              <?php endif; ?>
            <div>
                              <h5 class="mb-1 fw-bold"><?= htmlspecialchars($selected['nombre_completo']) ?></h5>
                              <span class="text-muted"><i class="fas fa-users me-1"></i>i><?= htmlspecialchars($selected['seccion']) ?></span>span>
            </div>div>
        </div>div>
    </div>div>
</div>div>

<!-- Timeline of boletines -->
<div class="row">
      <div class="col-12 col-md-8 offset-md-2">
                <?php if (empty($boletines)): ?>
              <div class="card">
                                <div class="card-body text-center py-5">
                                                      <i class="fas fa-file-pdf fa-4x text-muted mb-3"></i>i>
                                                      <h5 class="text-muted">No hay boletines disponibles</h5>h5>
                                                      <p class="text-muted">Los boletines aparecerán aquí cuando el administrador los publique.</p>p>
                                </div>div>
              </div>div>
                <?php else: ?>
              <div class="timeline-container position-relative" style="padding-left: 40px;">
                                <!-- vertical line -->
                                <div class="position-absolute" style="left:14px;top:0;bottom:0;width:2px;background:#dee2e6;"></div>div>

                                <?php foreach ($boletines as $boletin): ?>
                  <div class="mb-4 position-relative">
                                        <!-- dot -->
                                        <div class="position-absolute d-flex align-items-center justify-content-center rounded-circle bg-success text-white"
                                                                   style="left:-26px;top:10px;width:28px;height:28px;">
                                                                  <i class="fas fa-file-pdf" style="font-size:12px;"></i>i>
                                        </div>div>

                                        <!-- date badge -->
                                        <?php if (!empty($boletin['fecha_disponible'])): ?>
                      <div class="mb-2">
                                                <span class="badge bg-secondary">
                                                                              <?= date('d M Y', strtotime($boletin['fecha_disponible'])) ?>
                                                </span>span>
                      </div>div>
                                        <?php endif; ?>

                                        <!-- card -->
                                        <div class="card shadow-sm">
                                                                  <div class="card-body">
                                                                                                <h6 class="card-title text-success fw-bold mb-1">
                                                                                                                                  Boletín <?= htmlspecialchars($boletin['periodo']) ?>
                                                                                                </h6>h6>
                                                                                                <p class="card-text text-muted mb-3">
                                                                                                                                  El boletín del <?= htmlspecialchars($boletin['periodo']) ?> ya está disponible para su descarga en formato PDF.
                                                                                                  </p>p>
                                                                                                <a href="<?= url('padre/boletines/descargar?id=' . $boletin['id']) ?>"
                                                                                                                                 class="btn btn-primary btn-sm">
                                                                                                                                  <i class="fas fa-download me-1"></i>i>Descargar
                                                                                                  </a>a>
                                                                  </div>div>
                                        </div>div>
                  </div>div>
                                <?php endforeach; ?>

                                <!-- Pending periods -->
                                <?php
                  $boletinPeriodos = array_column($boletines, 'periodo_id');
                  $pendientes = array_filter($periodos, fn($p) => !in_array($p['id'], $boletinPeriodos));
                  foreach ($pendientes as $p):
                                    ?>
                                    <div class="mb-4 position-relative">
                                                          <div class="position-absolute d-flex align-items-center justify-content-center rounded-circle bg-light border"
                                                                                     style="left:-26px;top:10px;width:28px;height:28px;">
                                                                                    <i class="fas fa-clock text-secondary" style="font-size:12px;"></i>i>
                                                          </div>div>
                                                          <div class="card border-dashed" style="border-style:dashed !important;opacity:0.6;">
                                                                                    <div class="card-body py-2">
                                                                                                                  <small class="text-muted">
                                                                                                                                                    <i class="fas fa-hourglass-half me-1"></i>i>
                                                                                                                                                    Boletín <?= htmlspecialchars($p['nombre']) ?> — pendiente de publicación
                                                                                                                    </small>small>
                                                                                      </div>div>
                                                          </div>div>
                                    </div>div>
                                <?php endforeach; ?>
              </div>div>
                <?php endif; ?>
      </div>div>
</div>div>
            </div>
