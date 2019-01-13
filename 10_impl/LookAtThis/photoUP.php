<?php include 'includes/header.php';?>
<?php include 'includes/mysqli_connect.php';?>
<?php
if (isset($_POST['submit'])) {
 if (isset($_SESSION['email'])) { //if the user is registered
  if (count($_FILES['upload']['name']) > 0) {
   $podatkiZaVnos = array();

   $idgalerije = 10;
   if(strcmp($_POST['gallerySelectType'], "1")==0) { //dropdown
     $idgalerije = $_POST['selectedGallery'] * 1;
   } else { //new gallery
       //pridobivanje idja prijavljenega uporabnika
   		$emailUserLoggedIn = $_SESSION['email'];
   		$queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
   		$resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
   		$vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
   		$idUserLoggedIn = $vrsticaUserLoggedIn[0];

   		//pridobivanje podatkov iz obrazca in shranjevanje v spremelnjivke
   		$name = $_POST['name'];
   		$description = $_POST['description'];

   		//vnos podatkov v PB (SQL) in hkrati belezenje idja idgalerije
      $stmt = "INSERT INTO galleries(name,description,users_users_id) VALUES ('$name', '$description', $idUserLoggedIn)";
      if ($db->query($stmt) === TRUE) {
      $last_id = $db->insert_id;
      $idgalerije = $last_id;
    } else {
      echo "Error: " . $sql . "<br>" . $db->error;
    }
   }

   //če gre za galerijo z idjem 10 (javna), insertaš kot normalno vendar še hkrati narediš dostop

   for ($i = 0; $i < count($_FILES['upload']['name']); $i++) {
    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
    if ($tmpFilePath != "") {
     $shortname = $_FILES['upload']['name'][$i];
     if (strlen($shortname) > 45) {
      echo '<script language="javascript">';
      echo 'alert("Name of photo cannot be longer than 45 characters! Upload aborted")';
      echo '</script>';
      break;
     }

     $name = $_FILES['upload']['name'][$i];
     $ext = end((explode(".", $name)));
     $filePath = "uploads/" . date('d-m-Y-H-i-s') . "-" . $shortname . "." . $ext;
     move_uploaded_file($tmpFilePath, $filePath);
     $podatkiZaVnos[$i] = '("' . $shortname . '", "no description", "' . $filePath . '", 0, ' . $idgalerije . ')'; //idgalerije in master of the narekovaji!!!
    }
   }

   //redirectas na sebe kjer iz nastavljenih prebers in pol ko submitas clearas to spremenljivko, da ni skos prikazana tabela?
   $IDjiPravkarNalozenihSlik = array();

   if($idgalerije==10) {
     //pridobivanje idja prijavljenega uporabnika
     $emailUserLoggedIn = $_SESSION['email'];
     $queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
     $resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
     $vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
     $idUserLoggedIn = $vrsticaUserLoggedIn[0];

     for($j=0; $j<count($podatkiZaVnos); $j++) {
       $stmt = "INSERT INTO photos (name,description,photo_path,is_private,galleries_galleries_id) VALUES" . $podatkiZaVnos[$j];
       if ($db->query($stmt) === TRUE) {
         $last_id_slike = $db->insert_id;
         $idSlike = $last_id_slike;
         $IDjiPravkarNalozenihSlik[] = $idSlike;
         $stmtAccess = "INSERT INTO user_has_access_to_photos (users_users_id,photos_photos_id,user_with_access_id) VALUES ($idUserLoggedIn, $idSlike, $idUserLoggedIn)";
         $db->query($stmtAccess);
       } else {
         echo "Error: " . $sql . "<br>" . $db->error;
       }
     }
   } else { //ne gre za javno
     for($j=0; $j<count($podatkiZaVnos); $j++) {
       $stmt = "INSERT INTO photos (name,description,photo_path,is_private,galleries_galleries_id) VALUES" . $podatkiZaVnos[$j];
       if ($db->query($stmt) === TRUE) {
         $last_id_slike = $db->insert_id;
         $idSlike = $last_id_slike;
         $IDjiPravkarNalozenihSlik[] = $idSlike;
       } else {
         echo "Error: " . $db . "<br>" . $db->error;
       }
     }
   }
   $_SESSION['IDjiPravkarNalozenihSlik'] = $IDjiPravkarNalozenihSlik;
   //$samSebe = $_SERVER['PHP_SELF'];
   //header("Location: $samSebe");
  }
 }
 else { //user is not registered
  $numOfCookies = count($_COOKIE);
  $stevecCookijev = $numOfCookies;
  if(count($_FILES['upload']['name']) + $numOfCookies <= 11) {
    $podatkiZaVnos = array();
    for ($i = 0; $i < count($_FILES['upload']['name']); $i++) {
     $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
     if ($tmpFilePath != "") {
      $shortname = $_FILES['upload']['name'][$i];
      if (strlen($shortname) > 45) {
       echo '<script language="javascript">';
       echo 'alert("Name of photo cannot be longer than 45 characters! Upload aborted")';
       echo '</script>';
       break;
      }
      $name = $_FILES['upload']['name'][$i];
      $ext = end((explode(".", $name)));
      $filePath = "uploads/" . date('d-m-Y-H-i-s') . "-" . $shortname . "." . $ext;
      move_uploaded_file($tmpFilePath, $filePath);
      $podatkiZaVnos[$i] = '("' . $shortname . '", "sssii", "' . $filePath . '", 0, 10)';
     }
     $cookie_name = "photoNum-" . $stevecCookijev;
     setcookie($cookie_name, $stevecCookijev, time() +2592000); //30 days for every photo, 10 is the maximum ammount for 30 days, eacho photo has own counter
     $stevecCookijev++;
    }
    $stmt = "INSERT INTO photos (name,description,photo_path,is_private,galleries_galleries_id) VALUES" . implode(',', $podatkiZaVnos);
    $db->query($stmt);
    header("Refresh:0");
  } else { //too many photos
     $returnMessage = "Unregistered users have a limit of uploading only 10 photos (new ones can be uploaded after 30 days - from the day of the last upload).";
     echo '<script language="javascript">';
     echo "alert('$returnMessage')";
     echo '</script>';
    }
   }
 }

if(isset($_POST['submitPhotoData'])) {
  if(isset($_SESSION['email'])) {
    $emailUserLoggedIn = $_SESSION['email'];
		$queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
		$resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
		$vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
		$idUserLoggedIn = $vrsticaUserLoggedIn[0];
    $imenaSlik = pretvoriVPolje($_POST['imenaSlik']);
    $opisiSlik = pretvoriVPolje($_POST['opisiSlik']);
    $kBesedeSlik = pretvoriVPolje($_POST['kBesedeSlik']);
    $IDJIzaNaprej = $_SESSION['IDJIzaNaprej'];
    unset($_SESSION['IDJIzaNaprej']);
    for($i=0; $i<count($IDJIzaNaprej); $i++) {
      $stmtUpdate = "UPDATE photos SET name='$imenaSlik[$i]', description='$opisiSlik[$i]' WHERE photos_id=$IDJIzaNaprej[$i]";
      $db->query($stmtUpdate);
      $myString = $kBesedeSlik[$i];
      $myArray = explode(',', $myString);
      for($j=0; $j<count($myArray); $j++) {
        $stmtVnosKeywordov = "INSERT INTO keywords(title,photos_photos_id,users_users_id) VALUES ('$myArray[$j]', $IDJIzaNaprej[$i], $idUserLoggedIn)";
        $db->query($stmtVnosKeywordov);
      }
    }
    $message = "Upload succeded! Redirecting to galleries page...";
    echo "<script type='text/javascript'>alert('$message'); location='gallery.php';</script>";
  }
}
?>

<?php
$numOfCookies = count($_COOKIE);
$numOfLeft = 11 - $numOfCookies;
?>
  <div class="container">
    <div class="row">
      <div class="box">
        <div class="col-lg-12">
          <h2><small>UPLOAD PHOTOS</small></h2>
          <?php if(!isset($_SESSION['email'])) { ?>
          <h3><small>Unregistered users can only upload up to 10 photos. You still have: <?php echo $numOfLeft; ?></small></h3>
          <?php } ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post">
            <input id='upload' name="upload[]" type="file" multiple="multiple" required/>
            <?php if(isset($_SESSION['email'])) { ?>
              <input id="rdb1" type="radio" name="gallerySelectType" value="1" required> Choose a gallery from the list<br>
                <?php
                $emailUserLoggedIn = $_SESSION['email'];
              	$queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
              	$resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
              	$vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
              	$idUserLoggedIn = $vrsticaUserLoggedIn[0];

              	$queryJavna = "SELECT * FROM galleries WHERE galleries_id = 10";
              	$resultJavna = mysqli_query($db, $queryJavna);

              	$query = "SELECT * FROM galleries WHERE users_users_id = $idUserLoggedIn";
              	$result = mysqli_query($db, $query);

                if ((!$result)||(!$resultJavna)) {
              		die ("Can't access DB!");
              	}
              	else {
                  echo '<div id="blk-1" class="toHide" style="display:none">';
                  echo '<select name="selectedGallery" style="width: 50%" class="form-control">';
                  $vrsticaJavna = mysqli_fetch_row($resultJavna);
                  echo "<option value='$vrsticaJavna[0]'>$vrsticaJavna[1]</option>";
              		$st_vrstic = mysqli_num_rows($result);
              		if($st_vrstic > 0) {
              			$vrednostURL = $_SERVER['PHP_SELF'];
              			for ($j = 0 ; $j < $st_vrstic ; ++$j) {
              				$vrstica = mysqli_fetch_row($result);
              				echo "<option value='$vrstica[0]'>$vrstica[1]</option>";
              			}
              		}
                  echo '</select></div>';
              	}
                ?>
              <input id="rdb2" type="radio" name="gallerySelectType" value="2"> Place in new gallery<br>
                <div id="blk-2" class="toHide" style="display:none">
                  Name:&nbsp;<input type="text" name="name" maxlength="45">
                  <br />
                  Description:&nbsp;<input type="text" name="description" maxlength="280">
                </div>
            <?php } ?>
            <br /><input type="submit" class="btn btn-success" name="submit" value="Do it!">
          </form>
        </div>
      </div>
    </div>

    <?php
    if (isset($_SESSION['email'])) { //forma za dopolnitev informacij o slikah
      if(isset($_SESSION['IDjiPravkarNalozenihSlik'])) {
        $IDjiPravkarNalozenihSlik = $_SESSION['IDjiPravkarNalozenihSlik'];
        unset($_SESSION['IDjiPravkarNalozenihSlik']);
        echo '<div class="row">';
        echo '<div class="box">';
        echo '<div class="col-lg-12">';
        echo '<h2><small>FILL OUT PHOTO DATA</small></h2>';
        echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
        echo '<table class="table">';
        echo '<tr>';
        echo '<th>Preview</th>';
        echo '<th>Name</th>';
        echo '<th>Description</th>';
        echo '<th>Keywords (separate by comma!)</th>';
        echo '</tr>';
        for($i=0; $i<count($IDjiPravkarNalozenihSlik); $i++) { //vrstice
          $queryVnesenaSlika = "SELECT * FROM photos WHERE photos_id=$IDjiPravkarNalozenihSlik[$i]";
       		$resultVnesenaSlika = mysqli_query($db, $queryVnesenaSlika);
       		$vrsticaVnesenaSlika = mysqli_fetch_row($resultVnesenaSlika);
          echo '<tr>';
          for($j=0; $j<4; $j++) { //is private še ne diraj
            if($j==0)
              echo '<td><a href="' . $vrsticaVnesenaSlika[3] . '" target="_blank"><img width="200px" src="' . $vrsticaVnesenaSlika[3] . '" alt="photo #' . $vrsticaVnesenaSlika[0] . ' preview"></a></td>';
            else if($j==1)
              echo '<td><input type="text" name="imenaSlik[]" value="' . $vrsticaVnesenaSlika[1] . '"></td>'; //ni zahtevano
            else if($j==2)
              echo '<td><input type="text" name="opisiSlik[]" value="' . $vrsticaVnesenaSlika[2] . '"></td>'; //ni zahtevano
            else if($j==3)
              echo '<td><input type="text" name="kBesedeSlik[]" required></td>'; //zahtevano; tu bi lahko naredo da ti mogoc prkaze kaj je nt, sam pri vnosu naceloma nebi smelo bit še
          }
          echo '</tr>';
        }
        echo '</table>';
        echo '<input type="submit" name="submitPhotoData" class="btn btn-success" value="Do it!">';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        $_SESSION['IDJIzaNaprej'] = $IDjiPravkarNalozenihSlik;
      }
    }
    ?>
  </div>

<script>
$(function() {
    $("[name=gallerySelectType]").click(function(){
            $('.toHide').hide();
            $("#blk-"+$(this).val()).show('slow');
    });
 });
</script>

<?php
function pretvoriVPolje($input) {
    $output = array();
    for($i=0; $i<count($input); $i++)
      $output[] = $input[$i];
    return $output;
}
?>

<?php include 'includes/footer.php';?>
