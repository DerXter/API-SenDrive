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
            $vehicules = array(); //Tableau qui va contenir les véhicules diponibles entre les deux dates spécifiées
            $reqRecupIdDisponibilite = "SELECT idDisponibilite FROM Disponibilite WHERE dateDebut<=? AND dateFin>=?";
            $reponse = $bdd->prepare($reqRecupIdDisponibilite);
            $reponse->execute(array($dateDebut, $dateFin));
            $dataId=$reponse->fetchAll(); //Tableau contenant tous les 'idDisponibilité' correspondants aux dates spécifiées
            
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
        } //End afficheVehicules

        public static function afficheVehicules(){
            global $bdd;
            $reqAfficheVehicule = "SELECT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND idDate=idDisponibilite";
            $reponse = $bdd->query($reqAfficheVehicule);
            
            $vehicules = $reponse->fetchAll();
            $reponse->closeCursor();
            //Conversion du format du tableau en JSON
            $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
            return $vehicules;
        } //End afficheVehicules()

        public static function filtreVehicule($marque, $modele, $type, $energie, $climatisation){
            global $bdd;
            //Récupèration des id des données indiquées
            $idType = Vehicule::returnId('idTypeVehicule', 'TypeVehicule', 'typeVehicule', $type);
            $idCarburant = Vehicule::returnId('idCarburant', 'Carburant', 'carburant', $energie);
            $idMarque = Vehicule::returnId('idMarque', 'Marque', 'marque', $marque);
            $idModele = Vehicule::returnId('idModele', 'Modele', 'modele', $modele);
            //Mise  en place de la requête en fonction des données
            if(empty($marque) || $marque==""){
                $reqFiltreVehicule = "SELECT DISTINCT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND v.idModele=:idModele AND idType=:idType AND v.idCarburant=:idCarburant AND climatisation=:climatisation";
                $reponse = $bdd->prepare($reqFiltreVehicule);
                $reponse->execute(array(
                'idModele' => $idModele,
                'idType' => $idType,
                'idCarburant' => $idCarburant,
                'climatisation' => $climatisation
                ));
            }//End if $marque
            elseif(empty($modele) || $modele=""){
                $reqFiltreVehicule = "SELECT DISTINCT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND v.idMarque=:idMarque AND idType=:idType AND v.idCarburant=:idCarburant AND climatisation=:climatisation";
                $reponse = $bdd->prepare($reqFiltreVehicule);
                $reponse->execute(array(
                'idMarque' => $idMarque,
                'idType' => $idType,
                'idCarburant' => $idCarburant,
                'climatisation' => $climatisation
                ));
            } //End $modele
            elseif(empty($type) || $type=""){
                $reqFiltreVehicule = "SELECT DISTINCT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND v.idMarque=:idMarque AND v.idModele=:idModele AND v.idCarburant=:idCarburant AND climatisation=:climatisation";
                $reponse = $bdd->prepare($reqFiltreVehicule);
                $reponse->execute(array(
                'idMarque' => $idMarque,
                'idModele' => $idModele,
                'idCarburant' => $idCarburant,
                'climatisation' => $climatisation
                ));
            } //End $type
            elseif(empty($energie) || $energie=""){
                $reqFiltreVehicule = "SELECT DISTINCT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND v.idMarque=:idMarque AND v.idModele=:idModele AND idType=:idType AND climatisation=:climatisation";
                $reponse = $bdd->prepare($reqFiltreVehicule);
                $reponse->execute(array(
                'idMarque' => $idMarque,
                'idModele' => $idModele,
                'idType' => $idType,
                'climatisation' => $climatisation
                ));
            } //End $energie
            elseif(empty($climatisation) || $climatisation=""){
                $reqFiltreVehicule = "SELECT DISTINCT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND v.idMarque=:idMarque AND v.idModele=:idModele AND idType=:idType AND v.idCarburant=:idCarburant";
                $reponse = $bdd->prepare($reqFiltreVehicule);
                $reponse->execute(array(
                'idMarque' => $idMarque,
                'idModele' => $idModele,
                'idType' => $idType,
                'idCarburant' => $idCarburant
                ));
            } //End $climatisation

            else{
                $reqFiltreVehicule = "SELECT DISTINCT marque, modele, typeVehicule, prix, immatriculation, carburant, boiteDeVitesse, nombreDePortes, nombreDePlaces, climatisation, proprietaire, DATE_FORMAT(dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(dateFin, '%d/%m/%Y') AS dateFin, cheminPhoto FROM Marque ma, Modele mo, TypeVehicule ty, Carburant ca, Proprietaire p, Vehicule v, Disponibilite WHERE v.idMarque=ma.idMarque AND v.idModele=mo.idModele AND idType=idTypeVehicule AND v.idCarburant=ca.idCarburant AND v.idProprietaire=p.idProprietaire AND v.idDate=idDisponibilite AND v.idMarque=:idMarque AND v.idModele=:idModele AND idType=:idType AND v.idCarburant=:idCarburant AND climatisation=:climatisation";
                $reponse = $bdd->prepare($reqFiltreVehicule);
                $reponse->execute(array(
                'idMarque' => $idMarque,
                'idModele' => $idModele,
                'idType' => $idType,
                'idCarburant' => $idCarburant,
                'climatisation' => $climatisation
                ));
            } //End else
            $vehicules = $reponse->fetchAll();
            $vehicules = json_encode($vehicules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
            $reponse->closeCursor();

            return $vehicules;
        } //End filtreVehicule()

        public static function returnId($nomID, $table, $attribut, $valeur){
            global $bdd;
            $requete = "SELECT $nomID FROM $table WHERE $attribut='$valeur'";
            $reponse = $bdd->query($requete);
            $data = $reponse->fetch();
            $id = $data[$nomID];
            $reponse->closeCursor();
            return $id;
        } //End returnId()

    } //End Vehicule