<!-- <!DOCTYPE html>
<html>
<head> -->
  <!-- Required meta tags -->
<!--   <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->

  <!-- awesone fonts css-->
  <!-- <link href="css/font-awesome.css" rel="stylesheet" type="text/css"> -->
  <!-- owl carousel css-->
  <!-- <link rel="stylesheet" href="owl-carousel/assets/owl.carousel.min.css" type="text/css"> -->
  <!-- Bootstrap CSS -->
  <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
  <!-- custom CSS -->
  <!-- <link rel="stylesheet" href="css/ce.css"> -->
 <!--  <title>Centre d'examen</title>
</head> -->
<section style=" background: url('images/bg-personnel.jpg'); background-repeat: no-repeat; background-size: 100%;" class="body">
  <div class="candidate container" style="padding: 0;">
    <div class="btn btn-secondary btn-lg btn-block" style="margin: 0 0 20 0; width: 100%; font-size: 30px;">Gestionnaire du personnel examinatoire</div>
    <div class="candidate_manager row ">
      <button class="add btn btn-success" style="margin-left: 30px;" onclick="document.getElementById('modal-wrapper-add').style.display='block'">Nouveau personnel</button>
      <button class="upd btn btn-info" onclick="var updateMatricule =prompt('Entrer le matricule du personnel à modifier ');">Modifier un personnel</button>
      <button class="del btn btn-danger" onclick="var deleteMatricule =prompt('Entrer le matricule du personnel à supprimer ');">Supprimer un personnel</button>
    </div>

     <div class="listCandidat">
      <table>
        <tr class="text-white bg-primary">
          <th>Matricule</th>
          <th>Nom</th>
          <th>Prenom</th>
          <th>Sexe</th>
          <th>Catégorie</th>
          <th>Spécialitté</th>
          <th>Region</th>
        </tr>
      </table>
    </div>
  </div>

<!-- add candidate -->
<div id="modal-wrapper-add" class="modal login_form">
  <form class="modal-content animate" action="#">
    <div class="imgcontainer">
      <span onclick="document.getElementById('modal-wrapper-add').style.display='none'" class="close" title="Clode login">&times;</span>
      <h1 style="text-align: center;">Nouveau personnel</h1>
    </div>

    <div class="container box">
      <div class="inputbox">
        <input type="text" required="" name="firstName">
        <label for="firstName">Nom du personnel</label>
      </div>
      <div class="inputbox">
        <input type="text" required="" name="lastName" min="8">
        <label for="lastName">Prenom du personnel</label>
      </div>

      <div class="row sexe">
        <span>Sexe           </span>
        <div class="col-lg-3">
          <div class="input-group inputbox">
            <input type="radio" required="" name="sexe">
            <label for="feminin">Masculin</label>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="input-group inputbox">
            <input type="radio" required="" name="sexe">
            <label for="masculin">Feminin</label>
          </div>
        </div>
      </div><br>

      <div class="inputbox">
        <input type="text" required="" name="categorie" min="8">
        <label for="categorie">Catégorie</label>
      </div>

      <div class="inputbox">
        <input type="text" required="" name="specialite" min="8">
        <label for="specialite">Spécialité</label>
      </div>

       <div class="inputbox">
        <input type="text" required="" name="region">
        <label for="region">Région</label>
      </div>

    </div>
    <button type="submit" class="btn btn-success btn-lg btn-block">Ajouter</button>
  </form>
</div>
<script type="text/javascript">
    var modal = document.getElementById('modal-wrapper-add');
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<!-- end add candidate -->


  <!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="js/jquery-3.3.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- owl carousel js-->
<script src="owl-carousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
</section>
<!-- </html> -->
