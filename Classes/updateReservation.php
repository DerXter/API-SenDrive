<?php
    include_once('connexion.php');
    $cpSucces = 0; //Nombre de mise à jour réussie
    $cpEchec = 0; //Nombre de mise à jour échouée
    $dateDuJour = date('Y-m-d');
    $datesFinReservation = "SELECT DISTINCT idReservation, dateFin FROM Disponibilite, Reservation WHERE idDate=idDisponibilite AND statut='En cours'";
    $rapport="";

    global $bdd;
    $reponse = $bdd->query($datesFinReservation);
    echo "Mise à jour des réservations en cours .";
    while($reservation=$reponse->fetch()){
        echo ".";
        if($dateDuJour>=$reservation['dateFin']){
            $updateReservation = "UPDATE Reservation SET statut='Terminé' WHERE idReservation=?";
            $rep = $bdd->prepare($updateReservation);
            $rep->execute(array($reservation['idReservation']));
            if($rep->rowCount() > 0){
                $rapport .= "\nRéservation no". $reservation['idReservation']." mise à jour !\r\n";
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
    $rapport .= "Mise à jour Terminée ! <br />";
    $rapport .= "Mise à jour réussie: ".$cpSucces."<br />";
    $rapport .= "Mise à jour échouée: ".$cpEchec."<br />";
    echo $rapport;
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
    if($resultat)
        echo "Rapport de la mise à jour envoyé à ".$to."\n";
    else
        echo "Erreur dans l'envoi du rapport\n";
