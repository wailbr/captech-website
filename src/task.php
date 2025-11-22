<?php
session_start();
include('config.php');

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_GET['id'];

// Fetch task details
$query_task = $conn->prepare("SELECT * FROM Tache WHERE id_tache = ?");
$query_task->bind_param('i', $task_id);
$query_task->execute();
$result_task = $query_task->get_result();
$task = $result_task->fetch_assoc();

// Fetch comments associated with the task
$query_comments = $conn->prepare("SELECT C.*, U.prenom, U.nom FROM Commentaire C JOIN Utilisateur U ON C.id_utilisateur = U.id_utilisateur JOIN Commentaire_Tache CT ON C.id_commentaire = CT.id_commentaire WHERE CT.id_tache = ? ORDER BY C.date_commentaire DESC");
$query_comments->bind_param('i', $task_id);
$query_comments->execute();
$result_comments = $query_comments->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Tâche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">CAPTECH</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard_responsable_equipe.php">Tableau de Bord</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Détails de la Tâche</h2>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3><?php echo htmlspecialchars($task['titre']); ?></h3>
            </div>
            <div class="card-body">
                <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
                <p><strong>Date de Début:</strong> <?php echo htmlspecialchars($task['date_debut']); ?></p>
                <p><strong>Date de Fin:</strong> <?php echo htmlspecialchars($task['date_fin']); ?></p>
                <p><strong>Statut:</strong> <?php echo htmlspecialchars($task['statut']); ?></p>
            </div>
        </div>

        <h3>Commentaires</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul class="list-group">
                    <?php while ($comment = $result_comments->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <p><strong><?php echo htmlspecialchars($comment['prenom'] . ' ' . $comment['nom']); ?>:</strong> <?php echo htmlspecialchars($comment['contenu']); ?></p>
                            <p class="text-muted"><small><?php echo htmlspecialchars($comment['date_commentaire']); ?></small></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <h3>Ajouter un Commentaire</h3>
        <form method="POST" action="comment_task.php">
            <input type="hidden" name="id_tache" value="<?php echo $task['id_tache']; ?>">
            <div class="form-group">
                <textarea name="commentaire" class="form-control" placeholder="Ajouter un commentaire" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Commenter</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
