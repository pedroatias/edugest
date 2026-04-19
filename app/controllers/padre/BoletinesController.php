<?php
require_once APP_PATH . '/models/Boletin.php';

class BoletinesController extends Controller {

    private function getMatricula(): ?array {
              $userId  = Session::userId();
              $anioId  = Session::anioLectivoId();
              $codEst  = $this->input('cod_est', '');

          // Get all children of this parent
          $matriculas = Database::fetchAll("
                      SELECT m.id AS matricula_id, e.id AS estudiante_id,
                                         CONCAT(e.nombres,' ',e.apellidos) AS nombre_completo,
                                                            e.foto, s.nombre_completo AS seccion
                                                                        FROM matriculas m
                                                                                    INNER JOIN estudiantes e ON e.id = m.estudiante_id
                                                                                                INNER JOIN secciones s ON s.id = m.seccion_id
                                                                                                            INNER JOIN representantes r ON r.estudiante_id = e.id
                                                                                                                        INNER JOIN usuarios u ON u.id = r.usuario_id
                                                                                                                                    WHERE u.id = ? AND m.anio_lectivo_id = ?
                                                                                                                                                ORDER BY e.apellidos, e.nombres
                                                                                                                                                        ", [$userId, $anioId]);

          if (empty($matriculas)) {
                        return null;
          }

          if ($codEst) {
                        foreach ($matriculas as $m) {
                                          if ($m['matricula_id'] == $codEst) {
                                                                return ['selected' => $m, 'all' => $matriculas];
                                          }
                        }
          }

          return ['selected' => $matriculas[0], 'all' => $matriculas];
    }

    public function index(): void {
              $this->requireAuth('padre');
              $anioId  = Session::anioLectivoId();

          $data = $this->getMatricula();
              if (!$data) {
                            $this->redirect('inicio');
                            return;
              }

          $selected   = $data['selected'];
              $matriculas = $data['all'];

          // Get periods for this school year
          $periodos = Database::fetchAll(
                        "SELECT * FROM periodos_academicos WHERE anio_lectivo_id=? ORDER BY numero",
                        [$anioId]
                    );

          // Get available boletines (published) for this matricula
          $boletines = (new Boletin())->getByMatricula($selected['matricula_id']);

          // Only show published ones
          $boletinesDisponibles = array_filter($boletines, fn($b) => $b['disponible'] == 1);

          $this->view('padre/boletines/index', [
                                  'pageTitle'           => 'Boletines',
                                  'matriculas'          => $matriculas,
                                  'selected'            => $selected,
                                  'periodos'            => $periodos,
                                  'boletines'           => array_values($boletinesDisponibles),
                              ]);
    }

    public function descargar(): void {
              $this->requireAuth('padre');

          $boletinId  = $this->inputInt('id');
              $userId     = Session::userId();

          // Verify the boletin belongs to a child of this parent
          $boletin = Database::fetchOne("
                      SELECT b.*, pa.nombre AS periodo_nombre
                                  FROM boletines b
                                              INNER JOIN matriculas m ON m.id = b.matricula_id
                                                          INNER JOIN estudiantes e ON e.id = m.estudiante_id
                                                                      INNER JOIN representantes r ON r.estudiante_id = e.id
                                                                                  INNER JOIN usuarios u ON u.id = r.usuario_id
                                                                                              WHERE b.id = ? AND u.id = ? AND b.disponible = 1
                                                                                                      ", [$boletinId, $userId]);

          if (!$boletin) {
                        http_response_code(403);
                        echo 'Acceso no autorizado.';
                        return;
          }

          $filePath = ROOT_PATH . '/uploads/boletines/' . $boletin['archivo_pdf'];

          if (!$boletin['archivo_pdf'] || !file_exists($filePath)) {
                        http_response_code(404);
                        echo 'El archivo del boletín no está disponible.';
                        return;
          }

          header('Content-Type: application/pdf');
              header('Content-Disposition: attachment; filename="Boletin_' . $boletin['periodo_nombre'] . '.pdf"');
              header('Content-Length: ' . filesize($filePath));
              readfile($filePath);
              exit;
    }
}
