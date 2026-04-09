<?php ?>
<h4 class="fw-bold mb-4"><i class="fas fa-edit me-2 text-primary"></i>Ingreso de Notas</h4>
<div class="card mb-3"><div class="card-body">
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label fw-bold">Seccion / Asignatura</label>
            <select id="selAsignacion" class="form-select">
                <option value="">-- Seleccione --</option>
                <?php foreach($asignaciones as $a): ?>
                <option value="<?= $a['id'] ?>" data-seccion="<?= $a['seccion_id'] ?>" data-asignatura="<?= $a['asignatura_id'] ?>"><?= e($a['seccion'].' - '.$a['asignatura']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4"><label class="form-label fw-bold">Periodo</label>
            <select id="selPeriodo" class="form-select">
                <?php foreach($periodos as $p): ?>
                <option value="<?= $p['id'] ?>" data-habilitado="<?= $p['notas_habilitadas'] ?>"><?= e($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button id="btnCargar" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Cargar Estudiantes</button>
        </div>
    </div>
</div></div>
<div id="notasContainer"></div>
<script>
$('#btnCargar').click(function(){
    const asig=$('#selAsignacion option:selected');
    const seccionId=asig.data('seccion');
    const asignaturaId=asig.data('asignatura');
    const periodoId=$('#selPeriodo').val();
    const habilitado=$('#selPeriodo option:selected').data('habilitado');
    if(!seccionId){toastr.warning('Seleccione una asignatura.');return;}
    if(!habilitado){toastr.error('El acceso a notas esta cerrado para este periodo.');return;}
    $('#notasContainer').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
    $.get(APP_URL+'/docente/notas/seccion?seccion_id='+seccionId+'&asignatura_id='+asignaturaId+'&periodo_id='+periodoId,function(r){
        if(r.error){toastr.error(r.error);$('#notasContainer').html('');return;}
        let html='<div class="card"><div class="card-header d-flex justify-content-between"><h6 class="mb-0">Notas del Periodo</h6><button class="btn btn-success btn-sm" id="btnGuardarTodo"><i class="fas fa-save me-1"></i>Guardar Todo</button></div><div class="card-body table-responsive"><table class="table table-sm notes-table"><thead><tr><th>#</th><th>Estudiante</th>';
        r.estructura.forEach(e=>{html+='<th class="text-center">'+e.nombre+'<br><small class="text-muted">'+e.porcentaje+'%</small></th>';});
        html+='<th class="text-center">Definitiva</th></tr></thead><tbody>';
        r.estudiantes.forEach((est,i)=>{
            const notasEst=r.notas[est.matricula_id]||{};
            html+='<tr data-matricula="'+est.matricula_id+'"><td>'+(i+1)+'</td><td>'+est.nombre_completo+'</td>';
            r.estructura.forEach(comp=>{
                const n=notasEst[comp.id]||0;
                html+='<td><input type="number" class="nota-input" min="0" max="5" step="0.1" value="'+n+'" data-matricula="'+est.matricula_id+'" data-asignatura="'+asignaturaId+'" data-periodo="'+periodoId+'" data-componente="'+comp.id+'" data-porcentaje="'+comp.porcentaje+'"></td>';
            });
            html+='<td class="text-center nota-calculada" id="def_'+est.matricula_id+'">'+calcDef(notasEst,r.estructura)+'</td></tr>';
        });
        html+='</tbody></table></div></div>';
        $('#notasContainer').html(html);
    });
});

function calcDef(notas,estructura){
    let total=0;
    estructura.forEach(c=>{total+=(parseFloat(notas[c.id]||0)*parseFloat(c.porcentaje)/100);});
    return total.toFixed(2);
}

$(document).on('input','.nota-input',function(){
    const row=$(this).closest('tr');
    const mId=$(this).data('matricula');
    const notas={};
    row.find('.nota-input').each((_,el)=>{notas[$(el).data('componente')]=$(el).val();});
    const estructura=[];
    row.find('.nota-input').each((_,el)=>{estructura.push({id:$(el).data('componente'),porcentaje:$(el).data('porcentaje')});});
    $('#def_'+mId).text(calcDef(notas,estructura));
    clearTimeout(window._saveTimer);
    window._saveTimer=setTimeout(()=>{ ajaxPost('docente/notas/guardar',{matricula_id:mId,asignatura_id:$(this).data('asignatura'),periodo_id:$(this).data('periodo'),estructura_id:$(this).data('componente'),nota:$(this).val()},()=>{},{});},800);
});

$(document).on('click','#btnGuardarTodo',function(){
    const notas=[];
    $('.nota-input').each((_,el)=>{notas.push({matricula_id:$(el).data('matricula'),asignatura_id:$(el).data('asignatura'),periodo_id:$(el).data('periodo'),estructura_id:$(el).data('componente'),nota:$(el).val()});});
    $.ajax({type:'POST',url:APP_URL+'/docente/notas/guardar-masivo',data:JSON.stringify({notas}),contentType:'application/json',dataType:'json',success:function(r){toastr.success('Notas guardadas: '+r.saved);}});
});
</script>