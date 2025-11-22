<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$project_id = $_GET['id'];

$query_project = "SELECT * FROM Projet WHERE id_projet='$project_id'";
$result_project = mysqli_query($conn, $query_project);
$project = mysqli_fetch_assoc($result_project);

$query_tasks = "SELECT * FROM Tache WHERE id_projet='$project_id'";
$result_tasks = mysqli_query($conn, $query_tasks);

$query_comments = "SELECT c.contenu, u.nom, u.prenom FROM Commentaire c JOIN Commentaire_Tache ct ON c.id_commentaire = ct.id_commentaire JOIN Utilisateur u ON c.id_utilisateur = u.id_utilisateur WHERE ct.id_tache IN (SELECT id_tache FROM Tache WHERE id_projet = '$project_id')";
$result_comments = mysqli_query($conn, $query_comments);

$query_documents = "SELECT d.nom, d.chemin_acces FROM Document d JOIN Document_Projet dp ON d.id_document = dp.id_document WHERE dp.id_projet = '$project_id'";
$result_documents = mysqli_query($conn, $query_documents);

$query_budget = "SELECT b.id_budget, b.montant, b.date_allocation, b.etat FROM Budget b WHERE b.id_projet='$project_id' LIMIT 1";
$result_budget = mysqli_query($conn, $query_budget);
$budget = mysqli_fetch_assoc($result_budget);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['titre']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">CAPTECH</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if ($role == 'developpeur'): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard_developpeur.php">Tableau de Bord</a></li>
                <?php elseif ($role == 'chef_de_projet'): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard_chef_de_projet.php">Tableau de Bord</a></li>
                <?php elseif ($role == 'responsable_equipe'): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard_responsable_equipe.php">Tableau de Bord</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center"><?php echo htmlspecialchars($project['titre']); ?></h2>
        <p><?php echo htmlspecialchars($project['description']); ?></p>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <h3 class="mt-5">Tâches</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = mysqli_fetch_assoc($result_tasks)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['titre']); ?></td>
                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                        <td><?php echo htmlspecialchars($task['date_debut']); ?></td>
                        <td><?php echo htmlspecialchars($task['date_fin']); ?></td>
                        <td><?php echo htmlspecialchars($task['statut']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3 class="mt-5">Commentaires</h3>
        <ul class="list-group">
            <?php while ($comment = mysqli_fetch_assoc($result_comments)): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($comment['prenom'] . ' ' . $comment['nom']); ?>:</strong> <?php echo htmlspecialchars($comment['contenu']); ?>
                </li>
            <?php endwhile; ?>
        </ul>

        <h3 class="mt-5">Documents</h3>
        <ul class="list-group">
            <?php while ($document = mysqli_fetch_assoc($result_documents)): ?>
                <li class="list-group-item">
                    <a href="<?php echo htmlspecialchars($document['chemin_acces']); ?>" target="_blank"><?php echo htmlspecialchars($document['nom']); ?></a>
                </li>
            <?php endwhile; ?>
        </ul>

        <?php if ($role == 'responsable_equipe'): ?>
            <h3 class="mt-5">Budget</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Montant</th>
                        <th>Date d'Allocation</th>
                        <th>État</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($budget['montant']); ?></td>
                        <td><?php echo htmlspecialchars($budget['date_allocation']); ?></td>
                        <td><?php echo htmlspecialchars($budget['etat']); ?></td>
                        <td>
                            <?php if ($budget['etat'] != 'Validé'): ?>
                                <a href="validate_budget.php?id=<?php echo $budget['id_budget']; ?>&project_id=<?php echo $project_id; ?>" class="btn btn-success">Valider</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($role == 'chef_de_projet'): ?>
            <h3 class="mt-5">Actions de Gestion</h3>
            <a href="edit_project.php?id=<?php echo $project_id; ?>" class="btn btn-warning">Modifier le Projet</a>
            <a href="delete_project.php?id_projet=<?php echo $project_id; ?>" class="btn btn-danger">Supprimer le Projet</a>
        <?php endif; ?>
    </div>
</body>
</html>
