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
            $requete = 'SELECT proprietaire.*, raisonSociale from proprietaire, raisonsociale where proprietaire.idRaisonSociale = raisonSociale.idRaisonSociale;';
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
            //Vérification de l'unicité du proprietaire ajouté
            if(Proprietaire::verifDoublons('numeroIdentite', 'Proprietaire', $numIdentite)){
                echo "Numéro d'identité déjà utilisé !";
                return false;
            }
            else{
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
                //Vérification de la réussite de l'ajout
                if($reponse->rowCount() > 0){
                    echo "OK. Partenaire ajouté !";
                } 
                else{
                    echo "Une erreur est survenue lors de l'ajout du partenaire !";
                    return false;
                }
                $reponse->closeCursor();
                } //End else (raisonSocial)
            } //End else if(verifDoublons)
        } //End ajoutProprio()

        public static function modifProprio($idProprietaire, $raisonSociale, $proprietaire, $dateNaissance, $numIdentite, $telephone, $adresse, $email){
            global $bdd;
            $dateNaissance = date("Y-m-d", strtotime($dateNaissance));
            //Vérification de l'unicité du proprietaire ajouté
           // if(Proprietaire::verifDoublons('numeroIdentite', 'Proprietaire', $numIdentite)){
           //     echo "Numéro d'identité déjà utilisé !";
           //     return false;
           // }
           // else{
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
                //Vérification de la réussite de la mise à jour du propriétaire (qui est le partenaire)
                if($reponse->rowCount() > 0){
                    echo "OK. Partenaire mis à jour !";
                } 
                else{
                    echo "Une erreur est survenue lors de la mise à jour du partenaire !";
                    return false;
                }
                
                $reponse->closeCursor();

                } //End else (raisonSocial)
            //} //End else if(verifDoublons)
        } //End modifProprio

        public static function supprimerProprio($id){
            global $bdd;
            $requete = 'DELETE FROM Proprietaire WHERE idProprietaire=?';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array($id));
            //Vérification de la réussite de la suppréssion
            if($reponse->rowCount() > 0){
                echo "OK. Partenaire supprimé !";
            } 
            else{
                echo "Une erreur est survenue lors de la suppréssion du partenaire !";
                return false;
            }
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

        public static function verifDoublons($donnee, $table, $valeur){
            global $bdd;
            $result=false; //Flag me permettant de savoir s'il y'a un doublon ou pas
            $requete = "SELECT $donnee FROM $table";
            $reponse = $bdd->query($requete);
            while($data = $reponse->fetch()){
                if($valeur==$data[$donnee]){
                    $result = true;
                    break;
                } //End if
            } //End while ()
            $reponse->closeCursor();
            return $result==true ? true : false; //Retourne true s'il y'a un doublon et false dans le cas contraire
        } //End verifDoublons()

    } //End class Proprietaire