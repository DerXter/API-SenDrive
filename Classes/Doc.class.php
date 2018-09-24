<?php
    class Doc{
        //Attributs
        private $idDocument;
        private $chemin;
        private $titre;
        private $nature;

        //Méthodes
        public static function afficheDoc($nature){
            global $bdd;
            $nature_autorises = array('contrat', 'fiche', 'processus', 'facture', 'gestion');
            if(in_array($nature, $nature_autorises)){
                $requete = "SELECT idDocument, titre, cheminDocument FROM Documentation WHERE nature=?";
                $reponse = $bdd->prepare($requete);
                $reponse->execute(array($nature));
                //Vérification de l'éxistence des documents demandés
                if($doc = $reponse->fetchAll() ){
                    $doc = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //Conversion du format du tableau en JSON
                    return $doc;
                }
                else{
                    echo "Aucun fichier de $nature trouvé !";
                    return false;
                }
            } //End if(array)
            else{
                echo 'La nature spécifiée est inexistante !';
                return false;
            }
            
            $reponse->closeCursor();
        } //End afficheDoc()


        public static function supprimerDoc($id){
            $cheminFichier = Doc::returnData('cheminDocument', 'documentation', 'idDocument', $id);
            //Vérification de l'éxistence du fichier
            if(file_exists($cheminFichier) ){
                //Suppréssion du fichier dont le chemin relatif est spécifié
                if(unlink($cheminFichier)){
                    //Si la suppréssion a réussie
                    global $bdd;
                    $requete = "DELETE FROM documentation WHERE idDocument=?";
                    $reponse = $bdd->prepare($requete);
                    $reponse->execute(array($id));
                    if($reponse->rowCount() > 0){
                        echo 'Fichier supprimé.';
                        return true;
                    }
                    else{
                        echo 'Erreur lors de la suppréssion du ficher de la base de données.';
                        return false;
                    }
                } //End if(unlink)
                else{
                    echo 'Une erreur est survenue lors de la suppréssion. Droit de suppréssion éventuellement inexistant.';
                    return false;
                } //End else (unlink)
                
            } //End if (File_Exist)
            else{
                echo 'Le fichier spécifié n\'existe pas.';
                return false;
            }
            $reponse->closeCursor();
        } //End supprimerDoc()

        public static function returnData($nomData, $table, $attribut, $valeur){
            global $bdd;
            $requete = "SELECT $nomData FROM $table WHERE $attribut='$valeur'";
            $reponse = $bdd->query($requete);
            if ($data = $reponse->fetch()){
                $id = $data[$nomData];
                $reponse->closeCursor();
                return $id;
            }
            else{
                echo "$table choisi(e) non disponible !";
                return false;
            }
        } //End returnData()

    } //End Doc