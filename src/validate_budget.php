<?php
session_start();
include('config.php');

// Rediriger si l'utilisateur n'est pas connecté ou s'il n'est pas un responsable d'équipe
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'responsable_equipe') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['project_id'])) {
    $budget_id = $_GET['id'];
    $project_id = $_GET['project_id'];

    // Mettre à jour l'état du budget à "Validé"
    $query = $conn->prepare("UPDATE Budget SET etat = 'Validé' WHERE id_budget = ?");
    $query->bind_param('i', $budget_id);
    $query->execute();

    // Rediriger vers la page du projet avec un message de succès
    header("Location: project.php?id=" . $project_id . "&message=Votre projet a bien été validé");
    exit();
} else {
    // Rediriger vers le tableau de bord si aucun ID de budget ou project_id n'est fourni
    header("Location: dashboard_responsable_equipe.php");
    exit();
}
?>
