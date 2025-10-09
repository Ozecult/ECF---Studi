<?php
require_once __DIR__ . '/../models/Avis.php';
require_once __DIR__ . '/../models/Signalement.php';

class EmployeController 
{
    private $avisModel;
    private $signalementModel;
    
    public function __construct() 
    {
        $this->avisModel = new Avis();
        $this->signalementModel = new Signalement();
    }
    
    public function showDashboard() 
    {
        $avisEnAttente = $this->avisModel->getAvisEnAttente();
        $avisValides = $this->avisModel->getAvisValides(50);
        $avisRefuses = $this->avisModel->getAvisRefuses(50);
        $signalementsEnAttente = $this->signalementModel->getSignalementsEnAttente();
        $mesSignalements = $this->signalementModel->getMesSignalements($_SESSION['user_id']);
        $signalementsResolus = $this->signalementModel->getSignalementsResolus(50);
        
        $employeData = [
            'avis' => $avisEnAttente,
            'avis_valides' => $avisValides,
            'avis_refuses' => $avisRefuses,
            'signalements' => $signalementsEnAttente,
            'mes_signalements' => $mesSignalements,
            'signalements_resolus' => $signalementsResolus
        ];
        
        include __DIR__ . '/../views/employe.php';
    }
}
