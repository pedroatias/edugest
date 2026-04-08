<?php
class SesionController extends Controller {
    public function verificar(): void {
        if (!Session::isLoggedIn()) { $this->json(['status' => 'expired']); return; }
        $lastActivity = Session::get('last_activity', time());
        $remaining    = (SESSION_LIFETIME) - (time() - $lastActivity);
        if ($remaining <= 0) {
            Session::destroy();
            $this->json(['status' => 'expired']);
            return;
        }
        $this->json(['status' => 'active', 'remaining' => $remaining]);
    }
}