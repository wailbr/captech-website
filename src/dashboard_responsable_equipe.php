<?php
session_start();
include('config.php');

// Rediriger si l'utilisateur n'est pas connecté ou s'il n'est pas un responsable d'équipe
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'responsable_equipe') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Récupération des informations de l'utilisateur
$query_user = $conn->prepare("SELECT * FROM Utilisateur WHERE id_utilisateur = ?");
$query_user->bind_param('i', $user_id);
$query_user->execute();
$result_user = $query_user->get_result();
$user = $result_user->fetch_assoc();

// Récupération des projets gérés par le responsable d'équipe
$query_projects = $conn->prepare("SELECT * FROM Projet WHERE id_utilisateur = ? OR id_projet IN (SELECT id_projet FROM Equipe WHERE id_equipe IN (SELECT id_equipe FROM Membre WHERE id_utilisateur = ?))");
$query_projects->bind_param('ii', $user_id, $user_id);
$query_projects->execute();
$result_projects = $query_projects->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Responsable d'Équipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 30px;
        }
        .card {
            margin-bottom: 30px;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .btn-primary, .btn-success, .btn-warning, .btn-danger {
            margin-bottom: 5px;
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
        <h2 class="text-center">Bonjour, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h2>

        <!-- Search and Filter Section -->
        <div class="mb-4">
            <input type="text" id="search" class="form-control" placeholder="Rechercher des projets...">
        </div>

        <!-- Projets Section -->
        <div class="card">
            <div class="card-header">
                <h3>Vos Projets</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Date de Début</th>
                                <th>Date Fin Prévue</th>
                                <th>Budget</th>
                                <th>Membres</th>
                                <th>Tâches</th>
                            </tr>
                        </thead>
                        <tbody id="projectTable">
                            <?php while ($project = $result_projects->fetch_assoc()): ?>
                                <?php
                                    $project_id = $project['id_projet'];
                                    $query_members = $conn->prepare("SELECT U.nom, U.prenom FROM Utilisateur U JOIN Membre M ON U.id_utilisateur = M.id_utilisateur WHERE M.id_equipe IN (SELECT id_equipe FROM Equipe WHERE id_projet=?)");
                                    $query_members->bind_param('i', $project_id);
                                    $query_members->execute();
                                    $result_members = $query_members->get_result();
                                   
                                    $query_tasks = $conn->prepare("SELECT titre, statut FROM Tache WHERE id_projet=?");
                                    $query_tasks->bind_param('i', $project_id);
                                    $query_tasks->execute();
                                    $result_tasks_individual = $query_tasks->get_result();
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($project['titre']); ?></td>
                                    <td><?php echo htmlspecialchars($project['description']); ?></td>
                                    <td><?php echo htmlspecialchars($project['date_de_debut']); ?></td>
                                    <td><?php echo htmlspecialchars($project['date_de_fin_prevue']); ?></td>
                                    <td><?php echo htmlspecialchars($project['budget']); ?></td>
                                    <td>
                                        <ul>
                                            <?php while ($member = $result_members->fetch_assoc()): ?>
                                                <li><?php echo htmlspecialchars($member['prenom'] . ' ' . $member['nom']); ?></li>
                                            <?php endwhile; ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <ul>
                                            <?php while ($task = $result_tasks_individual->fetch_assoc()): ?>
                                                <li><?php echo htmlspecialchars($task['titre'] . ' (' . $task['statut'] . ')'); ?></li>
                                            <?php endwhile; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tâches Section -->
        <div class="card">
            <div class="card-header">
                <h3>Vos Tâches</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Date de Début</th>
                                <th>Date de Fin</th>
                                <th>Statut</th>
                                <th>Commentaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="taskTable">
                            <?php
                            $query_tasks = $conn->prepare("SELECT t.*, p.titre AS projet_titre FROM Tache t JOIN Projet p ON t.id_projet = p.id_projet WHERE p.id_utilisateur = ? OR p.id_projet IN (SELECT id_projet FROM Equipe WHERE id_equipe IN (SELECT id_equipe FROM Membre WHERE id_utilisateur = ?))");
                            $query_tasks->bind_param('ii', $user_id, $user_id);
                            $query_tasks->execute();
                            $result_tasks = $query_tasks->get_result();
                            while ($task = $result_tasks->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['titre']); ?></td>
                                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                                    <td><?php echo htmlspecialchars($task['date_debut']); ?></td>
                                    <td><?php echo htmlspecialchars($task['date_fin']); ?></td>
                                    <td><?php echo htmlspecialchars($task['statut']); ?></td>
                                    <td>
                                        <form method="POST" action="comment_task.php">
                                            <input type="hidden" name="id_tache" value="<?php echo $task['id_tache']; ?>">
                                            <input type="text" name="commentaire" class="form-control" placeholder="Ajouter un commentaire" required>
                                            <button type="submit" class="btn btn-primary mt-2">Commenter</button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="task.php?id=<?php echo $task['id_tache']; ?>" class="btn btn-primary">Voir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Budgets Section -->
        <div class="card">
            <div class="card-header">
                <h3>Budgets des Projets</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th>Montant</th>
                                <th>Date d'Allocation</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="budgetTable">
                            <?php
                            $query_budgets = $conn->prepare("SELECT b.*, p.titre AS projet_titre FROM Budget b JOIN Projet p ON b.id_projet = p.id_projet WHERE p.id_utilisateur = ? OR p.id_projet IN (SELECT id_projet FROM Equipe WHERE id_equipe IN (SELECT id_equipe FROM Membre WHERE id_utilisateur = ?))");
                            $query_budgets->bind_param('ii', $user_id, $user_id);
                            $query_budgets->execute();
                            $result_budgets = $query_budgets->get_result();
                            while ($budget = $result_budgets->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($budget['projet_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($budget['montant']); ?></td>
                                    <td><?php echo htmlspecialchars($budget['date_allocation']); ?></td>
                                    <td><?php echo htmlspecialchars($budget['etat']); ?></td>
                                    <td>
                                        <?php if ($budget['etat'] == 'En attente'): ?>
                                            <a href="validate_budget.php?id=<?php echo $budget['id_budget']; ?>" class="btn btn-success">Valider</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="mt-5">
            <h3>Statistiques des Projets</h3>
            <canvas id="projectChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('search');
            searchInput.addEventListener('keyup', function() {
                const value = searchInput.value.toLowerCase();
                document.querySelectorAll('#projectTable tr, #taskTable tr, #budgetTable tr').forEach(function(row) {
                    row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
                });
            });

            // Chart.js functionality
            const ctx = document.getElementById('projectChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Projet A', 'Projet B', 'Projet C'], // Replace with dynamic data
                    datasets: [{
                        label: 'Budget',
                        data: [10000, 15000, 20000], // Replace with dynamic data
                        backgroundColor: ['#007bff', '#28a745', '#dc3545'],
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
