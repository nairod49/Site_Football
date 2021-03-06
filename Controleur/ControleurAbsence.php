<?php

require_once 'Framework/Controleur.php';
require_once 'Modele/Absence.php';
require_once 'Modele/Effectif.php';

class ControleurAbsence extends Controleur {

    private $absence;
    private $effectif;

    public function __construct(){
        $this->absence = new Absence();
        $this->effectif = new Effectif();
    }

    public function index(){
            $absences =$this->absence->getAbsences();
            $this->genererVue(array('absences' => $absences));

            if(isset($_SESSION['ajoutAb'])){
             echo"              
                <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='1700'>
                    <div class='d-flex justify-content-center'>    
                        <div class='toast-body'>
                            Ajout réussi
                        </div>
                    </div>
                </div>
                ";
                unset($_SESSION['ajoutAb']);
            }

            if(isset($_SESSION['errAjoutAb'])){
                echo"              
                <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='1700'>
                    <div class='d-flex justify-content-center'>    
                        <div class='toast-body'>
                            Cette personne est déjà absente cette journée
                        </div>
                    </div>
                </div>
                ";
                unset($_SESSION['errAjoutAb']);
            }
            
            if(isset($_SESSION['supAb'])){
                echo"              
                <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='1700'>
                    <div class='d-flex justify-content-center'>    
                        <div class='toast-body'>
                            Absence supprimé
                        </div>
                    </div>
                </div>
                ";
                unset($_SESSION['supAb']);
            }
    }

    public function ajoutAbsence(){
        if($this->isConnect()){
            $effectifs = $this->effectif->getEffectifs();
            $this->genererVue(array('effectifs' => $effectifs)); 
        }
    }

    public function valideAbsence(){
        if($this->isConnect()){
            $id = $this->requete->getParametre("idEffectif");
            $date = empty($this->requete->getParametre("date")) ? date("Y-m-d") : implode('-', array_reverse(explode('/', $this->requete->getParametre("date"))));
            $code = $this->requete->getParametre("code");
            $uneAbsence = $this->absence->getAbsence($id, $date, $code);
            if(!$uneAbsence){
                if(isset($id) && isset($date) && isset($code)){
                    $_SESSION['ajoutAb']='ajoutAb';
                    $this->absence->addAbsence($id, $date, $code);
                    $this->executerAction('index');
                }
            }
            else{
                $_SESSION['errAjoutAb']='errAjoutAb';
                $this->executerAction('index');
            }
        }
    }

    public function supprimer(){
        if($this->isConnect()){
            $id = $this->requete->getParametre("id");
            if(isset($id)){
                $_SESSION['supAb']='supAb';
                $this->absence->delAbsence($id);
                $this->executerAction('index');
            }
        }
    }

}