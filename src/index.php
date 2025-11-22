<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/12471feb02.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Accueil - CAPTECH</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: rgb(13, 110, 253);
            background-color: #ffffff;
        }

        .navbar {
            background-color: rgb(13, 110, 253);
        }

        .navbar-brand,
        .nav-link {
            color: #ffffff;
        }

        .hero {
            background-color: #f8f9fa;
            padding: 80px 0;
            text-align: center;
        }

        .hero h1,
        .hero p {
            color: rgb(13, 110, 253);
        }

        .btn-primary {
            background-color: rgb(13, 110, 253);
            border: none;
        }

        .btn-primary:hover {
            background-color: rgb(10, 90, 210);
        }

        .features {
            padding: 60px 0;
        }

        .feature-box {
            background-color: #f1f1f1;
            padding: 30px;
            border-radius: 10px;
            transition: transform 0.3s;
            color: rgb(13, 110, 253);
        }

        .feature-box:hover {
            transform: scale(1.05);
        }

        .feature-icon {
            width: 160px;
            height: 160px;
            margin-bottom: 20px;
            border-radius: 7px;
        }

        .testimonials {
            padding: 60px 0;
        }

        .testimonial {
            background-color: #f1f1f1;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: rgb(13, 110, 253);
        }

        .footer {
            background-color: rgb(13, 110, 253);
            padding: 40px 0;
            text-align: center;
        }

        .footer h5 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: #cccccc;
        }

        .footer .social-icons a {
            color: #ffffff;
            margin: 0 10px;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .footer .social-icons a:hover {
            color: #cccccc;
        }

        .footer .footer-bottom {
            margin-top: 20px;
            color: #cccccc;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">CAPTECH</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white" href="login.php">Se connecter</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <h1>Bienvenue sur CAPTECH</h1>
            <p>Gérez vos projets efficacement et collaborez avec votre équipe en toute simplicité.</p>
            <a href="login.php" class="btn btn-primary">Commencer</a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features">
        <div class="container text-center">
            <h2 class="mb-5">Nos Fonctionnalités</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <img src="image/gestion.jpeg" alt="Gestion de Projet" class="feature-icon">
                        <h3>Gestion de Projet</h3>
                        <p>Suivez et gérez tous vos projets en un seul endroit avec des outils de planification et de suivi des tâches.</p>
                        <a href="#" class="btn btn-outline-primary mt-3">En savoir plus</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <img src="image/collab.jpeg" alt="Collaboration en Équipe" class="feature-icon">
                        <h3>Collaboration en Équipe</h3>
                        <p>Travaillez ensemble avec des outils de collaboration intégrés, des discussions en temps réel et le partage de fichiers.</p>
                        <a href="#" class="btn btn-outline-primary mt-3">En savoir plus</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <img src="image/rapport.png" alt="Rapports et Analyses" class="feature-icon">
                        <h3>Rapports et Analyses</h3>
                        <p>Obtenez des insights détaillés sur la progression de vos projets grâce à des rapports et des tableaux de bord intuitifs.</p>
                        <a href="#" class="btn btn-outline-primary mt-3">En savoir plus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="testimonials">
        <div class="container">
            <h2 class="text-center mb-5">Ce que disent nos clients</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial">
                        <p>"CAPTECH a transformé notre façon de gérer les projets. Une solution incontournable!"</p>
                        <p class="fw-bold mt-3">- Bouba, CEO</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial">
                        <p>"Une interface intuitive et des fonctionnalités puissantes. Je recommande fortement!"</p>
                        <p class="fw-bold mt-3">- Ousmane, Chef de Projet</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial">
                        <p>"La meilleure solution pour la collaboration en équipe. Nous avons gagné en efficacité."</p>
                        <p class="fw-bold mt-3">- Teddy, Directeur Technique</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>À propos de nous</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Notre histoire</a></li>
                        <li><a href="#">Équipe</a></li>
                        <li><a href="#">Carrières</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Support</a></li>
                        <li><a href="mailto:captech@gmail.com">captech@gmail.com</a></li>
                        <li><a href="tel:+33000000000">+33 000 00 00 00</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Suivez-nous</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CAPTECH. Tous droits réservés.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
