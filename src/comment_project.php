<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id_projet'])) {
        $id_projet = $_GET['id_projet'];

        $query = $conn->prepare("
            SELECT c.contenu, u.prenom, u.nom 
            FROM Commentaire_Tache ct 
            JOIN Commentaire c ON ct.id_commentaire = c.id_commentaire 
            JOIN Utilisateur u ON c.id_utilisateur = u.id_utilisateur 
            JOIN Tache t ON ct.id_tache = t.id_tache 
            WHERE t.id_projet = ?");
        $query->bind_param('i', $id_projet);
        $query->execute();
        $result = $query->get_result();

        $commentaires = [];
        while ($row = $result->fetch_assoc()) {
            $commentaires[] = $row;
        }
        echo json_encode($commentaires);
    } else {
        echo json_encode([]);
    }
}
?>
