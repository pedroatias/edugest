<?php
require_once APP_PATH.'/models/Evento.php';
class CalendarioController extends Controller {
    public function eventos(): void {
        Session::requireLogin();
        $instId=Session::institucionId(); $anioId=Session::anioLectivoId();
        $seccionId=$this->inputInt('seccion_id');
        $eventos=(new Evento())->getForCalendar($instId,$anioId,$seccionId?:0);
        $this->json($eventos);
    }
}