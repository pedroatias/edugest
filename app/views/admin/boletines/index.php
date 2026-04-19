<?php
// app/views/admin/boletines/index.php
$this->layout('layouts/admin', ['title' => 'Gestión de Boletines', 'active' => 'boletines']);
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Gestión de Boletines</h1>h1></div>div>
                            <div class="col-sm-6">
                                                  <ol class="breadcrumb float-sm-right">
                                                                            <li class="breadcrumb-item"><a href="/admin">Inicio</a>a></li>li>
                                                                            <li class="breadcrumb-item active">Boletines</li>li>
                                                  </ol>ol>
                            </div>div>
            </div>div>
        </div>div>
    </section>section>

      <section class="content">
                <div class="container-fluid">

                              <!-- Filters -->
                              <div class="card mb-3">
                                                <div class="card-header">
                                                                      <h3 class="card-title"><i class="fas fa-filter mr-2"></i>i>Filtrar por Período</h3>h3>
                                                </div>div>
                                                <div class="card-body">
                                                                      <form method="GET" class="form-inline">
                                                                                                <div class="form-group mr-3">
                                                                                                                              <label class="mr-2">Período:</label>label>
                                                                                                                              <select name="periodo_id" class="form-control form-control-sm">
                                                                                                                                                                <option value="">-- Todos --</option>option>
                                                                                                                                                                <?php foreach ($periodos as $p): ?>
                                      <option value="<?= $p['id'] ?>"
                                                                                <?= $periodoId == $p['id'] ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($p['nombre']) ?>
                                      </option>option>
                                                                                                                                                                <?php endforeach; ?>
                                                                                                                              </select>select>
                                                                                                  </div>div>
                                                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                                                                              <i class="fas fa-search mr-1"></i>i>Buscar
                                                                                                  </button>button>
                                                                                                <?php if ($periodoId): ?>
                          <button type="button" id="btnGenerarTodos" class="btn btn-warning btn-sm ml-2">
                                                        <i class="fas fa-cogs mr-1"></i>i>Generar PDFs pendientes
                          </button>button>
                                                                                                <button type="button" id="btnPublicar" class="btn btn-success btn-sm ml-2"
                                                                                                                                  data-periodo="<?= $periodoId ?>">
                                                                                                                              <i class="fas fa-globe mr-1"></i>i>Publicar todos
                                                                                                  </button>button>
                                                                                                <?php endif; ?>
                                                                      </form>form>
                                                </div>div>
                              </div>div>

                              <!-- Results table -->
                              <div class="card">
                                                <div class="card-header">
                                                                      <h3 class="card-title">
                                                                                                <i class="fas fa-file-pdf mr-2"></i>i>
                                                                                                <?= $periodoId ? 'Boletines del período seleccionado' : 'Seleccione un período para ver los boletines' ?>
                                                                      </h3>h3>
                                                                      <?php if (!empty($boletines)): ?>
                      <div class="card-tools">
                                                <span class="badge badge-info"><?= count($boletines) ?> estudiantes</span>span>
                      </div>div>
                                                                      <?php endif; ?>
                                                </div>div>
                                                <div class="card-body p-0">
                                                                      <?php if (empty($boletines) && !$periodoId): ?>
                          <div class="text-center py-5 text-muted">
                                                        <i class="fas fa-hand-point-up fa-3x mb-3"></i>i>
                                                        <p>Seleccione un período para gestionar los boletines.</p>p>
                          </div>div>
                                                                    <?php elseif (empty($boletines)): ?>
                          <div class="text-center py-4 text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3"></i>i>
                                                        <p>No hay matrículas para este período.</p>p>
                          </div>div>
                                                                    <?php else: ?>
                      <table class="table table-hover table-sm mb-0">
                                                <thead class="thead-light">
                                                                              <tr>
                                                                                                                <th>#</th>th>
                                                                                <th>Estudiante</th>th>
                                                                                <th>Código</th>th>
                                                                                <th>Sección</th>th>
                                                                                <th>Período</th>th>
                                                                                <th class="text-center">PDF Generado</th>th>
                                                                                <th class="text-center">Publicado</th>th>
                                                                                <th class="text-center">Acciones</th>th>
                                                                              </tr>tr>
                                                </thead>thead>
                                              <tbody>
                                                                            <?php foreach ($boletines as $i => $b): ?>
                              <tr id="row-<?= $b['matricula_id'] ?>">
                                                                <td><?= $i + 1 ?></td>
                                                      <td><?= htmlspecialchars($b['estudiante']) ?></td>
                                                      <td><code><?= htmlspecialchars($b['codigo']) ?></code></td>td>
                                                      <td><?= htmlspecialchars($b['seccion']) ?></td>
                                                      <td><?= htmlspecialchars($b['periodo']) ?></td>
                                                      <td class="text-center">
                                                                                            <?php if ($b['generado']): ?>
                                          <span class="badge badge-success"><i class="fas fa-check"></i>i> Sí</span>span>
                                                                                            <?php else: ?>
                                          <span class="badge badge-secondary"><i class="fas fa-times"></i>i> No</span>span>
                                                                                            <?php endif; ?>
                                                      </td>td>
                                                                <td class="text-center">
                                                                                                      <?php if ($b['disponible']): ?>
                                          <span class="badge badge-success"><i class="fas fa-eye"></i>i> Publicado</span>span>
                                                                                                      <?php else: ?>
                                          <span class="badge badge-warning">Borrador</span>span>
                                                                                                      <?php endif; ?>
                                                                </td>td>
                                                                <td class="text-center">
                                                                                                      <button class="btn btn-xs btn-info btnGenerar"
                                                                                                                                                    data-matricula="<?= $b['matricula_id'] ?>"
                                                                                                                                                    data-periodo="<?= $b['periodo_id'] ?>"
                                                                                                                                                    title="Generar PDF">
                                                                                                                                                <i class="fas fa-file-pdf"></i>i>
                                                                                                        </button>button>
                                                                </td>td>
                              </tr>tr>
                                                                            <?php endforeach; ?>
                                              </tbody>tbody>
                      </table>table>
                                                                      <?php endif; ?>
                                                </div>div>
                              </div>div>

                </div>div>
      </section>section>
</div>div>

<!-- Publish modal -->
<div class="modal fade" id="modalPublicar" tabindex="-1">
      <div class="modal-dialog">
                <div class="modal-content">
                              <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title"><i class="fas fa-globe mr-2"></i>i>Publicar Boletines</h5>h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>button>
                              </div>div>
                              <div class="modal-body">
                                                <div class="form-group">
                                                                      <label>Fecha de disponibilidad:</label>label>
                                                                      <input type="date" id="fechaDisponible" class="form-control"
                                                                                                   value="<?= date('Y-m-d') ?>">
                                                </div>div>
                                                <p class="text-muted small">
                                                                      Los representantes serán notificados que los boletines están disponibles para descarga.
                                                </p>p>
                              </div>div>
                              <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>button>
                                                <button type="button" id="btnConfirmarPublicar" class="btn btn-success">
                                                                      <i class="fas fa-globe mr-1"></i>i>Publicar y Notificar
                                                </button>button>
                              </div>div>
                </div>div>
      </div>div>
</div>div>

<script>
  // Generate single boletin
  $(document).on('click', '.btnGenerar', function() {
        const btn = $(this);
        const matriculaId = btn.data('matricula');
        const periodoId = btn.data('periodo');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.post(APP_URL + '/admin/boletines/generar', {
                  matricula_id: matriculaId,
                  periodo_id: periodoId
        }, function(r) {
                  if (r.success) {
                                btn.closest('tr').find('.badge-secondary').first()
                                   .removeClass('badge-secondary').addClass('badge-success')
                                   .html('<i class="fas fa-check"></i> Sí');
                                toastr.success('Boletín generado correctamente.');
                  } else {
                                toastr.error('Error al generar el boletín.');
                  }
        }).always(function() {
                  btn.prop('disabled', false).html('<i class="fas fa-file-pdf"></i>');
        });
  });

  // Generate all pending
  $('#btnGenerarTodos').click(function() {
        $('.btnGenerar:not([disabled])').each(function() {
                  $(this).trigger('click');
        });
  });

  // Open publish modal
  $('#btnPublicar').click(function() {
        $('#modalPublicar').modal('show');
  });

  // Confirm publish
  $('#btnConfirmarPublicar').click(function() {
        const periodoId = $('#btnPublicar').data('periodo');
        const fecha = $('#fechaDisponible').val();
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Publicando...');
        $.post(APP_URL + '/admin/boletines/publicar', {
                  periodo_id: periodoId,
                  fecha_disponible: fecha
        }, function(r) {
                  if (r.success) {
                                $('#modalPublicar').modal('hide');
                                toastr.success('Boletines publicados y representantes notificados.');
                                // Refresh page
                                setTimeout(() => location.reload(), 1500);
                  } else {
                                toastr.error('Error al publicar los boletines.');
                  }
        }).always(function() {
                  btn.prop('disabled', false).html('<i class="fas fa-globe mr-1"></i>Publicar y Notificar');
        });
  });
</script>
</script>
                                                      </td></td>
                                              </tbody></th></th>
                                                                              </tr>
                      </p>
                          </p>
                            </h1>
