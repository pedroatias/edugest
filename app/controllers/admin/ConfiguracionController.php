<?php
class ConfiguracionController extends Controller {
    public function index(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        $inst=Database::fetchOne("SELECT * FROM instituciones WHERE id=?",[$instId]);
        $anios=Database::fetchAll("SELECT * FROM anios_lectivos WHERE institucion_id=? ORDER BY anio DESC",[$instId]);
        $escala=Database::fetchAll("SELECT * FROM escala_valorativa WHERE institucion_id=? ORDER BY orden",[$instId]);
        $this->view('admin/configuracion/index',['inst'=>$inst,'anios'=>$anios,'escala'=>$escala,'pageTitle'=>'Configuracion']);
    }
    public function actualizar(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        $logo=$this->uploadFile('logo','logos');
        $data=['nombre'=>$this->sanitize($this->input('nombre','')),'nit'=>$this->sanitize($this->input('nit','')),'direccion'=>$this->sanitize($this->input('direccion','')),'telefono'=>$this->sanitize($this->input('telefono','')),'email'=>$this->sanitize($this->input('email','')),'color_primario'=>$this->input('color_primario','#1a73e8'),'color_secundario'=>$this->input('color_secundario','#28a745'),'slogan'=>$this->sanitize($this->input('slogan',''))];
        if($logo)$data['logo']=$logo;
        Database::execute("UPDATE instituciones SET nombre=?,nit=?,direccion=?,telefono=?,email=?,color_primario=?,color_secundario=?,slogan=?".(isset($data['logo'])?',logo=?':'')." WHERE id=?",[...array_values($data),$instId]);
        Session::flash('success','Configuracion actualizada.'); $this->redirect('admin/configuracion');
    }
    public function aniosLectivos(): void { $this->requireAuth('admin'); $this->redirect('admin/configuracion'); }
    public function crearAnio(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId(); $anio=$this->inputInt('anio');
        Database::insert("INSERT INTO anios_lectivos (institucion_id,anio,nombre,fecha_inicio,fecha_fin) VALUES (?,?,?,?,?)",[$instId,$anio,"Ano Lectivo $anio",$this->input('fecha_inicio',''),$this->input('fecha_fin','')]);
        Session::flash('success',"Ano $anio creado."); $this->redirect('admin/configuracion');
    }
    public function activarAnio(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId(); $id=$this->inputInt('id');
        Database::execute("UPDATE anios_lectivos SET activo=0 WHERE institucion_id=?",[$instId]);
        Database::execute("UPDATE anios_lectivos SET activo=1 WHERE id=? AND institucion_id=?",[$id,$instId]);
        $_SESSION['anio_lectivo_id']=$id;
        $this->json(['success'=>true]);
    }
    public function usuarios(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        $usuarios=Database::fetchAll("SELECT * FROM usuarios WHERE institucion_id=? AND rol IN ('admin','docente') ORDER BY rol,apellidos",[$instId]);
        $this->view('admin/configuracion/usuarios',['usuarios'=>$usuarios,'pageTitle'=>'Usuarios del Sistema']);
    }
    public function crearUsuario(): void {
        $this->requireAuth('admin');
        $instId=Session::institucionId();
        require_once APP_PATH.'/models/Usuario.php';
        $usuarioModel=new Usuario();
        $id=Database::insert("INSERT INTO usuarios (institucion_id,numero_documento,username,password,rol,nombres,apellidos,email,telefono) VALUES (?,?,?,?,?,?,?,?,?)",[$instId,$this->sanitize($this->input('numero_documento','')),$this->sanitize($this->input('username','')),$usuarioModel->hashPassword($this->input('password','123456')),$this->input('rol','docente'),$this->sanitize($this->input('nombres','')),$this->sanitize($this->input('apellidos','')),$this->sanitize($this->input('email','')),$this->sanitize($this->input('telefono',''))]);
        if($id&&$this->input('rol')=='docente'){ Database::insert("INSERT INTO docentes (usuario_id) VALUES (?)",[$id]); }
        Session::flash('success','Usuario creado. Contrasena temporal: 123456'); $this->redirect('admin/usuarios');
    }
    public function actualizarUsuario(): void {
        $this->requireAuth('admin');
        $id=$this->inputInt('id');
        Database::execute("UPDATE usuarios SET nombres=?,apellidos=?,email=?,telefono=?,activo=? WHERE id=?",[$this->sanitize($this->input('nombres','')),$this->sanitize($this->input('apellidos','')),$this->sanitize($this->input('email','')),$this->sanitize($this->input('telefono','')),$this->input('activo',1)?1:0,$id]);
        Session::flash('success','Usuario actualizado.'); $this->redirect('admin/usuarios');
    }
}