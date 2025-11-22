<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'developpeur') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$query_user = "SELECT * FROM Utilisateur WHERE id_utilisateur='$user_id'";
$result_user = mysqli_query($conn, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Récupérer les équipes et projets de l'utilisateur
$query_teams = "
    SELECT e.nom AS equipe_nom, p.titre AS projet_titre, p.id_projet
    FROM Equipe e 
    JOIN Projet p ON e.id_projet = p.id_projet 
    JOIN Membre m ON e.id_equipe = m.id_equipe 
    WHERE m.id_utilisateur='$user_id'";
$result_teams = mysqli_query($conn, $query_teams);

// Récupérer les tâches de l'utilisateur
$query_tasks = "SELECT * FROM Tache WHERE id_utilisateur='$user_id'";
$result_tasks = mysqli_query($conn, $query_tasks);

// Récupérer les tâches créées par l'utilisateur
$query_user_tasks = "SELECT t.*, p.titre AS projet_titre FROM Tache t JOIN Projet p ON t.id_projet = p.id_projet WHERE t.id_utilisateur='$user_id'";
$result_user_tasks = mysqli_query($conn, $query_user_tasks);

// Gérer le dépôt de documents
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document']) && isset($_POST['upload_document'])) {
    $document = $_FILES['document'];
    $document_name = basename($document['name']);
    $target_dir = "uploads/";
    $target_file = $target_dir . $document_name;
   
    if (move_uploaded_file($document['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO Document (nom, chemin_acces) VALUES (?, ?)");
        $stmt->bind_param("ss", $document_name, $target_file);
        $stmt->execute();
       
        $document_id = $conn->insert_id;
        $id_projet = $_POST['id_projet'];
       
        $stmt = $conn->prepare("INSERT INTO Document_Projet (id_projet, id_document) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_projet, $document_id);
        $stmt->execute();
       
        $upload_success = "Le document a été téléchargé avec succès.";
    } else {
        $upload_error = "Erreur lors du téléchargement du fichier.";
    }
}

// Gérer l'ajout de commentaires à un projet
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment_project'])) {
    $commentaire = $_POST['commentaire'];
    $id_projet = $_POST['id_projet'];
   
    $stmt = $conn->prepare("INSERT INTO Commentaire (contenu, id_utilisateur) VALUES (?, ?)");
    $stmt->bind_param("si", $commentaire, $user_id);
    $stmt->execute();
   
    $commentaire_id = $conn->insert_id;
   
    $stmt = $conn->prepare("INSERT INTO Commentaire_Projet (id_projet, id_commentaire) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_projet, $commentaire_id);
    $stmt->execute();
   
    // Rafraîchir la page après l'ajout du commentaire
    header("Location: dashboard_developpeur.php");
    exit();
}

// Gérer l'ajout de commentaires à une tâche
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment_task'])) {
    $commentaire = $_POST['commentaire'];
    $id_tache = $_POST['id_tache'];
   
    $stmt = $conn->prepare("INSERT INTO Commentaire (contenu, id_utilisateur) VALUES (?, ?)");
    $stmt->bind_param("si", $commentaire, $user_id);
    $stmt->execute();
   
    $commentaire_id = $conn->insert_id;
   
    $stmt = $conn->prepare("INSERT INTO Commentaire_Tache (id_tache, id_commentaire) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_tache, $commentaire_id);
    $stmt->execute();
   
    // Rafraîchir la page après l'ajout du commentaire
    header("Location: dashboard_developpeur.php");
    exit();
}

// Mettre à jour l'état d'une tâche
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_task_status'])) {
    $new_status = $_POST['new_status'];
    $id_tache = $_POST['id_tache'];
   
    $stmt = $conn->prepare("UPDATE Tache SET statut=? WHERE id_tache=?");
    $stmt->bind_param("si", $new_status, $id_tache);
    $stmt->execute();
   
    // Rafraîchir la page après la mise à jour du statut de la tâche
    header("Location: dashboard_developpeur.php");
    exit();
}

// Récupérer les documents de projet
$query_documents = "
    SELECT d.nom, d.chemin_acces, p.titre AS projet_titre
    FROM Document d
    JOIN Document_Projet dp ON d.id_document = dp.id_document
    JOIN Projet p ON dp.id_projet = p.id_projet
    JOIN Equipe e ON p.id_projet = e.id_projet
    JOIN Membre m ON e.id_equipe = m.id_equipe
    WHERE m.id_utilisateur='$user_id'";
$result_documents = mysqli_query($conn, $query_documents);

// Compter les tâches pour le graphique
$count_non_commence = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM Tache WHERE id_utilisateur='$user_id' AND statut='Non commencé'"))['count'];
$count_en_cours = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM Tache WHERE id_utilisateur='$user_id' AND statut='En cours'"))['count'];
$count_termine = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM Tache WHERE id_utilisateur='$user_id' AND statut='Terminé'"))['count'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Développeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #taskProgressChart {
            max-width: 400px;
            max-height: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="index.php">CAPTECH</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Bonjour, <?php echo isset($user) ? htmlspecialchars($user['prenom'] . ' ' . $user['nom']) : 'Utilisateur'; ?></h2>
       
        <h3 class="mt-5">Vos Équipes</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Nom de l'Équipe</th>
                        <th>Projet Associé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($team = mysqli_fetch_assoc($result_teams)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($team['equipe_nom']); ?></td>
                            <td><?php echo htmlspecialchars($team['projet_titre']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h3 class="mt-5">Documents de Projet</h3>
        <?php if (isset($upload_success)) echo '<div class="alert alert-success">' . htmlspecialchars($upload_success) . '</div>'; ?>
        <?php if (isset($upload_error)) echo '<div class="alert alert-danger">' . htmlspecialchars($upload_error) . '</div>'; ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Nom du Document</th>
                        <th>Projet Associé</th>
                        <th>Télécharger</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($document = mysqli_fetch_assoc($result_documents)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($document['nom']); ?></td>
                            <td><?php echo htmlspecialchars($document['projet_titre']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($document['chemin_acces']); ?>" class="btn btn-primary" download>Télécharger</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h3 class="mt-5">Vos Tâches</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Titre de la Tâche</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = mysqli_fetch_assoc($result_tasks)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['titre']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td>
                                <form method="post" action="dashboard_developpeur.php">
                                    <input type="hidden" name="id_tache" value="<?php echo $task['id_tache']; ?>">
                                    <select name="new_status" class="form-select" onchange="this.form.submit()">
                                        <option value="Non commencé" <?php echo ($task['statut'] == 'Non commencé') ? 'selected' : ''; ?>>Non commencé</option>
                                        <option value="En cours" <?php echo ($task['statut'] == 'En cours') ? 'selected' : ''; ?>>En cours</option>
                                        <option value="Terminé" <?php echo ($task['statut'] == 'Terminé') ? 'selected' : ''; ?>>Terminé</option>
                                    </select>
                                    <input type="hidden" name="update_task_status" value="1">
                                </form>
                            </td>
                            <td>
                                <form method="post" action="dashboard_developpeur.php">
                                    <input type="hidden" name="id_tache" value="<?php echo $task['id_tache']; ?>">
                                    <textarea name="commentaire" placeholder="Ajouter un commentaire" class="form-control"></textarea>
                                    <button type="submit" name="add_comment_task" class="btn btn-primary mt-2">Ajouter Commentaire</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h3 class="mt-5">Tâches Ajoutées par Vous</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Titre de la Tâche</th>
                        <th>Projet Associé</th>
                        <th>Description</th>
                        <th>Date de Début</th>
                        <th>Date de Fin</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = mysqli_fetch_assoc($result_user_tasks)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['titre']); ?></td>
                            <td><?php echo htmlspecialchars($task['projet_titre']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td><?php echo htmlspecialchars($task['date_debut']); ?></td>
                            <td><?php echo htmlspecialchars($task['date_fin']); ?></td>
                            <td><?php echo htmlspecialchars($task['statut']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <canvas id="taskProgressChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('taskProgressChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Non commencé', 'En cours', 'Terminé'],
                datasets: [{
                    label: 'Progression des Tâches',
                    data: [<?php echo $count_non_commence; ?>, <?php echo $count_en_cours; ?>, <?php echo $count_termine; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>
</html>
