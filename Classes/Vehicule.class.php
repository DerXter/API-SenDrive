<?php
    class Vehicule{
        //Attributs
        private $_idVehicule;
        private $_idMarque;
        private $_idModele;
        private $_idType;
        private $_idProprietaire;
        private $_idCarburant;
        private $_idDate;
        private $_immatriculation;
        private $_climatisation;
        private $_nombreDePortes;
        private $_nombreDePlaces;
        private $_description;
        private $_prix;
        private $_boiteDeVitesse;

        //Accesseurs

        //Autres fonctions
        public static function afficheVehicule($dateDebut, $dateFin){
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
                $vehicules = array(); //Tableau qui va contenir les véhicules diponibles entre les deux dates spécifiées
                $reqRecupIdDisponibilite = "SELECT idDisponibilite FROM Disponibilite WHERE dateDebut<=? AND dateFin>=?";
                $reponse = $bdd->prepare($reqRecupIdDisponibilite);
                $reponse->execute(array($dateDebut, $dateFin));
                if ($dataId=$reponse->fetchAll()){ //Tableau contenant tous les 'idDisponibilité' correspondants aux dates spécifiées
                
                    foreach($dataId as $donnees) {
                        # Pour chaque 'idDisponibilité' trouvé, on retourne un tableau contenant l'ensemble des véhicules diponibles
                        $idDate = $donnees['idDisponibilite'];
                        $reqAfficheVehicule = "SELECT DISTINCT idVehicule, marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND idDate=?";
                        $reponse1 = $bdd->prepare($reqAfficheVehicule);
                        $reponse1->execute(array($idDate));
                        $vehicules = array_merge_recursive($vehicules, $reponse1->fetchAll());  //Puis ce tableau est concaténé avec le prochain tableau trouvé grâce à l'éventuel prochain idDisponibilité.
                        
                    } //End While($dataId)
                    $reponse->closeCursor();

                    //Conversion du format du tableau en JSON
                    $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    return $vehicules;
                } //End if(dataID)
                else{
                    echo "Aucun véhicule disponible à cette période !";
                    return false;
                }
            } //End else(dateDebut>=dateFin)
    
        } //End afficheVehicules

        public static function afficheVehicules(){
            global $bdd;
            $reqAfficheVehicule = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite";
            $reponse = $bdd->query($reqAfficheVehicule);
            
            if($vehicules = $reponse->fetchAll()){
                $reponse->closeCursor();
                //Conversion du format du tableau en JSON
                $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                return $vehicules;
            } //End if
            else{
                echo "Aucun véhicule trouvée !";
                return false;
            }
           
        } //End afficheVehicules()

        public static function filtreVehicule($filtre){ //Affiche des critères de véhicule selon le filtre
            global $bdd;
            if ($filtre=='marque' || $filtre=='modele' || $filtre=='typevehicule' || $filtre=='carburant'){
                $requete = "SELECT * FROM $filtre";
            }
            else{
                switch ($filtre){
                    case 'clim-oui':
                        $requete = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND climatisation='oui' ";
                    break;
                    case 'clim-non':
                        $requete = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite AND climatisation='non' ";
                    break;
                    default:
                        echo 'Filtre inconnu !';
                        return false;
                } //End switch
            } //End else
            $reponse = $bdd->query($requete);
            if ($vehicules = $reponse->fetchAll()){
                $reponse->closeCursor();
                $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                return $vehicules;
            } //End if
            else{
                echo "Aucune données disponibles pour ce filtre !";
                return false;
            } //End else
           
        } //End filtreVehicule()

        public static function ajoutVehicule($idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $dateDebut, $dateFin, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse){
            global $bdd;
            //Ajustement du format des dates
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));
            //Vérification de la conformité de la période
            if ($dateDebut >= $dateFin){
                echo "La date d'arrivée ne peut être supérieure à la date de départ !";
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
                $IdDisponibilite = $data['idDisponibilite'];

                if (!empty($IdDisponibilite)){
                    $requete = 'INSERT INTO Vehicule(idMarque, idModele, idType, idProprietaire, idCarburant, idDate, immatriculation, climatisation, nombreDePortes, nombreDePlaces, description, prix, boiteDeVitesse) VALUES(:idMarque, :idModele, :idType, :idProprietaire, :idCarburant, :idDate, :immatriculation, :climatisation, :nbPorte, :nbPlace, :description, :prix, :boiteDeVitesse)';
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array(
                    'idMarque' => $idMarque,
                    'idModele' => $idModele,
                    'idType' => $idType,
                    'idProprietaire' => $idProprietaire,
                    'idCarburant' => $idCarburant,
                    'idDate' => $IdDisponibilite,
                    'immatriculation' => $immatriculation,
                    'climatisation' => $climatisation,
                    'nbPorte' => $nbPorte,
                    'nbPlace' => $nbPlace,
                    'description' => $description,
                    'prix' => $prix,
                    'boiteDeVitesse' => $boiteDeVitesse
                    ));
                } //End if
                
                $reponse->closeCursor();
            } //End else
            
        } //End ajoutVehicule()

        public static function modifierVehicule($idVehicule, $idMarque, $idModele, $idType, $idProprietaire, $idCarburant, $dateDebut, $dateFin, $immatriculation, $climatisation, $nbPorte, $nbPlace, $description, $prix, $boiteDeVitesse){
            global $bdd;
            //Ajustement du format des dates
            $dateDebut = date("Y-m-d", strtotime($dateDebut));
            $dateFin = date("Y-m-d", strtotime($dateFin));
            //Vérification de la conformité de la période
            if ($dateDebut >= $dateFin){
                echo "La date d'arrivée ne peut être supérieure à la date de départ !";
                return false;
            }
            else{  
                //Modification des dates de la base
                $idDate = Vehicule::returnId('idDate', 'Vehicule', 'idVehicule', $idVehicule);
                $reqIdDisponibilite = "UPDATE Disponibilite SET dateDebut=?, dateFin=? WHERE idDisponibilite=?";
                $reponse = $bdd->prepare($reqIdDisponibilite);
                $reponse->execute(array($dateDebut, $dateFin, $idDate));
                //$reponse->closeCursor();

                    $requete = 'UPDATE Vehicule SET idMarque=:idMarque, idModele=:idModele, idType=:idType, idProprietaire=:idProprietaire, idCarburant=:idCarburant, idDate=:idDate, immatriculation=:immatriculation, climatisation=:climatisation, nombreDePortes=:nombreDePortes, nombreDePlaces=:nombreDePlaces, description=:description, prix=:prix, boiteDeVitesse=:boiteDeVitesse WHERE idVehicule=:idVehicule';
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array(
                    'idVehicule' => $idVehicule,
                    'idMarque' => $idMarque,
                    'idModele' => $idModele,
                    'idType' => $idType,
                    'idProprietaire' => $idProprietaire,
                    'idCarburant' => $idCarburant,
                    'idDate' => $idDate,
                    'immatriculation' => $immatriculation,
                    'climatisation' => $climatisation,
                    'nombreDePortes' => $nbPorte,
                    'nombreDePlaces' => $nbPlace,
                    'description' => $description,
                    'prix' => $prix,
                    'boiteDeVitesse' => $boiteDeVitesse
            
                    ));
                
                $reponse->closeCursor();
            } //End else
            
        } //End modifierVehicule()


        public static function supprimerVehicule($id){
            global $bdd;
            $requete = 'DELETE FROM Vehicule WHERE idVehicule=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            $reponse->closeCursor();

        } //End supprimerVehicule($id)

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

    } //End Vehicule