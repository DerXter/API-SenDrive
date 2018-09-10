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
        public static function ajoutClient($nom, $prenom, $telephone, $adresse, $mail, $destination){
            global $bdd;
            $requete = 'INSERT INTO Clientele (nom, prenom, email, telephone, adresse, destination) VALUES(:nom, :prenom, :mail, :telephone, :adresse, :destination)';
            $reponse = $bdd->prepare($requete);
            $reponse->execute(array(
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'mail' => $mail,
                'destination' => $destination
            ));
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

        public static function afficheIndicatifs(){
            global $bdd;
            $requete = 'SELECT indicatif FROM Clientele';
            $reponse = $bdd->query($requete);
            if($clients = $reponse->fetchAll()){
                $clients = json_encode($clients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
                $reponse->closeCursor();
                return $clients;
            }
            else{
                echo "Aucun indicatif trouvé !";
                return false;
            }
        } //End afficheClients()

    } //End class Client