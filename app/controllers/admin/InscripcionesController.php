<?php
class InscripcionesController extends Controller {
    public function index(): void {
        $this->requireAuth('admin');
        $iid=Session::institucionId();$est=$this->input('estado','');
        $sql="SELECT i.*,g.nombre AS grado FROM inscripciones i LEFT JOIN grados g ON g.id=i.grado_solicitado_id WHERE i.institucion_id=?";
        $p=[$iid];if($est){$sql.=" AND i.estado=?";$p[]=$est;}$sql.=" ORDER BY i.created_at DESC";
        $this->view('admin/inscripciones/index',['inscripciones'=>Database::fetchAll($sql,$p),'estado'=>$est,'pageTitle'=>'Inscripciones']);
    }
    public function ver(): void {
        $this->requireAuth('admin');$id=$this->inputInt('id');
        $i=Database::fetchOne("SELECT i.*,g.nombre AS grado FROM inscripciones i LEFT JOIN grados g ON g.id=i.grado_solicitado_id WHERE i.id=?",[$id]);
        if(!$i){$this->redirect('admin/inscripciones');return;}
        $this->view('admin/inscripciones/ver',['inscripcion'=>$i,'documentos'=>Database::fetchAll("SELECT * FROM documentos_inscripcion WHERE inscripcion_id=?",[$id]),'pageTitle'=>'Solicitud '.$i['codigo_solicitud']]);
    }
    public function cambiarEstado(): void {
        $this->requireAuth('admin');$id=$this->inputInt('id');
        Database::execute("UPDATE inscripciones SET estado=?,observaciones=?,revisado_por=?,updated_at=NOW() WHERE id=?",[$this->input('estado',''),$this->sanitize($this->input('observaciones','')),Session::userId(),$id]);
        Session::flash('success','Estado actualizado.');$this->redirect('admin/inscripciones/ver?id='.$id);
    }
    public function convertirEstudiante(): void {
        $this->requireAuth('admin');$id=$this->inputInt('id');
        $ins=Database::fetchOne("SELECT * FROM inscripciones WHERE id=?",[$id]);
        if(!$ins||$ins['estado']!=='aprobada'){Session::flash('error','Solo inscripciones aprobadas.');$this->redirect('admin/inscripciones/ver?id='.$id);return;}
        $iid=Session::institucionId();$aid=Session::anioLectivoId();
        $anio=Database::fetchValue("SELECT anio FROM anios_lectivos WHERE id=?",[$aid]);
        $num=Database::fetchValue("SELECT COUNT(*)+1 FROM estudiantes WHERE institucion_id=?",[$iid]);
        $cod=$anio.str_pad($num,4,'0',STR_PAD_LEFT);
        $eid=Database::insert("INSERT INTO estudiantes (institucion_id,codigo,nombres,apellidos,fecha_nacimiento,genero,numero_documento,tipo_documento,estado) VALUES (?,?,?,?,?,?,?,?,'activo')",[$iid,$cod,$ins['nombres'],$ins['apellidos'],$ins['fecha_nacimiento'],$ins['genero'],$ins['numero_documento'],$ins['tipo_documento']]);
        if($eid){
            $sc=$this->inputInt('seccion_id');
            if($sc)Database::insert("INSERT INTO matriculas (estudiante_id,seccion_id,anio_lectivo_id,fecha_matricula,estado) VALUES (?,?,?,CURDATE(),'matriculado')",[$eid,$sc,$aid]);
            if($ins['rep_nombres'])Database::insert("INSERT INTO representantes (estudiante_id,nombres,apellidos,numero_documento,parentesco,email,telefono,es_acudiente_principal) VALUES (?,?,?,?,?,?,?,1)",[$eid,$ins['rep_nombres'],$ins['rep_apellidos'],$ins['rep_documento'],$ins['rep_parentesco'],$ins['rep_email'],$ins['rep_telefono']]);
            Database::execute("UPDATE inscripciones SET estado='matriculado' WHERE id=?",[$id]);
            Session::flash('success','Estudiante creado. Codigo: '.$cod);
        }$this->redirect('admin/inscripciones');
    }
}