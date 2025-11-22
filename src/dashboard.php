<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM Utilisateur WHERE id_utilisateur='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$query_projects = "SELECT * FROM Projet WHERE id_utilisateur='$user_id'";
$result_projects = mysqli_query($conn, $query_projects);

$query_teams = "SELECT e.nom, p.titre FROM Equipe e JOIN Projet p ON e.id_projet = p.id_projet JOIN Membre m ON e.id_equipe = m.id_equipe WHERE m.id_utilisateur='$user_id'";
$result_teams = mysqli_query($conn, $query_teams);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">CAPTECH</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Bonjour, <?php echo $user['prenom']; ?> <?php echo $user['nom']; ?></h2>
        <h3 class="mt-5">Vos Projets</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Budget</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($project = mysqli_fetch_assoc($result_projects)): ?>
                    <tr>
                        <td><?php echo $project['titre']; ?></td>
                        <td><?php echo $project['description']; ?></td>
                        <td><?php echo $project['date_de_debut']; ?></td>
                        <td><?php echo $project['date_fin_prevu']; ?></td>
                        <td><?php echo $project['budget']; ?></td>
                        <td><a href="project.php?id=<?php echo $project['id_projet']; ?>" class="btn btn-primary">Voir</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <h3 class="mt-5">Vos Équipes</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom de l'Équipe</th>
                    <th>Projet Associé</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($team = mysqli_fetch_assoc($result_teams)): ?>
                    <tr>
                        <td><?php echo $team['nom']; ?></td>
                        <td><?php echo $team['titre']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
