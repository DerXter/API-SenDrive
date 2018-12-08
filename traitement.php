<?php
/*FRONT - Define access control values */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
    //Vérification de la reception des paramètres attendus
    if (isset($_GET['nature']) AND isset($_GET['cible']) ){
        //Sécurisation des données reçues
        $nature = htmlspecialchars($_GET['nature']);
        $cible = htmlspecialchars($_GET['cible']);
        if(isset($_GET['id'])){
            $id = htmlspecialchars($_GET['id']);
        }
        else{
            $id = '';
        }
        //Mise en place du chemin vers le repertoire où le fichier sera stocké
        if($nature=='photo'){
            //Mise en place des extensions autorisées
            $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
            switch ($cible){
                case 'utilisateur':
                    $table = 'Utilisateur';
                    $repertoire = 'Images/utilisateur/';
                break;
                case 'chauffeur':
                    $table = 'Chauffeur';
                    $repertoire = 'Images/chauffeur/';
                break;
                case 'vehicule':
                    $table = 'Vehicule';
                    $repertoire = 'Images/vehicule/';
                break;
                default :
                    echo 'Cette cible ne correspond pas à la nature choisie !';
                    return false;
            } //End switch

        } //End if(photo)
        else if ($nature=='doc'){
            //Mise en place des extensions autorisées
            $extensions_autorisees = array('pdf', 'docx');
            $table = 'Documentation';
            switch ($cible){
                case 'contrat':
                    $repertoire = 'documentation/contrat/';
                break;
                case 'fiche':
                    $repertoire = 'documentation/fiche/'; //Fiche Etat des Lieux
                break;
                case 'processus':
                    $repertoire = 'documentation/processus/';
                break;
                case 'facture':
                    $repertoire = 'documentation/facture/';
                break;
                case 'gestion':
                    $repertoire = 'documentation/gestion/';
                break;
                default :
                    echo 'Cette cible ne correspond pas à la nature choisie !';
                    return false;
            } //End switch
        } //End else if(doc)
        else{
            echo 'la nature spécifiée est incorrecte !';
            return false;
        }
    } //End if(isset)   
    else{
        echo "Veuillez spécifier la nature et/ou la cible du fichier envoyé !";
        return false;
    }

 //******************************Traitement de l'upload des chemins dans la base******************************
 include_once('Classes/connexion.php');
    //Fonction de mise à jour des chemins des fichiers dans la base de données
    function uploadPath($table, $chemin, $nature, $titre, $id, $cible){
        global $bdd;
        switch ($nature){
            case 'photo':
                if(!empty($id)){
                    $idName = 'id'.$table;
                    $reponse = $bdd->prepare("UPDATE $table SET cheminPhoto=? WHERE $idName=?");
                    $reponse->execute(array($chemin, $id));
                }
                else{
                    echo "Erreur lors de la mise à jour du chemin dans la base de données : id $table nécéssaire.";
                    return false;
                }
            break;
            case 'doc':
                $reponse = $bdd->prepare("INSERT INTO $table (titre, cheminDocument, nature) VALUES (?, ?, ?)");
                $reponse->execute(array($titre, $chemin, $cible));
            break;
            default :
                echo "Nature inconnue !";
                return false;
        } //End switch
       
        
        if($reponse->rowCount() > 0){
            echo "Chemin mis à jour : ";
            return true;
        }
        else{
            echo "Une erreur est survenue lors de la mise à jour du chemin !";
            return false;
        }
        $reponse->closeCursor();
    } //End uploadPath

    //******************************Traitement de l'upload fichiers dans le serveur******************************

    // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
    if (isset($_FILES['monfichier']) AND $_FILES['monfichier']['error'] == 0){
        // Testons si le fichier n'est pas trop gros (<= 5Mo)
        if ($_FILES['monfichier']['size'] <= 5000000){
            // Testons si l'extension est autorisée
            $infosfichier = pathinfo($_FILES['monfichier']['name']); //Récupération des infos du fichier
            $extension_upload = $infosfichier['extension']; //Récupération de l'extension
            
            if (in_array($extension_upload, $extensions_autorisees)){
                // On peut valider le fichier et le stocker définitivement
                $nomFichier = basename($_FILES['monfichier']['name']); //Récupération du nom du fichier dans le chemin absolu
                $cheminFichier = $repertoire . $nomFichier; //Mise en place du chemin vers le repertoire où le fichier sera stocké
                $etat = uploadPath($table, $cheminFichier, $nature, $nomFichier, $id, $cible); //Mise à jour du chemin du fichier dans la bdd
                if($etat == false){
                    echo ': Une erreur est survenue lors de l\'upload du fichier dans le serveur.';
                }
                else{
                   if( move_uploaded_file($_FILES['monfichier']['tmp_name'], $cheminFichier) ){ //Déplacement du fichier dans le répertoire adéquoit
                    echo "OK. L'envoi a bien été effectué !";
                    }
                    else{
                        echo ': Une erreur est survenue lors de l\'upload. Vérifier l\'éxistence du droit de suppression. ';
                    }
                } //End else if(etat)
                
                } //End if(in_array)
            else{
                echo "Extension non autorisée !";
                }
            } //End if($_FILES)
        else{
            echo "Fichier trop volumineux !";
        }
        } //End if(isset)
        else{
            echo "Aucun fichier reçu !";
        }