<?php
class GruposController extends Controller {
    public function grados(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        $grados=Database::fetchAll("SELECT g.*,COUNT(s.id) AS num_secciones FROM grados g LEFT JOIN secciones s ON s.grado_id=g.id AND s.anio_lectivo_id=? WHERE g.institucion_id=? GROUP BY g.id ORDER BY g.orden",[$_SESSION['anio_lectivo_id']??0,$instId]);
        $this->view('admin/grados/index',['grados'=>$grados,'pageTitle'=>'Grados']);
    }
    public function crearGrado(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        Database::insert("INSERT INTO grados (institucion_id,nombre,nivel,orden) VALUES (?,?,?,?)",[$instId,$this->sanitize($this->input('nombre','')),  $this->sanitize($this->input('nivel','')),$this->inputInt('orden')]);
        Session::flash('success','Grado creado.'); $this->redirect('admin/grados');
    }
    public function actualizarGrado(): void {
        $this->requireAuth('admin');
        $id=$this->inputInt('id');
        Database::execute("UPDATE grados SET nombre=?,nivel=?,orden=? WHERE id=?",[$this->sanitize($this->input('nombre','')), $this->sanitize($this->input('nivel','')),$this->inputInt('orden'),$id]);
        Session::flash('success','Grado actualizado.'); $this->redirect('admin/grados');
    }
    public function secciones(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId(); $anioId=Session::anioLectivoId();
        $secciones=Database::fetchAll("SELECT s.*,g.nombre AS grado,COALESCE(s.nombre_completo,CONCAT(g.nombre,'-',s.nombre)) AS display_name,CONCAT(u.nombres,' ',u.apellidos) AS director,COUNT(m.id) AS num_estudiantes FROM secciones s INNER JOIN grados g ON g.id=s.grado_id LEFT JOIN usuarios u ON u.id=s.docente_director_id LEFT JOIN matriculas m ON m.seccion_id=s.id AND m.anio_lectivo_id=s.anio_lectivo_id WHERE s.anio_lectivo_id=? AND g.institucion_id=? GROUP BY s.id ORDER BY g.orden,s.nombre",[$anioId,$instId]);
        $grados=Database::fetchAll("SELECT * FROM grados WHERE institucion_id=? AND activo=1 ORDER BY orden",[$instId]);
        $docentes=Database::fetchAll("SELECT u.id,CONCAT(u.nombres,' ',u.apellidos) AS nombre FROM usuarios u WHERE u.institucion_id=? AND u.rol='docente' AND u.activo=1 ORDER BY u.apellidos",[$instId]);
        $this->view('admin/secciones/index',['secciones'=>$secciones,'grados'=>$grados,'docentes'=>$docentes,'pageTitle'=>'Secciones']);
    }
    public function crearSeccion(): void {
        $this->requireAuth('admin');
        $anioId=Session::anioLectivoId(); $gradoId=$this->inputInt('grado_id'); $nombre=$this->sanitize($this->input('nombre',''));
        $grado=Database::fetchOne("SELECT nombre FROM grados WHERE id=?",[$gradoId]);
        $nombreCompleto=$grado['nombre'].'-'.$nombre;
        Database::insert("INSERT INTO secciones (grado_id,anio_lectivo_id,nombre,nombre_completo,capacidad_max,docente_director_id) VALUES (?,?,?,?,?,?)",[$gradoId,$anioId,$nombre,$nombreCompleto,$this->inputInt('capacidad_max',35),$this->inputInt('director_id')?:null]);
        Session::flash('success','Seccion creada.'); $this->redirect('admin/secciones');
    }
    public function actualizarSeccion(): void {
        $this->requireAuth('admin');
        $id=$this->inputInt('id');
        Database::execute("UPDATE secciones SET nombre=?,nombre_completo=?,capacidad_max=?,docente_director_id=? WHERE id=?",[$this->sanitize($this->input('nombre','')), $this->sanitize($this->input('nombre_completo','')),$this->inputInt('capacidad_max',35),$this->inputInt('director_id')?:null,$id]);
        Session::flash('success','Seccion actualizada.'); $this->redirect('admin/secciones');
    }
}