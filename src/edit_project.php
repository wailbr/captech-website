<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'chef_de_projet') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID de projet non défini ou invalide.";
    exit();
}

$project_id = $_GET['id'];

// Retrieve all users and teams for dropdowns
$user_query = "SELECT id_utilisateur, CONCAT(prenom, ' ', nom) AS full_name FROM Utilisateur";
$user_result = mysqli_query($conn, $user_query);

$team_query = "SELECT id_equipe, nom FROM Equipe";
$team_result = mysqli_query($conn, $team_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mise à jour du projet
    if (isset($_POST['update_project'])) {
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $date_de_debut = $_POST['date_de_debut'];
        $date_de_fin_prevue = $_POST['date_de_fin_prevue'];
        $budget = $_POST['budget'];

        $query = "UPDATE Projet SET titre='$titre', description='$description', date_de_debut='$date_de_debut', date_de_fin_prevue='$date_de_fin_prevue', budget='$budget' WHERE id_projet='$project_id'";
        if (mysqli_query($conn, $query)) {
            // Suppression des anciennes tâches et membres si nécessaire (optionnel)
            // Ajout des nouvelles tâches
            if (!empty($_POST['task_title'])) {
                foreach ($_POST['task_title'] as $index => $task_title) {
                    $task_description = $_POST['task_description'][$index];
                    $task_start_date = $_POST['task_start_date'][$index];
                    $task_end_date = $_POST['task_end_date'][$index];
                    $task_status = $_POST['task_status'][$index];
                    $task_user_id = $_POST['task_user_id'][$index];

                    $task_query = "INSERT INTO Tache (titre, description, date_debut, date_fin, statut, id_projet, id_utilisateur) VALUES ('$task_title', '$task_description', '$task_start_date', '$task_end_date', '$task_status', '$project_id', '$task_user_id')";
                    mysqli_query($conn, $task_query);
                }
            }

            // Suppression des membres existants
            $delete_members_query = "DELETE FROM Membre WHERE id_equipe IN (SELECT id_equipe FROM Equipe WHERE id_projet='$project_id')";
            mysqli_query($conn, $delete_members_query);

            // Ajout des nouveaux membres
            if (!empty($_POST['member_user_id'])) {
                foreach ($_POST['member_user_id'] as $index => $member_user_id) {
                    $member_team_id = $_POST['member_team_id'][$index];
                    $member_query = "INSERT INTO Membre (id_utilisateur, id_equipe) VALUES ('$member_user_id', '$member_team_id')";
                    mysqli_query($conn, $member_query);
                }
            }

            header("Location: dashboard_chef_de_projet.php");
            exit();
        } else {
            echo "Erreur : " . mysqli_error($conn);
        }
    }
}

$query = "SELECT * FROM Projet WHERE id_projet='$project_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Erreur de requête : " . mysqli_error($conn);
    exit();
}

$project = mysqli_fetch_assoc($result);

if (!$project) {
    echo "Projet non trouvé.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Projet</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function addMember() {
            var container = document.getElementById("members-container");
            var div = document.createElement("div");
            div.className = "member-entry form-row";
            div.innerHTML = `
                <div class="form-group col-md-5">
                    <label for="member_user_id[]">Nom de l'Utilisateur</label>
                    <select class="form-control" id="member_user_id[]" name="member_user_id[]" required>
                        <option value="">Sélectionner un utilisateur</option>
                        <?php
                        mysqli_data_seek($user_result, 0);
                        while ($user = mysqli_fetch_assoc($user_result)): ?>
                            <option value="<?php echo $user['id_utilisateur']; ?>"><?php echo $user['full_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label for="member_team_id[]">Nom de l'Équipe</label>
                    <select class="form-control" id="member_team_id[]" name="member_team_id[]" required>
                        <option value="">Sélectionner une équipe</option>
                        <?php
                        mysqli_data_seek($team_result, 0);
                        while ($team = mysqli_fetch_assoc($team_result)): ?>
                            <option value="<?php echo $team['id_equipe']; ?>"><?php echo $team['nom']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-2 align-self-end">
                    <button type="button" class="btn btn-danger" onclick="removeMember(this)">Supprimer</button>
                </div>
            `;
            container.appendChild(div);
        }

        function removeMember(button) {
            var container = document.getElementById("members-container");
            container.removeChild(button.parentElement.parentElement);
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier Projet</h2>
        <form method="POST">
            <input type="hidden" name="update_project" value="1">
            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $project['titre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo $project['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="date_de_debut">Date de Début</label>
                <input type="date" class="form-control" id="date_de_debut" name="date_de_debut" value="<?php echo $project['date_de_debut']; ?>" required>
            </div>
            <div class="form-group">
                <label for="date_de_fin_prevue">Date de Fin Prévue</label>
                <input type="date" class="form-control" id="date_de_fin_prevue" name="date_de_fin_prevue" value="<?php echo $project['date_de_fin_prevue']; ?>" required>
            </div>
            <div class="form-group">
                <label for="budget">Budget</label>
                <input type="number" class="form-control" id="budget" name="budget" value="<?php echo $project['budget']; ?>" required>
            </div>

            <h3 class="mt-5">Ajouter Tâches</h3>
            <div id="tasks-container">
                <div class="task-entry">
                    <div class="form-group">
                        <label for="task_title[]">Titre de la Tâche</label>
                        <input type="text" class="form-control" id="task_title[]" name="task_title[]" required>
                    </div>
                    <div class="form-group">
                        <label for="task_description[]">Description de la Tâche</label>
                        <textarea class="form-control" id="task_description[]" name="task_description[]" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="task_start_date[]">Date de Début</label>
                        <input type="date" class="form-control" id="task_start_date[]" name="task_start_date[]" required>
                    </div>
                    <div class="form-group">
                        <label for="task_end_date[]">Date de Fin</label>
                        <input type="date" class="form-control" id="task_end_date[]" name="task_end_date[]" required>
                    </div>
                    <div class="form-group">
                        <label for="task_status[]">Statut</label>
                        <select class="form-control" id="task_status[]" name="task_status[]" required>
                            <option value="En cours">En cours</option>
                            <option value="Non commencé">Non commencé</option>
                            <option value="Terminé">Terminé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="task_user_id[]">Utilisateur Assigné</label>
                        <select class="form-control" id="task_user_id[]" name="task_user_id[]" required>
                            <option value="">Sélectionner un utilisateur</option>
                            <?php
                            // Reset the result pointer and fetch users again for the tasks section
                            mysqli_data_seek($user_result, 0);
                            while ($user = mysqli_fetch_assoc($user_result)): ?>
                                <option value="<?php echo $user['id_utilisateur']; ?>"><?php echo $user['full_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <h3 class="mt-5">Ajouter Membres</h3>
            <div id="members-container">
                <div class="member-entry form-row">
                    <div class="form-group col-md-5">
                        <label for="member_user_id[]">Nom de l'Utilisateur</label>
                        <select class="form-control" id="member_user_id[]" name="member_user_id[]" required>
                            <option value="">Sélectionner un utilisateur</option>
                            <?php
                            // Reset the result pointer and fetch users again for the members section
                            mysqli_data_seek($user_result, 0);
                            while ($user = mysqli_fetch_assoc($user_result)): ?>
                                <option value="<?php echo $user['id_utilisateur']; ?>"><?php echo $user['full_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="member_team_id[]">Nom de l'Équipe</label>
                        <select class="form-control" id="member_team_id[]" name="member_team_id[]" required>
                            <option value="">Sélectionner une équipe</option>
                            <?php
                            // Reset the result pointer and fetch teams again for the members section
                            mysqli_data_seek($team_result, 0);
                            while ($team = mysqli_fetch_assoc($team_result)): ?>
                                <option value="<?php echo $team['id_equipe']; ?>"><?php echo $team['nom']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2 align-self-end">
                        <button type="button" class="btn btn-danger" onclick="removeMember(this)">Supprimer</button>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="addMember()">Ajouter un membre</button>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Enregistrer les modifications</button>
        </form>
        <div class="text-center mt-4">
            <a href="dashboard_chef_de_projet.php" class="btn btn-primary">Retour au Tableau de Bord</a>
        </div>
    </div>
</body>
</html>
