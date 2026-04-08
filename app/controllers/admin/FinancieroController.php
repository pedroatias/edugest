<?php
require_once APP_PATH . '/models/CuentaPorCobrar.php';

class FinancieroController extends Controller {
    public function conceptos(): void {
        $this->requireAuth('admin');
        $instId   = Session::institucionId();
        $conceptos= Database::fetchAll("SELECT * FROM conceptos_cobro WHERE institucion_id=? ORDER BY tipo,nombre", [$instId]);
        $this->view('admin/financiero/conceptos', ['conceptos'=>$conceptos,'pageTitle'=>'Conceptos de Cobro']);
    }

    public function crearConcepto(): void {
        $this->requireAuth('admin');
        $instId = Session::institucionId();
        Database::insert("INSERT INTO conceptos_cobro (institucion_id,nombre,descripcion,tipo,valor,aplica_interes_mora,porcentaje_mora) VALUES (?,?,?,?,?,?,?)", [
            $instId,
            $this->sanitize($this->input('nombre','')),
            $this->sanitize($this->input('descripcion','')),
            $this->input('tipo','otro'),
            $this->inputFloat('valor'),
            $this->input('aplica_mora',0) ? 1 : 0,
            $this->inputFloat('porcentaje_mora')
        ]);
        Session::flash('success','Concepto creado.');
        $this->redirect('admin/conceptos-cobro');
    }

    public function cobros(): void {
        $this->requireAuth('admin');
        $instId = Session::institucionId();
        $anioId = Session::anioLectivoId();
        $conceptos = Database::fetchAll("SELECT * FROM conceptos_cobro WHERE institucion_id=? AND activo=1", [$instId]);
        $resumen   = Database::fetchAll("SELECT cc.nombre, cc.tipo, COUNT(cpc.id) AS num_cobros, SUM(cpc.total) AS total_cobrado, SUM(CASE WHEN cpc.estado='pagado' THEN cpc.total ELSE 0 END) AS recaudado FROM cuentas_por_cobrar cpc INNER JOIN conceptos_cobro cc ON cc.id=cpc.concepto_id INNER JOIN matriculas m ON m.id=cpc.matricula_id INNER JOIN estudiantes e ON e.id=m.estudiante_id WHERE e.institucion_id=? AND cpc.anio_lectivo_id=? GROUP BY cc.id", [$instId, $anioId]);
        $this->view('admin/financiero/cobros', compact('conceptos','resumen') + ['pageTitle'=>'Gestion de Cobros']);
    }

    public function generarMasivo(): void {
        $this->requireAuth('admin');
        $instId    = Session::institucionId();
        $anioId    = Session::anioLectivoId();
        $conceptoId= $this->inputInt('concepto_id');
        $count     = (new CuentaPorCobrar())->generarPensiones($instId, $anioId, $conceptoId);
        Session::flash('success', "Se generaron $count cuotas exitosamente.");
        $this->redirect('admin/cobros');
    }

    public function estadoCuenta(): void {
        $this->requireAuth('admin');
        $matriculaId = $this->inputInt('matricula_id');
        $anioId      = Session::anioLectivoId();
        $matricula   = Database::fetchOne("SELECT m.*, e.nombres, e.apellidos, e.codigo, s.nombre_completo AS seccion FROM matriculas m INNER JOIN estudiantes e ON e.id=m.estudiante_id INNER JOIN secciones s ON s.id=m.seccion_id WHERE m.id=?", [$matriculaId]);
        if (!$matricula) { $this->redirect('admin/estudiantes'); return; }
        $cuentas = (new CuentaPorCobrar())->getEstadoCuenta($matriculaId, $anioId);
        $total   = array_sum(array_column($cuentas,'total'));
        $pagado  = array_sum(array_column($cuentas,'pagado'));
        $this->view('admin/financiero/estado_cuenta', compact('matricula','cuentas','total','pagado') + ['pageTitle'=>'Estado de Cuenta']);
    }
}