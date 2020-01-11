<!DOCTYPE html>
<html>
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- awesone fonts css-->
  <link href="css/font-awesome.css" rel="stylesheet" type="text/css">
  <!-- owl carousel css-->
  <link rel="stylesheet" href="owl-carousel/assets/owl.carousel.min.css" type="text/css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- custom CSS -->
  <link rel="stylesheet" href="css/obc.css">
  <link rel="stylesheet" href="css/ce.css">
  <title>OBC-index</title>
</head>
<body style=" background-image: url('images/bg-personnel.jpg'); background-repeat: no-repeat; background-size: 100%;">
  <div ></div>
  <div style="position: absolute; top: 0px; left: 0px; background-color: rgba(0,0,0,0.6); width: 100%; height: 100%; z-index: 1;">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">SOLUTION-OBC</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Acceuil <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Profil</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Etablissemenet
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">Lycée Bilingue d'étoug-ébé</a>
              <a class="dropdown-item" href="#">Lycée de Ngoa-ékélé</a>
              <a class="dropdown-item" href="#">Lycée Leclairc</a>
              <a class="dropdown-item" href="#">Lycée de Mendong</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Ajouter un nouvel établissement</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Reglage</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>

    <div class="body-dashbord">
      <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical" class="vertical-nav">
        <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Home</a>
        <a class="nav-link" id="v-pills-newExam-tab" data-toggle="pill" href="#v-pills-newExam" role="tab" aria-controls="v-pills-newExam" aria-selected="false">Nouvel examen</a>
              <div class="dropdown-divider"></div>
        <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-add" aria-selected="false">Ajouter une actualié</a>
        <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-bibliothèque" aria-selected="false">Bibliothèques</a>
              <div class="dropdown-divider"></div>
        <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-answer" aria-selected="false">Resultats de l'examen</a>
        <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-police" aria-selected="false">Police de l'examen</a>
        <a class="nav-link" id="v-pills-epreuves-tab" data-toggle="pill" href="#v-pills-epreuves" role="tab" aria-controls="v-pills-police" aria-selected="false">Epreuves</a>
              <div class="dropdown-divider"></div>
        <a class="nav-link" id="v-pills-epreuves-tab" data-toggle="pill" href="#v-pills-pv" role="tab" aria-controls="v-pills-pv" aria-selected="false">Procces verbaux</a>
      </div>
      <div class="tab-content" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab"><?php include('obc_home.php') ?></div>
        <div class="tab-pane fade" id="v-pills-newExam" role="tabpanel" aria-labelledby="v-pills-newExam-tab"></h1><br><br><br><br><section style="text-align: center; color: #fff;">Cette platforme vous permet de realiser et d'organiser les examens au cameroun en sessions. Vous pouvez donc <a href="">créer & definir une nouvelle session</a>ici en renseignant les informations relatives à la nouvelle session.</section></div>
        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab"><?php include('addNews.php') ?></div>
        <div class="tab-pane fade" id="v-pills-police" role="tabpanel" aria-labelledby="v-pills-police-tab">...</div>
        <div class="tab-pane fade" id="v-pills-epreuves" role="tabpanel" aria-labelledby="v-pills-epreuves-tab"><?php include('epreuve.php') ?></div>
      </div>
    </div>
  </div>

  <!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="js/jquery-3.3.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- owl carousel js-->
<script src="owl-carousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
