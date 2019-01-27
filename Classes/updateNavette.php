<?php
    include_once('connexion.php');
    $cpSucces = 0; //Nombre de mise à jour réussie
    $cpEchec = 0; //Nombre de mise à jour échouée
    //Récupération de la date du jour
    $dateDuJour = date('Y-m-d');
    //Récupération de l'heure qu'il fait
    $localtime = localtime();
    $heure = $localtime[2];
    $minute = $localtime[1];
    $seconde = $localtime[0];
    $heureActuelle = $heure.':'.$minute.':'.$seconde;

    $heuresFinNavette = "SELECT DISTINCT idNavette, heureFin, date FROM Horaire h, Navette n WHERE n.idHoraire=h.idHoraire AND statut='En cours'";
    $rapport="";

    global $bdd;
    $reponse = $bdd->query($heuresFinNavette);
    while($navette=$reponse->fetch()){
        if(($dateDuJour==$navette['date'] && $heureActuelle>=$navette['heureFin']) || $dateDuJour>$navette['date']){
            $updateNavette = "UPDATE Navette SET statut='Terminé' WHERE idNavette=?";
            $rep = $bdd->prepare($updateNavette);
            $rep->execute(array($navette['idNavette']));
            if($rep->rowCount() > 0){
                $rapport .= "Navette no". $navette['idNavette']." mise à jour !\r\n";
                $cpSucces++;
            }
            else{
                $rapport .= "****************Navette no". $navette['idNavette']." non mise à jour !****************\r\n";
                $cpEchec++;
            }
            $rep->closeCursor();
        } //End if
    } //End while
    $reponse->closeCursor();
    $rapport .= "\r\nMise à jour Terminée ! \r\n";
    $rapport .= "\r\nMise à jour réussie: ".$cpSucces."\r\n";
    $rapport .= "Mise à jour échouée: ".$cpEchec."\r\n";


    // ***************************** Génération et envoie du mail *****************************
    // Destinataire
    $to = "mbayederguene97@gmail.com";
    // Sujet
    $subject = 'Rapport de mise à jour des navettes: Tâche Cron';
    
    // Message
    $message = "
    <html>
    <head>
        <title>Test Cron</title>
    </head>
    <body>
        <p>
            <h1> Rapport de mise à jour des navettes : </h1> <br />
            $rapport
        </p>
    </body>
    </html>
    ";
    
    // Pour envoyer un mail HTML, l en-tête Content-type doit être défini
    $headers = "MIME-Version: 1.0" . "\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    
    // En-têtes additionnels
    $headers .= 'From: Mail de la plateforme Sen\'Drive <no-reply@sendrive.com>' . "\r\n";
    
    // Envoie
    $resultat = mail($to, $subject, $message, $headers);
