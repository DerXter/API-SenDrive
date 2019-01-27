<?php
    include_once('connexion.php');
    $cpSucces = 0; //Nombre de mise à jour réussie
    $cpEchec = 0; //Nombre de mise à jour échouée
    $dateDuJour = date('Y-m-d');
    $datesFinReservation = "SELECT DISTINCT idReservation, dateFin FROM Disponibilite, Reservation WHERE idDate=idDisponibilite AND statut='En cours'";

    global $bdd;
    $reponse = $bdd->query($datesFinReservation);
    while($reservation=$reponse->fetch()){
        if($dateDuJour>=$reservation['dateFin']){
            $updateReservation = "UPDATE Reservation SET statut='Terminé' WHERE idReservation=?";
            $rep = $bdd->prepare($updateReservation);
            $rep->execute(array($reservation['idReservation']));
            if($rep->rowCount() > 0){
                $rapport .= "Réservation no". $reservation['idReservation']." mise à jour !\r\n";
                $cpSucces++;
            }
            else{
                $rapport .= "****************Réservation no". $reservation['idReservation']." non mise à jour !****************\r\n";
                $cpEchec++;
            }
            $rep->closeCursor();
        } //End if
    } //End while
    $reponse->closeCursor();
    $rapport .= "\nMise à jour Terminée ! \n";
    $rapport .= "\r\nMise à jour réussie: ".$cpSucces."\r\n";
    $rapport .= "Mise à jour échouée: ".$cpEchec."\r\n";

    // ***************************** Génération et envoie du mail *****************************
    // Destinataire
    $to = "mbayederguene97@gmail.com";
    // Sujet
    $subject = 'Rapport de mise à jour des réservations: Tâche Cron';
    
    // Message
    $message = "
    <html>
    <head>
        <title>Test Cron</title>
    </head>
    <body>
        <p>
            <h1> Rapport de mise à jour des réservations : </h1> <br />
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
