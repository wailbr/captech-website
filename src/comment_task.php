<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id_tache'])) {
        $id_tache = $_GET['id_tache'];

        $query = $conn->prepare("
            SELECT c.contenu, u.prenom, u.nom 
            FROM Commentaire_Tache ct 
            JOIN Commentaire c ON ct.id_commentaire = c.id_commentaire 
            JOIN Utilisateur u ON c.id_utilisateur = u.id_utilisateur 
            WHERE ct.id_tache = ?");
        $query->bind_param('i', $id_tache);
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
