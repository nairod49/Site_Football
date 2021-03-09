<?php

require_once 'Framework/Controleur.php';
require_once 'Modele/Rencontre.php';
require_once 'Modele/Convocation.php';
require_once 'Modele/Convoquee.php';

class ControleurRencontre extends Controleur {
    
    private $rencontre;
    private $convocation;
    private $convoquee;

    public function __construct() {
        $this->rencontre = new Rencontre();
        $this->convocation = new Convocation();
        $this->convoquee = new Convoquee();
    }

    public function index() {
        $rencontres = $this->rencontre->getRencontres();
        $this->genererVue(array('rencontres' => $rencontres));

        if(isset($_SESSION['errAjoutRen'])){
            echo"              
            <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='1700'>
                <div class='d-flex justify-content-center'>    
                    <div class='toast-body'>
                        Cette rencontre existe déjà
                    </div>
                </div>
            </div>
            ";
            unset($_SESSION['errAjoutRen']);
        }

        if(isset($_SESSION['AjoutRen'])){
            echo"              
            <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='1700'>
                <div class='d-flex justify-content-center'>    
                    <div class='toast-body'>
                        Ajout réussi
                    </div>
                </div>
            </div>
            ";
            unset($_SESSION['AjoutRen']);
        }

        if(isset($_SESSION['UpRen'])){
            echo"              
            <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='1700'>
                <div class='d-flex justify-content-center'>    
                    <div class='toast-body'>
                        La rencontre a été mise à jour
                    </div>
                </div>
            </div>
            ";
            unset($_SESSION['UpRen']);
        }

        if(isset($_SESSION['errUpRen'])){
            echo"              
            <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='4000'>
                <div class='d-flex justify-content-center'>    
                    <div class='toast-body'>
                        Cette rencontre ne peut pas être modifiée car elle est liée à une convocation
                    </div>
                </div>
            </div>
            ";
            unset($_SESSION['errUpRen']);
        }

        if(isset($_SESSION['DelRen'])){
            echo"              
            <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='4000'>
                <div class='d-flex justify-content-center'>    
                    <div class='toast-body'>
                        Cette rencontre a bien été supprimé
                    </div>
                </div>
            </div>
            ";
            unset($_SESSION['DelRen']);
        }

        if(isset($_SESSION['ImportRen'])){
            echo"              
            <div class='toast align-items-center position-absolute top-50 start-50 translate-middle text-white bg-secondary' id='myToast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-delay='4000'>
                <div class='d-flex justify-content-center'>    
                    <div class='toast-body'>
                        Import réussi
                    </div>
                </div>
            </div>
            ";
            unset($_SESSION['ImportRen']);
        }
    }

    
    public function ajoutRencontre() {
        if($this->SecretaireisConnected()){
            $competitions = $this->rencontre->getCompetitions();
            $this->genererVue(array('competitions' => $competitions));
        }
    }

    public function valideRencontre(){
        if($this->SecretaireisConnected()){
            $categorie = $this->requete->getParametre("categorie");
            $competition = $this->requete->getParametre("competition");
            $Equipe = $this->requete->getParametre("Equipe");
            $EquipeAdv = $this->requete->getParametre("EquipeAdv");
            $date = implode('-', array_reverse(explode('/', $this->requete->getParametre("date"))));
            $heure = $this->requete->getParametre("heure");
            $terrain = $this->requete->getParametre("terrain");
            $site = $this->requete->getParametre("site");

            if(isset($categorie) && isset($competition) && isset($Equipe) && isset($EquipeAdv) 
            && isset($date) && isset($heure) && isset($terrain) && isset($site)){
                $res = $this->rencontre->getRencontre($categorie, $competition, $Equipe, $EquipeAdv, $date,
                $heure, $terrain, $site);
                if(!$res){
                    $this->rencontre->addRencontre($categorie, $competition, $Equipe, $EquipeAdv, $date,
                    $heure, $terrain, $site);
                    $_SESSION['AjoutRen']='AjoutRen';
                    $this->executerAction('index');
                }
                else{
                    $_SESSION['errAjoutRen']='errAjoutRen';
                    $this->executerAction('index');
                }
            }
        }
    }

    public function modifRencontre(){
        if($this->SecretaireisConnected()){
            $id_rencontre = $this->requete->getParametre("id");
            $convo = $this->convocation->getConvocationIdRen($id_rencontre);

            if(isset($id_rencontre)){
                if(!$convo){ 
                    $res = $this->rencontre->getRencontreId($id_rencontre);
                    $this->genererVue(array('rencontre' => $res));
                }
                else{
                    $_SESSION['errUpRen']='errUpRen';
                    $this->executerAction('index');
                }
            }
        }
    }

    public function UpdateRencontre(){
        if($this->SecretaireisConnected()){
            $id_rencontre = $this->requete->getParametre("id");
            $categorie = $this->requete->getParametre("categorie");
            $competition = $this->requete->getParametre("competition");
            $Equipe = $this->requete->getParametre("Equipe");
            $EquipeAdv = $this->requete->getParametre("EquipeAdv");
            $date = implode('-', array_reverse(explode('/', $this->requete->getParametre("date"))));
            $heure = $this->requete->getParametre("heure");
            $terrain = $this->requete->getParametre("terrain");
            $site = $this->requete->getParametre("site");

            if(isset($categorie) && isset($competition) && isset($Equipe) && isset($EquipeAdv) 
            && isset($date) && isset($heure) && isset($terrain) && isset($site)){
                $this->rencontre->updateRencontre($EquipeAdv, $date, $heure, $terrain, $site, $id_rencontre);
                $_SESSION['UpRen']='UpRen';
                $this->executerAction('index');
            }
        }
    }

    public function supprimer(){
        if($this->SecretaireisConnected()){
            $id_rencontre = $this->requete->getParametre("id");
            
            if(isset($id_rencontre)){
                //testé si $convo retourne quelque chose
                $convo = $this->convocation->getConvocationIdRen($id_rencontre);
                if($convo){
                    $id_convocation = $convo['id_convocation'];
                    // on supprime les convoqués
                    $this->convoquee->delConvoquee($id_convocation);
                }
                // on supprime la convocation liée a la rencontre
                $this->convocation->delConvocation($id_rencontre);
                // on supprime la rencontre
                $this->rencontre->delRencontre($id_rencontre);
                $_SESSION['DelRen']='DelRen';
                $this->executerAction('index');
            }
        }
    }

    public function importDonnee(){
        if($this->SecretaireisConnected()){
            $this->genererVue();
        }
    }

    public function import(){
        if($this->SecretaireisConnected()){
            $fichier = $this->requete->getParametre("CsvFile");     
            if (file_exists($fichier) && $id_file = fopen($fichier, "r")) {
                while ($tab = fgetcsv($id_file, 200, ";")) {
                    //Si la première ligne du fichier contient les noms des colonnes on ne la lit pas
                    if($tab[0] != 'Categorie'){
                        $categorie = $tab[0];
                        $competition = $tab[1];
                        $Equipe = $tab[2];
                        $EquipeAdverse = $tab[3];
                        $date = implode('-', array_reverse(explode('/', $tab[4])));
                        $heure = $tab[5];
                        $terrain = $tab[6];
                        $site = $tab[7];

                        $res = $this->rencontre->getRencontre($categorie, $competition, $Equipe, $EquipeAdverse, $date,
                        $heure, $terrain, $site);
                        //je verifie que ma rencontre n'existe déjà pas
                        if(!$res){
                            $this->rencontre->addRencontre($categorie, $competition, $Equipe, $EquipeAdverse, $date,
                            $heure, $terrain, $site);        
                        }
                        $_SESSION['ImportRen']='ImportRen';
                        $this->executerAction('index'); 
                    }
                }
                fclose($id_file);
            }
        }
    }

}