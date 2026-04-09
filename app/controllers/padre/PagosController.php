<?php
require_once APP_PATH.'/models/Pago.php';
require_once APP_PATH.'/models/CuentaPorCobrar.php';
require_once APP_PATH.'/models/Estudiante.php';
class PagosController extends Controller {
    public function index(): void {
        $this->requireAuth('padre');
        $userId=Session::userId(); $anioId=Session::anioLectivoId();
        $estudiantes=(new Estudiante())->getByPadre($userId);
        $cuentasPorEst=[];
        foreach($estudiantes as $est){
            $cuentasPorEst[$est['id']]=['estudiante'=>$est,'pendientes'=>(new CuentaPorCobrar())->getPendientesByMatricula($est['matricula_id'])];
        }
        $this->view('padre/pagos/index',['estudiantes'=>$estudiantes,'cuentasPorEst'=>$cuentasPorEst,'wompiPublicKey'=>WOMPI_PUBLIC_KEY,'pageTitle'=>'Pagos y Cartera']);
    }
    public function historial(): void {
        $this->requireAuth('padre');
        $userId=Session::userId(); $anioId=Session::anioLectivoId();
        $estudiantes=(new Estudiante())->getByPadre($userId);
        $historial=[];
        foreach($estudiantes as $est){ foreach((new Pago())->getByMatricula($est['matricula_id'],$anioId) as $p){ $p['estudiante']=$est['nombres'].' '.$est['apellidos']; $historial[]=$p; } }
        usort($historial,fn($a,$b)=>strtotime($b['fecha_pago'])-strtotime($a['fecha_pago']));
        $this->json(['historial'=>$historial]);
    }
    public function iniciarPago(): void {
        $this->requireAuth('padre');
        $cuentaIds=$_POST['cuentas']??[];
        if(empty($cuentaIds)){$this->json(['error'=>'Seleccione al menos una cuenta.'],400);return;}
        $total=0; $descs=[];
        foreach($cuentaIds as $cid){ $cuenta=Database::fetchOne("SELECT * FROM cuentas_por_cobrar WHERE id=?",[(int)$cid]); if($cuenta&&$cuenta['estado']!=='pagado'){$total+=$cuenta['total'];$descs[]=$cuenta['descripcion'];} }
        $this->json(['total'=>$total,'referencia'=>generateCode('PAG',10),'descripcion'=>implode(', ',$descs),'currency'=>'COP']);
    }
    public function confirmacion(): void { $this->requireAuth('padre'); $this->view('padre/pagos/confirmacion',['pageTitle'=>'Confirmacion']); }
}