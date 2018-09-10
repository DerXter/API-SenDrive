<?php
    class Chauffeur{
        //Attributs
        private $_idChauffeur;
        private $_idDate;
        private $_prenom;
        private $_nom;
        private $_dateNaissance;
        private $_numeroIdentite;
        private $_permis;
        private $_adresse;
        private $_telephone;
        private $_commentaire;

        //Accesseurs

        //Autres fonctions
        public static function afficheChauffeur($dateDebut, $dateFin){
            global $bdd;
            //Changement du format de la date en yyyy-mm-dd
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));
            //Vérification de la conformité de la période
            if ($dateDebut >= $dateFin){
                echo "La date d'arrivée ne peut être supérieure à la date de départ !";
                return false;
            }
            else{
                $chauffeurs = array(); //Tableau qui va contenir les chauffeurs diponibles entre les deux dates spécifiées
                $reqRecupIdDisponibilite = "SELECT idDisponibilite FROM Disponibilite WHERE dateDebut<=? AND dateFin>=?";
                $reponse = $bdd->prepare($reqRecupIdDisponibilite);
                $reponse->execute(array($dateDebut, $dateFin));
                if ($dataId=$reponse->fetchAll()){ //Tableau contenant tous les 'idDisponibilité' correspondants aux dates spécifiées
                    foreach($dataId as $donnees) {
                        # Pour chaque 'idDisponibilité' trouvé, on retourne un tableau contenant l'ensemble des chauffeurs diponibles
                        $idDate = $donnees['idDisponibilite'];
                        $reqAfficheChauffeur = "SELECT idChauffeur, prenom, nom, permis, adresse, telephone, commentaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Chauffeur, Disponibilite WHERE idDate=idDisponibilite AND idDate=?";
                        $reponse1 = $bdd->prepare($reqAfficheChauffeur);
                        $reponse1->execute(array($idDate));
                        $chauffeurs = array_merge_recursive($chauffeurs, $reponse1->fetchAll());  //Puis ce tableau est concaténé avec le prochain tableau trouvé grâce à l'éventuel prochain idDisponibilité.
                        
                    } //End While($dataId)
                    $reponse->closeCursor();
                    //Conversion du format du tableau en JSON
                    $chauffeurs = json_encode($chauffeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    
                    return $chauffeurs;
                } //End if($dataId)
                else{
                    echo "Aucun chauffeur disponible à cette période !";
                    return false;
                } //End else if ($dataId)
            } //End else (dateDebut >= dateFin)
        } //End afficheChauffeur($dateDebut, $dateFin)

        public static function afficheChauffeurs(){
            global $bdd;
            $reqAfficheChauffeur = "SELECT prenom, nom, permis, adresse, telephone, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Chauffeur, Disponibilite WHERE idDate=idDisponibilite";
            $reponse = $bdd->query($reqAfficheChauffeur);
            if ($chauffeurs = $reponse->fetchAll()){
                $reponse->closeCursor();
                //Conversion du format du tableau en JSON
                $chauffeurs = json_encode($chauffeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
                return $chauffeurs;
            } //End if($chauffeurs)
            else{
                echo "Aucun chauffeur trouvé !";
                return false;
            }
        } //End afficheChauffeurs()
    

    } //End class Chauffeur