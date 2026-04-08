<?php
class PdfHelper {
    public static function generarBoletin(int $matriculaId, int $periodoId): bool {
        require_once ROOT_PATH . '/vendor/tcpdf/tcpdf.php';
        require_once APP_PATH . '/models/Calificacion.php';

        $matricula = Database::fetchOne("
            SELECT m.*, e.nombres, e.apellidos, e.codigo, e.foto,
                   s.nombre_completo AS seccion, g.nombre AS grado,
                   CONCAT(e.nombres,' ',e.apellidos) AS nombre_completo,
                   al.anio, inst.nombre AS institucion, inst.logo AS inst_logo
            FROM matriculas m
            INNER JOIN estudiantes e ON e.id = m.estudiante_id
            INNER JOIN secciones s ON s.id = m.seccion_id
            INNER JOIN grados g ON g.id = s.grado_id
            INNER JOIN anios_lectivos al ON al.id = m.anio_lectivo_id
            INNER JOIN instituciones inst ON inst.id = e.institucion_id
            WHERE m.id = ?
        ", [$matriculaId]);

        if (!$matricula) return false;

        $periodo = Database::fetchOne("SELECT * FROM periodos_academicos WHERE id=?", [$periodoId]);
        $notas   = (new Calificacion())->getNotasPorAsignatura($matriculaId, $periodoId);
        $escala  = Database::fetchAll("SELECT * FROM escala_valorativa WHERE institucion_id=? ORDER BY nota_minima", [$matricula['id']]);

        // Create PDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($matricula['institucion']);
        $pdf->SetTitle('Boletin - ' . $matricula['nombre_completo']);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, strtoupper($matricula['institucion']), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6, 'INFORME ACADEMICO - ' . strtoupper($periodo['nombre']) . ' ' . $matricula['anio'], 0, 1, 'C');
        $pdf->Ln(5);

        // Student info
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 6, 'Estudiante:', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, $matricula['nombre_completo'], 0, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(25, 6, 'Codigo:', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(30, 6, $matricula['codigo'], 0, 1);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(40, 6, 'Grado:', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 6, $matricula['grado'] . ' - ' . $matricula['seccion'], 0, 1);
        $pdf->Ln(5);

        // Grades table header
        $pdf->SetFillColor(52, 73, 94);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(70, 7, 'ASIGNATURA', 1, 0, 'C', true);

        $estructura = Database::fetchAll("SELECT * FROM estructura_notas WHERE institucion_id=? AND activo=1 ORDER BY orden", [$matricula['id']]);
        $colW = count($estructura) > 0 ? (80 / count($estructura)) : 20;
        foreach ($estructura as $comp) {
            $pdf->Cell($colW, 7, strtoupper($comp['nombre']), 1, 0, 'C', true);
        }
        $pdf->Cell(30, 7, 'DEFINITIVA', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);

        // Grades rows
        $pdf->SetFont('helvetica', '', 9);
        $fill = false;
        foreach ($notas as $asig) {
            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
            $pdf->Cell(70, 6, $asig['asignatura'], 1, 0, 'L', $fill);
            foreach ($estructura as $comp) {
                $nota = 0;
                foreach ($asig['componentes'] as $c) {
                    if ($c['id'] == $comp['id']) { $nota = $c['nota']; break; }
                }
                $pdf->Cell($colW, 6, number_format($nota, 1), 1, 0, 'C', $fill);
            }
            // Definitiva color
            $def = $asig['nota_periodo'];
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(30, 6, number_format($def, 2), 1, 1, 'C', $fill);
            $pdf->SetFont('helvetica', '', 9);
            $fill = !$fill;
        }

        // Escala
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(0, 5, 'ESCALA VALORATIVA:', 0, 1);
        $pdf->SetFont('helvetica', '', 8);
        foreach ($escala as $e) {
            $pdf->SetFillColor(hexdec(substr($e['color'],1,2)), hexdec(substr($e['color'],3,2)), hexdec(substr($e['color'],5,2)));
            $pdf->Cell(25, 5, $e['nombre'], 1, 0, 'C', true);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35, 5, $e['nota_minima'] . ' - ' . $e['nota_maxima'], 1, 0, 'C');
            $pdf->Cell(10, 5, '', 0, 0);
        }
        $pdf->Ln();

        // Save PDF
        $dir = UPLOADS_PATH . '/boletines';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = 'boletin_' . $matriculaId . '_' . $periodoId . '_' . time() . '.pdf';
        $pdf->Output($dir . '/' . $filename, 'F');

        // Update DB
        $existing = Database::fetchOne("SELECT id FROM boletines WHERE matricula_id=? AND periodo_id=?", [$matriculaId, $periodoId]);
        if ($existing) {
            Database::execute("UPDATE boletines SET archivo_pdf=?,fecha_generacion=NOW(),generado=1 WHERE id=?", [$filename, $existing['id']]);
        } else {
            Database::insert("INSERT INTO boletines (matricula_id,periodo_id,archivo_pdf,fecha_generacion,fecha_disponible,disponible,generado) VALUES (?,?,?,NOW(),CURDATE(),0,1)",
                [$matriculaId, $periodoId, $filename]);
        }
        return true;
    }

    public static function estadoCuentaPdf(array $matricula, array $cuentas): void {
        require_once ROOT_PATH . '/vendor/tcpdf/tcpdf.php';
        $pdf = new TCPDF('P','mm','A4',true,'UTF-8');
        $pdf->SetTitle('Estado de Cuenta - ' . $matricula['nombres'] . ' ' . $matricula['apellidos']);
        $pdf->SetMargins(15,15,15);
        $pdf->SetAutoPageBreak(true,15);
        $pdf->AddPage();
        $pdf->SetFont('helvetica','B',14);
        $pdf->Cell(0,8,'ESTADO DE CUENTA',0,1,'C');
        $pdf->SetFont('helvetica','',10);
        $pdf->Cell(0,6,'Estudiante: ' . $matricula['nombres'] . ' ' . $matricula['apellidos'] . ' | Codigo: ' . $matricula['codigo'],0,1);
        $pdf->Cell(0,6,'Grupo: ' . $matricula['seccion'],0,1);
        $pdf->Ln(3);
        $pdf->SetFillColor(52,73,94);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('helvetica','B',9);
        $pdf->Cell(70,7,'CONCEPTO',1,0,'C',true);
        $pdf->Cell(30,7,'VENCIMIENTO',1,0,'C',true);
        $pdf->Cell(25,7,'VALOR',1,0,'C',true);
        $pdf->Cell(25,7,'PAGADO',1,0,'C',true);
        $pdf->Cell(25,7,'ESTADO',1,1,'C',true);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica','',9);
        foreach ($cuentas as $c) {
            $color = match($c['estado']) { 'pagado'=>[144,238,144], 'pendiente'=>($c['fecha_vencimiento']<date('Y-m-d')?[255,182,193]:[255,255,255]), default=>[255,255,224] };
            $pdf->SetFillColor(...$color);
            $pdf->Cell(70,6,$c['descripcion'],1,0,'L',true);
            $pdf->Cell(30,6,formatDate($c['fecha_vencimiento']),1,0,'C',true);
            $pdf->Cell(25,6,number_format($c['total'],0,',','.'),1,0,'R',true);
            $pdf->Cell(25,6,number_format($c['pagado'],0,',','.'),1,0,'R',true);
            $pdf->Cell(25,6,strtoupper($c['estado']),1,1,'C',true);
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="estado_cuenta.pdf"');
        $pdf->Output('', 'I');
        exit;
    }
}