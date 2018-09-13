<?php
    class Chauffeur{
        //Attributs
        private $idChauffeur;
        private $idDate;
        private $prenom;
        private $nom;
        private $dateNaissance;
        private $numeroIdentite;
        private $permis;
        private $adresse;
        private $telephone;
        private $commentaire;

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
                        $reqAfficheChauffeur = "SELECT idChauffeur, prenom, nom, permis, adresse, telephone, commentaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Chauffeur, Disponibilite WHERE idDate=idDisponibilite AND idDate=? AND statut=?";
                        $reponse1 = $bdd->prepare($reqAfficheChauffeur);
                        $reponse1->execute(array($idDate, 'Libre'));
                        $chauffeurs = array_merge_recursive($chauffeurs, $reponse1->fetchAll());  //Puis ce tableau est concaténé avec le prochain tableau trouvé grâce à l'éventuel prochain idDisponibilité.
                        
                    } //End While($dataId)
                    if(empty($chauffeurs)){
                        echo "Aucun chauffeur disponible à cette période !";
                        return false;
                    }
                    else{
                        $reponse->closeCursor();
                        //Conversion du format du tableau en JSON
                        $chauffeurs = json_encode($chauffeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        return $chauffeurs;
                    }         
                    
                } //End if($dataId)
                else{
                    echo "Aucun chauffeur disponible à cette période !";
                    return false;
                } //End else if ($dataId)
            } //End else (dateDebut >= dateFin)
        } //End afficheChauffeur($dateDebut, $dateFin)

        public static function afficheChauffeurs($statut){
            global $bdd;
            if(empty($statut)){
                $reqAfficheChauffeur = "SELECT prenom, nom, permis, adresse, telephone, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto, statut FROM Chauffeur, Disponibilite WHERE idDate=idDisponibilite";
            }
            else if ($statut=='Libre' || $statut=='Réservé'){
                $reqAfficheChauffeur = "SELECT prenom, nom, permis, adresse, telephone, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto, statut FROM Chauffeur, Disponibilite WHERE idDate=idDisponibilite AND statut=?";
            }
            else{
                echo 'Statut inconnu ! Les statuts disponibles sont : \'Libre\' et \'Réservé\' !';
                return false;
            }
            
            $reponse = $bdd->prepare($reqAfficheChauffeur);
            $reponse->execute(array($statut));

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

        public static function ajoutChauffeur($prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $dateDebut, $dateFin, $commentaire){
            global $bdd;
            $statut = 'Libre';
            //Mie en conformité des dates
            $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));
            //Vérification de la conformité de la période
            if ($dateDebut >= $dateFin){
                echo "La date de fin ne peut être supérieure à la date de début de disponibilité !";
                return false;
            }
            else{  
                //Ajout des dates dans la base
                $requete = 'INSERT INTO Disponibilite (dateDebut, dateFin) VALUES(:dateDebut, :dateFin)';
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array(
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin
                ));
                //Récupération de l'Id de la dernière date entrée
                $reqLastIdDate = 'SELECT idDisponibilite FROM Disponibilite ORDER BY idDisponibilite DESC LIMIT 0,1';
                $reponse = $bdd->query($reqLastIdDate);
                $data = $reponse->fetch();
                $idDisponibilite = $data['idDisponibilite'];
                //Insertion du Chauffeur dans la base
                $reqInsertChauffeur = "INSERT INTO Chauffeur (idDate, prenom, nom, dateNaissance, numeroIdentite, permis, adresse, telephone, commentaire, statut) VALUES (:idDate, :prenom, :nom, :dateNaissance, :numeroIdentite, :permis, :adresse, :telephone, :commentaire, :statut)";
                $reponse = $bdd->prepare($reqInsertChauffeur);
                $reponse->execute(array(
                    'idDate' => $idDisponibilite,
                    'prenom' => $prenom, 
                    'nom' => $nom, 
                    'dateNaissance' => $dateNaissance, 
                    'numeroIdentite' => $numeroIdentite, 
                    'permis' => $permis, 
                    'adresse' => $adresse, 
                    'telephone' => $telephone, 
                    'commentaire' => $commentaire, 
                    'statut' => $statut 
                ));
            
            } //End else

        } //End ajoutChauffeur()

        public static function modifierChauffeur($idChauffeur, $prenom, $nom, $dateNaissance, $numeroIdentite, $permis, $adresse, $telephone, $dateDebut, $dateFin, $commentaire, $statut){
            global $bdd;
            if ($statut!='Libre' && $statut!='Réservé'){
                echo 'Statut inconnu ! Les statuts disponibles sont : \'Libre\' et \'Réservé\' !';
                return false;
            }
            else{
                //Mie en conformité des dates
                $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
                $dateDebut = date("Y-m-d", strtotime($dateDebut));
                $dateFin = date("Y-m-d", strtotime($dateFin));
                //Vérification de la conformité de la période
                if ($dateDebut >= $dateFin){
                    echo "La date de fin ne peut être supérieure à la date de début de disponibilité !";
                    return false;
                }
                else{  
                    //Modification des dates de la base
                    $idDate = Chauffeur::returnId('idDate', 'Chauffeur', 'idChauffeur', $idChauffeur);
                    $reqIdDisponibilite = "UPDATE Disponibilite SET dateDebut=?, dateFin=? WHERE idDisponibilite=?";
                    $reponse = $bdd->prepare($reqIdDisponibilite);
                    $reponse->execute(array($dateDebut, $dateFin, $idDate));

                        $requete = 'UPDATE Chauffeur SET idDate=:idDate, prenom=:prenom, nom=:nom, dateNaissance=:dateNaissance, numeroIdentite=:numeroIdentite, permis=:permis, adresse=:adresse, telephone=:telephone, commentaire=:commentaire, statut=:statut WHERE idChauffeur=:idChauffeur';
                        $reponse = $bdd->prepare($requete);
                        $reponse->execute(array(
                            'idDate' => $idDate,
                            'prenom' => $prenom, 
                            'nom' => $nom, 
                            'dateNaissance' => $dateNaissance, 
                            'numeroIdentite' => $numeroIdentite, 
                            'permis' => $permis, 
                            'adresse' => $adresse, 
                            'telephone' => $telephone, 
                            'commentaire' => $commentaire, 
                            'statut' => $statut,
                            'idChauffeur' => $idChauffeur
                
                        ));
                    
                    $reponse->closeCursor();
                } //End else
            } //End else if (statut)
            

        } //End modifierChauffeur()

        public static function supprimerChauffeur($id){
            global $bdd;
            $requete = 'DELETE FROM Chauffeur WHERE idChauffeur=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            $reponse->closeCursor();

        } //End supprimerChauffeur($id)


        public static function returnId($nomID, $table, $attribut, $valeur){
            global $bdd;
            $requete = "SELECT $nomID FROM $table WHERE $attribut='$valeur'";
            $reponse = $bdd->query($requete);
            if ($data = $reponse->fetch()){
                $id = $data[$nomID];
                $reponse->closeCursor();
                return $id;
            }
            else{
                echo "$table choisi(e) non disponible !";
                return false;
            }
        } //End returnId()
    

    } //End class Chauffeur