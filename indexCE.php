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
  <title>CE-index</title>
</head>
<body style=" background-image: url('images/bg-personnel.jpg'); background-repeat: no-repeat; background-size: 100%;">
  <div ></div>
  <div style="position: absolute; top: 0px; left: 0px; background-color: rgba(0,0,0,0.6); width: 100%; height: 100%; z-index: 1;">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">SOLUTION-CE</a>
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
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Salles d'examens
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">Salle 1</a>
              <a class="dropdown-item" href="#">Salle 2</a>
              <a class="dropdown-item" href="#">Salle 3</a>
              <a class="dropdown-item" href="#">Salle 4</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Nouvelle salle d'examen</a>
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
        <a class="nav-link" id="v-pills-ressource-tab" data-toggle="pill" href="#v-pills-ressource" role="tab" aria-controls="v-pills-ressource" aria-selected="false">Ressource</a>
              <div class="dropdown-divider"></div>
        <a class="nav-link" id="v-pills-candidat-tab" data-toggle="pill" href="#v-pills-candidat" role="tab" aria-controls="v-pills-candidat" aria-selected="false">Candidat</a>
        <a class="nav-link" id="v-pills-personnel-tab" data-toggle="pill" href="#v-pills-personnel" role="tab" aria-controls="v-pills-personnel" aria-selected="false"> Le Personnel</a>
              <div class="dropdown-divider"></div>
        <a class="nav-link" id="v-pills-correction-tab" data-toggle="pill" href="#v-pills-correction" role="tab" aria-controls="v-pills-correction" aria-selected="false">Correction</a>
        <div class="dropdown-divider"></div>
        <a class="nav-link" id="v-pills-pv-tab" data-toggle="pill" href="#v-pills-pv" role="tab" aria-controls="v-pills-pv" aria-selected="false">Procces verbaux</a>
      </div>
      <div class="tab-content" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab"><?php include('ce_home.php') ?></div>
        <div class="tab-pane fade" id="v-pills-ressource" role="tabpanel" aria-labelledby="v-pills-ressource-tab"><?php include('ce_ressource.php') ?></div>
        <div class="tab-pane fade" id="v-pills-candidat" role="tabpanel" aria-labelledby="v-pills-candidat-tab"><?php include('ce_candidat.php') ?></div>
        <div class="tab-pane fade" id="v-pills-personnel" role="tabpanel" aria-labelledby="v-pills-personnel-tab"><?php include('ce_personnel.php') ?></div>
        <div class="tab-pane fade" id="v-pills-correction" role="tabpanel" aria-labelledby="v-pills-correction-tab"><?php include('pv_notes.php') ?></div>
        <div class="tab-pane fade" id="v-pills-pv" role="tabpanel" aria-labelledby="v-pills-pv-tab"><?php include('ce_personnel.php') ?></div>
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
