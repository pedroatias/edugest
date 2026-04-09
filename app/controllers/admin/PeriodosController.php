<?php
class PeriodosController extends Controller {
    public function index(): void {
        $this->requireAuth('admin');
        $anioId=Session::anioLectivoId();
        $periodos=Database::fetchAll("SELECT * FROM periodos_academicos WHERE anio_lectivo_id=? ORDER BY numero",[$anioId]);
        $this->view('admin/periodos/index',['periodos'=>$periodos,'pageTitle'=>'Periodos Academicos']);
    }
    public function crear(): void {
        $this->requireAuth('admin');
        $anioId=Session::anioLectivoId();
        Database::insert("INSERT INTO periodos_academicos (anio_lectivo_id,nombre,numero,fecha_inicio,fecha_fin,porcentaje) VALUES (?,?,?,?,?,?)",[$anioId,$this->sanitize($this->input('nombre','')), $this->inputInt('numero'),$this->input('fecha_inicio',''),$this->input('fecha_fin',''),$this->inputFloat('porcentaje',25)]);
        Session::flash('success','Periodo creado.'); $this->redirect('admin/periodos');
    }
    public function actualizar(): void {
        $this->requireAuth('admin');
        $id=$this->inputInt('id');
        Database::execute("UPDATE periodos_academicos SET nombre=?,fecha_inicio=?,fecha_fin=?,porcentaje=? WHERE id=?",[$this->sanitize($this->input('nombre','')), $this->input('fecha_inicio',''),$this->input('fecha_fin',''),$this->inputFloat('porcentaje',25),$id]);
        Session::flash('success','Periodo actualizado.'); $this->redirect('admin/periodos');
    }
    public function toggleNotas(): void {
        $this->requireAuth('admin');
        $id=$this->inputInt('id');
        $periodo=Database::fetchOne("SELECT notas_habilitadas FROM periodos_academicos WHERE id=?",[$id]);
        if(!$periodo){$this->json(['error'=>'No encontrado'],404);return;}
        $nuevo=$periodo['notas_habilitadas']?0:1;
        Database::execute("UPDATE periodos_academicos SET notas_habilitadas=? WHERE id=?",[$nuevo,$id]);
        $this->json(['success'=>true,'notas_habilitadas'=>$nuevo,'mensaje'=>$nuevo?'Acceso a notas habilitado':'Acceso a notas cerrado']);
    }
    public function estructuraNotas(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        $estructura=Database::fetchAll("SELECT * FROM estructura_notas WHERE institucion_id=? ORDER BY orden",[$instId]);
        $this->view('admin/periodos/estructura_notas',['estructura'=>$estructura,'pageTitle'=>'Estructura de Notas']);
    }
    public function guardarEstructura(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        $nombres=$_POST['nombre']??[]; $porcentajes=$_POST['porcentaje']??[]; $ids=$_POST['id']??[];
        Database::execute("UPDATE estructura_notas SET activo=0 WHERE institucion_id=?",[$instId]);
        $total=array_sum($porcentajes);
        if(abs($total-100)>0.01){$this->json(['error'=>'Los porcentajes deben sumar 100%. Suma actual: '.$total.'%'],400);return;}
        foreach($nombres as $i=>$nombre){
            if(!trim($nombre))continue;
            $eid=(int)($ids[$i]??0);
            if($eid){ Database::execute("UPDATE estructura_notas SET nombre=?,porcentaje=?,orden=?,activo=1 WHERE id=?",[$nombre,$porcentajes[$i]??0,$i+1,$eid]); }
            else { Database::insert("INSERT INTO estructura_notas (institucion_id,nombre,porcentaje,orden) VALUES (?,?,?,?)",[$instId,$nombre,$porcentajes[$i]??0,$i+1]); }
        }
        Session::flash('success','Estructura de notas actualizada.'); $this->redirect('admin/estructura-notas');
    }
}