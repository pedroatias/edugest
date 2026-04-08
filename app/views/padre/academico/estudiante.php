<?php
$daysMap = [1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes'];
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('academico') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">Academico - Estudiante</h4>
</div>

<div class="row g-4">
    <!-- Student card -->
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <?php if ($estudiante['foto']): ?>
                <img src="<?= uploads($estudiante['foto']) ?>" class="rounded-circle mx-auto d-block mb-2" width="80" height="80" style="object-fit:cover">
            <?php else: ?>
                <div class="rounded-circle bg-secondary mx-auto d-block mb-2 d-flex align-items-center justify-content-center" style="width:80px;height:80px">
                    <i class="fas fa-user fa-2x text-white"></i>
                </div>
            <?php endif; ?>
            <h6 class="fw-bold"><?= e($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?></h6>
            <div class="text-muted small">
                <div><strong>Codigo:</strong> <?= e($estudiante['codigo']) ?></div>
                <div><strong>Grupo:</strong> <?= e($estudiante['seccion']) ?></div>
            </div>
        </div>
    </div>

    <!-- Tabs content -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="acadTabs">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-calendario">Calendario</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-notas">Notas</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-acumulado">Acumulado</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-horario">Horario</a></li>
                </ul>
            </div>
            <div class="card-body tab-content">
                <!-- Calendario -->
                <div class="tab-pane fade show active" id="tab-calendario">
                    <div id="calendar-container" style="min-height:400px"></div>
                </div>
                <!-- Notas -->
                <div class="tab-pane fade" id="tab-notas">
                    <div class="mb-3">
                        <label class="form-label">Periodo:</label>
                        <select class="form-select form-select-sm w-auto" id="selectPeriodo">
                            <?php foreach ($periodos as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= e($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="notas-content"><p class="text-muted">Seleccione un periodo para ver las notas.</p></div>
                </div>
                <!-- Acumulado -->
                <div class="tab-pane fade" id="tab-acumulado">
                    <div id="acumulado-content"><p class="text-muted">Cargando...</p></div>
                </div>
                <!-- Horario -->
                <div class="tab-pane fade" id="tab-horario">
                    <div id="horario-content"><p class="text-muted">Cargando...</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/es.global.min.js"></script>

<script>
const COD_EST = '<?= $estudiante['matricula_id'] ?>';

// Calendar
document.addEventListener('DOMContentLoaded', () => {
    const cal = new FullCalendar.Calendar(document.getElementById('calendar-container'), {
        locale: 'es',
        initialView: 'dayGridMonth',
        height: 400,
        events: APP_URL + '/academico/calendario?cod_est=' + COD_EST,
        eventClick: function(info) {
            const props = info.event.extendedProps;
            alert(info.event.title + '\n' + (props.descripcion||'') + '\n' + (props.asignatura?'Asignatura: '+props.asignatura:''));
        }
    });
    cal.render();
});

// Load notas
$('#tab-notas').on('click', function() { loadNotas(); });
$('#selectPeriodo').on('change', loadNotas);

function loadNotas() {
    const periodoId = $('#selectPeriodo').val();
    $.get(APP_URL + '/academico/notas?cod_est=' + COD_EST + '&periodo_id=' + periodoId, function(r) {
        if (r.acceso_cerrado) {
            $('#notas-content').html('<div class="alert alert-danger"><i class="fas fa-ban me-2"></i>Acceso a notas cerrado temporalmente.</div>');
            return;
        }
        if (!r.notas.length) { $('#notas-content').html('<p class="text-muted">Sin notas registradas.</p>'); return; }
        let html = '<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Asignatura</th>';
        r.notas[0].componentes.forEach(c => html += '<th class="text-center">' + c.nombre + '<br><small class="text-muted">' + c.porcentaje + '%</small></th>');
        html += '<th class="text-center">Definitiva</th></tr></thead><tbody>';
        r.notas.forEach(n => {
            const desemp = getDesempeno(n.nota_periodo, r.escala);
            html += '<tr><td>' + n.asignatura + '</td>';
            n.componentes.forEach(c => html += '<td class="text-center">' + c.nota.toFixed(1) + '</td>');
            html += '<td class="text-center"><span class="badge desempeno-' + desemp.nombre.toLowerCase() + '">' + n.nota_periodo.toFixed(2) + '</span></td></tr>';
        });
        html += '</tbody></table></div>';
        // Escala legend
        html += '<div class="d-flex gap-2 flex-wrap mt-2">';
        r.escala.forEach(e => html += '<span class="badge" style="background:' + e.color + '">' + e.nombre + ': ' + e.nota_minima + '-' + e.nota_maxima + '</span>');
        html += '</div>';
        $('#notas-content').html(html);
    });
}

function getDesempeno(nota, escala) {
    return escala.find(e => nota >= parseFloat(e.nota_minima) && nota <= parseFloat(e.nota_maxima)) || {nombre:'bajo'};
}

// Load acumulado on tab click
$('a[href="#tab-acumulado"]').on('shown.bs.tab', function() {
    $.get(APP_URL + '/academico/acumulado?cod_est=' + COD_EST, function(r) {
        if (!r.asignaturas.length) { $('#acumulado-content').html('<p class="text-muted">Sin datos de acumulado.</p>'); return; }
        let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Asignatura</th>';
        r.periodos.forEach(p => html += '<th class="text-center">' + p.nombre + '</th>');
        html += '<th class="text-center">Promedio</th></tr></thead><tbody>';
        r.asignaturas.forEach(a => {
            const desemp = getDesempeno(a.promedio, r.escala);
            html += '<tr><td>' + a.nombre + '</td>';
            r.periodos.forEach(p => { const n = a.periodos[p.id]; html += '<td class="text-center">' + (n !== undefined ? n.toFixed(2) : '-') + '</td>'; });
            html += '<td class="text-center"><span class="badge desempeno-' + desemp.nombre.toLowerCase() + '">' + a.promedio.toFixed(2) + '</span></td></tr>';
        });
        html += '</tbody></table></div>';
        // Escala
        html += '<div class="d-flex gap-2 flex-wrap mt-2">';
        r.escala.forEach(e => html += '<span class="badge" style="background:' + e.color + '">' + e.nombre + '</span>');
        html += '</div>';
        $('#acumulado-content').html(html);
    });
});

// Load horario on tab click
$('a[href="#tab-horario"]').on('shown.bs.tab', function() {
    $.get(APP_URL + '/academico/horario?cod_est=' + COD_EST, function(r) {
        const days = {1:'Lunes',2:'Martes',3:'Miercoles',4:'Jueves',5:'Viernes'};
        const bloques = Object.keys(r.horario).sort();
        if (!bloques.length) { $('#horario-content').html('<p class="text-muted">Sin horario asignado.</p>'); return; }
        let html = '<div class="table-responsive"><table class="table table-bordered table-sm schedule-table"><thead><tr><th>N</th>';
        [1,2,3,4,5].forEach(d => html += '<th>' + days[d] + '</th>');
        html += '</tr></thead><tbody>';
        bloques.forEach(b => {
            html += '<tr><td class="text-center fw-bold">' + b + '</td>';
            [1,2,3,4,5].forEach(d => {
                const cell = r.horario[b]?.[d];
                if (cell) {
                    html += '<td><span class="schedule-badge" style="background:' + (cell.color||'#3498db') + '">' + cell.asignatura + '</span></td>';
                } else { html += '<td></td>'; }
            });
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        $('#horario-content').html(html);
    });
});
</script>