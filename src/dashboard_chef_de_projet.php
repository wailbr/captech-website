<?php
session_start();
include('config.php');

// Vérification de la session utilisateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'chef_de_projet') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération des informations de l'utilisateur connecté
$query = "SELECT * FROM Utilisateur WHERE id_utilisateur='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Récupération de tous les projets
$query_projects = "SELECT * FROM Projet";
$result_projects = mysqli_query($conn, $query_projects);

// Générer les données pour le graphique
$project_progress = [];
while ($project = mysqli_fetch_assoc($result_projects)) {
    // Recalculer les données pour l'affichage
    $project_progress[] = [
        'titre' => $project['titre'],
        'avancement' => (rand(50, 100)), // Supposons un pourcentage aléatoire pour l'exemple
    ];
}
// Remettre le pointeur au début des résultats pour réutiliser les projets
mysqli_data_seek($result_projects, 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Chef de Projet</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="index.php">CAPTECH</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Bonjour, <?php echo $user['prenom']; ?> <?php echo $user['nom']; ?></h2>

        <!-- Affichage des messages -->
        <?php
        if (isset($_GET['message'])) {
            echo "<div class='alert alert-info'>" . htmlspecialchars($_GET['message']) . "</div>";
        }
        ?>

        <!-- Section de recherche et filtrage -->
        <div class="mb-4">
            <input type="text" id="search" class="form-control" placeholder="Rechercher des projets...">
        </div>

        <h3 class="mt-5">Tous les Projets</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Budget</th>
                    <th>Membres</th>
                    <th>Tâches</th>
                    <th>Commentaires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="projectTable">
                <?php while ($project = mysqli_fetch_assoc($result_projects)): ?>
                    <?php
                        $project_id = $project['id_projet'];
                        $query_members = "SELECT U.nom, U.prenom FROM Utilisateur U JOIN Membre M ON U.id_utilisateur = M.id_utilisateur WHERE M.id_equipe IN (SELECT id_equipe FROM Equipe WHERE id_projet='$project_id')";
                        $result_members = mysqli_query($conn, $query_members);
                        
                        $query_tasks = "SELECT id_tache, titre, statut FROM Tache WHERE id_projet='$project_id'";
                        $result_tasks = mysqli_query($conn, $query_tasks);
                    ?>
                    <tr>
                        <td><?php echo $project['titre']; ?></td>
                        <td><?php echo $project['description']; ?></td>
                        <td><?php echo $project['date_de_debut']; ?></td>
                        <td><?php echo $project['date_de_fin_prevue']; ?></td>
                        <td><?php echo $project['budget']; ?></td>
                        <td>
                            <ul>
                                <?php while ($member = mysqli_fetch_assoc($result_members)): ?>
                                    <li><?php echo $member['prenom'] . ' ' . $member['nom']; ?></li>
                                <?php endwhile; ?>
                            </ul>
                        </td>
                        <td>
                            <ul>
                                <?php while ($task = mysqli_fetch_assoc($result_tasks)): ?>
                                    <li><?php echo $task['titre'] . ' (' . $task['statut'] . ')'; ?></li>
                                <?php endwhile; ?>
                            </ul>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm voir-commentaires" data-id-projet="<?php echo $project['id_projet']; ?>">Voir Commentaires</button>
                            <ul class="commentaires-liste" id="commentaires-<?php echo $project['id_projet']; ?>"></ul>
                        </td>
                        <td>
                            <a href="edit_project.php?id=<?php echo $project['id_projet']; ?>" class="btn btn-warning">Modifier</a>
                            <a href="delete_project.php?id_projet=<?php echo $project['id_projet']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- Bouton pour créer un nouveau projet -->
        <div class="text-center mt-4">
            <a href="create_project.php" class="btn btn-success">Créer un Nouveau Projet</a>
        </div>

        <!-- Section des statistiques -->
        <div class="mt-5">
            <h3>État d'Avancement des Projets</h3>
            <canvas id="projectChart"></canvas>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            // Fonctionnalité de recherche
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#projectTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Générer les données pour le graphique d'avancement
            var projectProgress = <?php echo json_encode($project_progress); ?>;
            var labels = projectProgress.map(function(proj) { return proj.titre; });
            var data = projectProgress.map(function(proj) { return proj.avancement; });

            // Graphique d'avancement des projets
            var ctx = document.getElementById('projectChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar', // Change the chart type to 'bar'
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Avancement (%)',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Projets'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Avancement (%)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Charger les commentaires des projets
            $(".voir-commentaires").on("click", function() {
                var idProjet = $(this).data("id-projet");
                var commentairesListe = $("#commentaires-" + idProjet);
                
                if (commentairesListe.is(":empty")) {
                    $.get("comment_project.php", { id_projet: idProjet }, function(data) {
                        var commentaires = JSON.parse(data);
                        commentaires.forEach(function(commentaire) {
                            commentairesListe.append("<li>" + commentaire.contenu + " (Par " + commentaire.prenom + " " + commentaire.nom + ")</li>");
                        });
                    });
                }
                commentairesListe.toggle();
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
