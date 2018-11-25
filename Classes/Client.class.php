<?php
    class Client{
        //Attributs
        private $_idClient;
        private $_nom;
        private $_prenom;
        private $_email;
        private $_telephone;
        private $_adresse;
        private $_destination;

        //Methodes
        public static function ajoutClient($nom, $prenom, $telephone, $adresse, $mail){
            global $bdd;
            $requete = 'INSERT INTO Clientele (nom, prenom, email, telephone, adresse) VALUES(:nom, :prenom, :mail, :telephone, :adresse)';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array(
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'mail' => $mail,
            ));
            //Vérification de la réussite de l'ajout
            if($reponse->rowCount() > 0){
                echo "OK. Client ajouté !";
            } 
            else{
                echo "Une erreur est survenue lors de l'ajout du client !";
                return false;
            }
            $reponse->closeCursor();
        } //End ajoutClient()

        public static function afficheClients(){
            global $bdd;
            $requete = 'SELECT * FROM Clientele';
            $reponse = $bdd->query($requete);
            if($clients = $reponse->fetchAll()){
                $clients = json_encode($clients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
                $reponse->closeCursor();
                return $clients;
            }
            else{
                echo "Aucun client trouvé !";
                return false;
            }
        } //End afficheClients()

    } //End class Client