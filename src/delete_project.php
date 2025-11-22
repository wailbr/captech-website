<?php
// Include the database connection file
include('config.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the id_projet parameter is set in the URL
if (isset($_GET['id_projet'])) {
    // Get the project ID from the URL
    $id_projet = $_GET['id_projet'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete related comments first
        $sql_delete_comments = "
            DELETE Commentaire 
            FROM Commentaire
            JOIN Commentaire_Tache ON Commentaire.id_commentaire = Commentaire_Tache.id_commentaire
            JOIN Tache ON Commentaire_Tache.id_tache = Tache.id_tache
            WHERE Tache.id_projet = ?";
        $stmt = $conn->prepare($sql_delete_comments);
        $stmt->bind_param("i", $id_projet);
        $stmt->execute();
        $stmt->close();

        // Delete related tasks
        $sql_delete_tasks = "DELETE FROM Tache WHERE id_projet = ?";
        $stmt = $conn->prepare($sql_delete_tasks);
        $stmt->bind_param("i", $id_projet);
        $stmt->execute();
        $stmt->close();

        // Delete related documents
        $sql_delete_documents = "
            DELETE Document 
            FROM Document
            JOIN Document_Projet ON Document.id_document = Document_Projet.id_document
            WHERE Document_Projet.id_projet = ?";
        $stmt = $conn->prepare($sql_delete_documents);
        $stmt->bind_param("i", $id_projet);
        $stmt->execute();
        $stmt->close();

        // Delete from the linking table for documents
        $sql_delete_document_links = "DELETE FROM Document_Projet WHERE id_projet = ?";
        $stmt = $conn->prepare($sql_delete_document_links);
        $stmt->bind_param("i", $id_projet);
        $stmt->execute();
        $stmt->close();

        // Delete from the linking table for team members
        $sql_delete_members = "
            DELETE Membre 
            FROM Membre
            JOIN Equipe ON Membre.id_equipe = Equipe.id_equipe
            WHERE Equipe.id_projet = ?";
        $stmt = $conn->prepare($sql_delete_members);
        $stmt->bind_param("i", $id_projet);
        $stmt->execute();
        $stmt->close();

        // Delete related teams
        $sql_delete_teams = "DELETE FROM Equipe WHERE id_projet = ?";
        $stmt = $conn->prepare($sql_delete_teams);
        $stmt->bind_param("i", $id_projet);
        $stmt->execute();
        $stmt->close();

        // Delete the project itself
        $sql_delete_project = "DELETE FROM Projet WHERE id_projet = ?";
        $stmt = $conn->prepare($sql_delete_project);
        $stmt->bind_param("i", $id_projet);
        $stmt->execute();

        // Check if a row was deleted
        if ($stmt->affected_rows > 0) {
            // Commit the transaction
            $conn->commit();
            // Redirect back to the dashboard with a success message
            header("Location: dashboard_chef_de_projet.php?message=Projet supprimé avec succès");
        } else {
            // Rollback the transaction
            $conn->rollback();
            // Redirect back to the dashboard with an error message
            header("Location: dashboard_chef_de_projet.php?message=Erreur: Projet non trouvé");
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Rollback the transaction
        $conn->rollback();
        // Redirect back to the dashboard with an error message
        header("Location: dashboard_chef_de_projet.php?message=Erreur lors de la suppression du projet: " . $e->getMessage());
    }
} else {
    // Redirect back to the dashboard with an error message
    header("Location: dashboard_chef_de_projet.php?message=ID de projet non spécifié");
}

// Close the database connection
$conn->close();
?>
