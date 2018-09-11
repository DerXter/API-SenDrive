<?php
    class Proprietaire{
        //Attributs
        private $idProprietaire;
        private $idRaisonSociale;
        private $proprietaire;
        private $dateNaissance;
        private $numIdentite;
        private $telephone;
        private $adresse;
        private $email;

        //Fonctions
        public static function afficheProprio(){
            global $bdd;
            $requete = 'SELECT * FROM Proprietaire';
            $reponse = $bdd->query($requete);
            if($proprios=$reponse->fetchAll()){
                $proprios =  json_encode($proprios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $reponse->closeCursor();
                return $proprios;
            } //End if
            else{
                echo "Aucun propriétaire trouvé !";
                return false;
            }

        } //End affiche Proprio()

        public static function ajoutProprio($raisonSociale, $proprietaire, $dateNaissance, $numIdentite, $telephone, $adresse, $email){
            global $bdd;
            $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
            $idRaisonSociale = Proprietaire::returnId('idRaisonSociale', 'RaisonSociale', 'raisonSociale', $raisonSociale);
            if ($idRaisonSociale==false){
                return false;
            }
            else{
                $requete = 'INSERT INTO Proprietaire(idRaisonSociale, proprietaire, dateNaissance, numeroIdentite, adresse, telephone, email) VALUES(:idRaisonSociale, :proprietaire, :dateNaissance, :numIdentite, :telephone, :adresse, :email)';
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array(
                'idRaisonSociale' => $idRaisonSociale,
                'proprietaire' => $proprietaire,
                'dateNaissance' => $dateNaissance,
                'numIdentite' => $numIdentite,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'email' => $email
            ));
            
            $reponse->closeCursor();
            } //End else
        } //End ajoutProprio()

        public static function modifProprio($idProprietaire, $raisonSociale, $proprietaire, $dateNaissance, $numIdentite, $telephone, $adresse, $email){
            global $bdd;
            $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
            $idRaisonSociale = Proprietaire::returnId('idRaisonSociale', 'RaisonSociale', 'raisonSociale', $raisonSociale);
            if ($idRaisonSociale==false){
                return false;
            }
            else{
                $requete = 'UPDATE Proprietaire SET idRaisonSociale=:idRaisonSociale, proprietaire=:proprietaire, dateNaissance=:dateNaissance, numeroIdentite=:numIdentite, telephone=:telephone, adresse=:adresse, email=:email WHERE idProprietaire=:idProprietaire';
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array(
                'idRaisonSociale' => $idRaisonSociale,
                'proprietaire' => $proprietaire,
                'dateNaissance' => $dateNaissance,
                'numIdentite' => $numIdentite,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'email' => $email,
                'idProprietaire' => $idProprietaire
            ));
            
            $reponse->closeCursor();

            } //End else

        } //End modifProprio

        public static function supprimerProprio($id){
            global $bdd;
            $requete = 'DELETE FROM Proprietaire WHERE idProprietaire=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            $reponse->closeCursor();

        } //End supprimerProprio($id)

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

    } //End class Proprietaire