<?php include 'includes/header.php';?>
<?php include 'includes/mysqli_connect.php';?>
<?php
//preverimo, ce je uporabnik vnesel vse podatke v formo
if(isset($_SESSION['email'])) {
	if((isset($_POST['name'])) && (isset($_POST['description']))) {
		//pridobivanje idja prijavljenega uporabnika
		$emailUserLoggedIn = $_SESSION['email'];
		$queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
		$resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
		$vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
		$idUserLoggedIn = $vrsticaUserLoggedIn[0];

		//pridobivanje podatkov iz obrazca in shranjevanje v spremelnjivke
		$name = $_POST['name'];
		$description = $_POST['description'];

		//vnos podatkov v PB (SQL)
	  $stmt = "INSERT INTO galleries(name,description,users_users_id) VALUES ('$name', '$description', $idUserLoggedIn)";
	  $db->query($stmt);
	}
	else if((isset($_POST['updateGalleryID']))&&(isset($_POST['nameUpdate'])) && (isset($_POST['descriptionUpdate']))) {
		$newName = $_POST['nameUpdate'];
		$newDescription = $_POST['descriptionUpdate'];
		$theID = $_POST['updateGalleryID'];
		$stmtUpdate = "UPDATE galleries SET name='$newName', description='$newDescription' WHERE galleries_id=$theID";
		$db->query($stmtUpdate);
	}
}

if (isset($_GET['deleteGallery'])) {
	$idGalerije = $_GET['deleteGallery'];
	$stmtSlikePoIDJUGalerije = "SELECT * FROM photos WHERE galleries_galleries_id=$idGalerije";
	$resultSlikePoIDJUGalerije = mysqli_query($db, $stmtSlikePoIDJUGalerije);

	$st_vrstic = mysqli_num_rows($resultSlikePoIDJUGalerije);
	if($st_vrstic > 0) {
		for ($j = 0 ; $j < $st_vrstic ; $j++) {
			$vrsticaSlikePoIDJUGalerije = mysqli_fetch_row($resultSlikePoIDJUGalerije);
			unlink($vrsticaSlikePoIDJUGalerije[3]);
		}
	}
	$queryDelete = "DELETE FROM galleries WHERE galleries_id=$idGalerije"; //baza skrbi za izbris slik po izbrisu galerije, v kateri so
	if (!mysqli_query($db, $queryDelete))
		print("Izbris ni uspel: $queryDelete<br />" . mysql_error() . "<br /><br />");
}

if (isset($_POST['move_button'])) {
    $idGalerijeZaPremik = $_POST['movedGallery'] * 1; //novi id galerije za updateGallery
		foreach ($_POST['checked'] as $idIzbraneSlike) {	//polje idjev slik, ki so bile izbrane, za spremembo
			$stmtUpdate = "UPDATE photos SET galleries_galleries_id=$idGalerijeZaPremik WHERE photos_id=$idIzbraneSlike";
			$db->query($stmtUpdate);
		}

} else if (isset($_POST['delete_button'])) { //delete izbranih z checkboxom
	foreach ($_POST['checked'] as $idIzbraneSlike) {	//polje idjev slik, ki so bile izbrane, za izbris
		$stmtDobiPot = "SELECT photo_path FROM photos WHERE photos_id=$idIzbraneSlike";
		$resultDobiPot = mysqli_query($db, $stmtDobiPot);
		$vrsticaDobiPot = mysqli_fetch_row($resultDobiPot);
		$potSlike = $vrsticaDobiPot[0];
		unlink($potSlike); //izbris iz mape uploads
		$stmtDelete = "DELETE FROM photos WHERE photos_id=$idIzbraneSlike"; //izbris iz baze
		$db->query($stmtDelete);
	}

	//po idju getaj path slike, jo deletaj, pol pa še iz baze pobriši, nato reloadaj

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
    $IDJIzaNaprej = $_SESSION['IDJIzaUredit'];
    unset($_SESSION['IDJIzaUredit']);
    for($i=0; $i<count($IDJIzaNaprej); $i++) {
      $stmtUpdate = "UPDATE photos SET name='$imenaSlik[$i]', description='$opisiSlik[$i]' WHERE photos_id=$IDJIzaNaprej[$i]";
      $db->query($stmtUpdate);

			$stmtDelete = "DELETE FROM keywords WHERE photos_photos_id=$IDJIzaNaprej[$i] AND users_users_id=$idUserLoggedIn"; //izbris iz baze
			$db->query($stmtDelete);

      $myString = $kBesedeSlik[$i];
      $myArray = explode(',', $myString);
      for($j=0; $j<count($myArray); $j++) {
        $stmtVnosKeywordov = "INSERT INTO keywords(title,photos_photos_id,users_users_id) VALUES ('$myArray[$j]', $IDJIzaNaprej[$i], $idUserLoggedIn)";
        $db->query($stmtVnosKeywordov);
      }
    }
    $message = "Edit succeded! Redirecting to galleries page...";
    echo "<script type='text/javascript'>alert('$message'); location='gallery.php';</script>";
  }
}
?>

    <div class="container">

			<?php
			if (isset($_POST['edit_button'])) {
				$IDjiPravkarNalozenihSlik = array();
				echo '<div class="row">';
				echo '<div class="box">';
				echo '<div class="col-lg-12">';
				echo '<h2><small>EDIT PHOTO DATA</small></h2>';
				echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">'; //formo še copy pastat sem, always delete old keywords!
				echo '<table class="table">';
				echo '<tr>';
				echo '<th>Preview</th>';
				echo '<th>Name</th>';
				echo '<th>Description</th>';
				echo '<th>Keywords (separate by comma!)</th>';
				echo '</tr>';
				$i=0;
				foreach ($_POST['checked'] as $idIzbraneSlike) { //vrstice
					$queryVnesenaSlika = "SELECT * FROM photos WHERE photos_id=$idIzbraneSlike";
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
						else if($j==3) {
							$emailUserLoggedIn = $_SESSION['email'];
							$queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
							$resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
							$vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
							$idUserLoggedIn = $vrsticaUserLoggedIn[0];

							$stmtDobiVseKeywordeTeSlike = "SELECT title FROM keywords WHERE photos_photos_id=$idIzbraneSlike AND users_users_id=$idUserLoggedIn";
							$resultDobiVseKeywordeTeSlike = mysqli_query($db, $stmtDobiVseKeywordeTeSlike);
							$stringKeywordi = "";
							$st_vrstic = mysqli_num_rows($resultDobiVseKeywordeTeSlike);
							if($st_vrstic > 0) {
								for($k = 0; $k<$st_vrstic; $k++) {
									$vrsticaKeyword = mysqli_fetch_row($resultDobiVseKeywordeTeSlike);
									$stringKeywordi = $stringKeywordi . $vrsticaKeyword[0];
									if($k!=($st_vrstic-1)) //če ni zadnji, doda vejico
										$stringKeywordi = $stringKeywordi . ",";
								}
							}
							//posebej obravnavat pol (delete vse bivse)
							echo '<td><input type="text" name="kBesedeSlik[]" value="' . $stringKeywordi . '" required></td>'; //zahtevano; tu bi lahko naredo da ti mogoc prkaze kaj je nt, sam pri vnosu naceloma nebi smelo bit še
						}
					}
					echo '</tr>';
					$i++;
					$IDjiPravkarNalozenihSlik[] = $idIzbraneSlike;
				}
				echo '</table>';
				echo '<input type="submit" name="submitPhotoData" class="btn btn-success" value="Do it!">';
				echo '</form>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
				$_SESSION['IDJIzaUredit'] = $IDjiPravkarNalozenihSlik;
			}

			?>

			<?php if(isset($_SESSION['email'])) { ?>
				<?php if(isset($_GET['updateGallery'])) {
					$queryGetData = "SELECT * FROM galleries WHERE galleries_id='".$_GET['updateGallery']."'";
					$resultGetData = mysqli_query($db, $queryGetData);
					$vrsticaGetData = mysqli_fetch_row($resultGetData); //polje z vrednostmi update galerije - za prikaze v inptu type-u

				?>
					<div class="row">
	        	<div class="box">
	          	<div class="col-lg-12">
								<h2><small>UPDATE GALLERY</small></h2>
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	                Name:&nbsp;<input type="text" name="nameUpdate" maxlength="45" value="<?php echo $vrsticaGetData[1];?>">
	                <br />
	                Description:&nbsp;<input type="text" name="descriptionUpdate" maxlength="280" value="<?php echo $vrsticaGetData[2];?>">
	                <br />
	                <input type="submit" name="submit" class="btn btn-success" value="Do it!">
	                <input type="hidden" name="users_id">
									<input type="hidden" name="updateGalleryID" value="<?php echo $_GET['updateGallery'];?>">
	              </form>
							</div>
						</div>
					</div>
				<?php } ?>

        <div class="row">
            <div class="box">
              <div class="col-lg-12">
              <h2><small>CREATE GALLERY</small></h2>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                Name:&nbsp;<input type="text" name="name" maxlength="45" required>
                <br />
                Description:&nbsp;<input type="text" name="description" maxlength="280">
                <br />
                <input type="submit" name="submit" class="btn btn-success" value="Do it!">
                <input type="hidden" name="users_id">
              </form>
            </div>
          </div>
        </div>
			<?php } ?>

<?php
if(isset($_SESSION['email'])) {
	//prikaz galerij, ki jih ta user ima v "lasti" skupaj z javno javno galerijo!
	//morda še prikaz galerij, ki jih ima deljene, al boš to naredo drugač? like samo selecto slike s pravicami pa prikazo
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
		$st_vrstic = mysqli_num_rows($result);
		echo '<div class="row">';
		echo '<div class="box">';
		echo '<div class="col-lg-12">';
		echo '<h2><small>Choose a gallery</small></h2>';
		$vrednostURL = $_SERVER['PHP_SELF'];
		echo "<form method='post' action='$vrednostURL'>";
		echo '<div class="form-group">';
		echo '<label for="sel1">Choose a gallery from the list:</label>';
		echo '<select name="selectedGallery" style="width: 50%" class="form-control" id="sel1">';
		if(!strcmp($_SESSION['email'],"admin@lookatthis.com")==0) {
			$vrsticaJavna = mysqli_fetch_row($resultJavna);
			echo "<option value='$vrsticaJavna[0]'>$vrsticaJavna[1]</option>";
		}
		for ($j = 0 ; $j < $st_vrstic ; ++$j) {
			$vrstica = mysqli_fetch_row($result);
			echo "<option value='$vrstica[0]'>$vrstica[1]</option>";
		}
		echo '</select>';
		echo '</div>';
		echo '<input type="submit" class="btn btn-1" value="Choose"/>';
		echo '</form>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
} else { //uporabnik nima računa/ni prijavljen - vidi le skupno (javno) galerijo in pa slike, ki jih je sam dodal
		echo '<div class="row">';
		echo '<div class="box">';
		echo '<div class="col-lg-12">';
		echo '<div class="container gallery-container">';
		$queryGalleryInfo = "SELECT * FROM galleries WHERE galleries_id='10'";
		$resultGalleryInfo = mysqli_query($db, $queryGalleryInfo);
		$vrsticaGalleryInfo = mysqli_fetch_row($resultGalleryInfo);
		echo "<h2 style='margin: 0; display: inline-block;'><small>NOW VIEWING - $vrsticaGalleryInfo[1]</small></h2>";
		echo "<p>Description: $vrsticaGalleryInfo[2]<br>";
		$editedDateCreated = date('j. F Y', strtotime($vrsticaGalleryInfo[3]));
		$editedDateModified = date('j. F Y', strtotime($vrsticaGalleryInfo[4]));
		echo "Date created: $editedDateCreated<br>";
		echo "Date modified: $editedDateModified<br>";

		$querySteviloSlik = "SELECT COUNT(*) FROM photos WHERE galleries_galleries_id='10'";
		$resultSteviloSlik = mysqli_query($db, $querySteviloSlik);
		$steviloSlik = mysqli_fetch_row($resultSteviloSlik)[0];

		echo "# of photos: $steviloSlik</p>";

		echo '<div class="tz-gallery">';
		echo '<div class="row">';

		$queryPhotosInGallery = "SELECT * FROM photos WHERE galleries_galleries_id='10'";
		$resultPhotosInGallery = mysqli_query($db, $queryPhotosInGallery);

		$numOfPhotos = mysqli_num_rows($resultPhotosInGallery);
		if($numOfPhotos > 0) {
			for ($j = 0 ; $j < $numOfPhotos ; $j++) {
				$photoRow = mysqli_fetch_row($resultPhotosInGallery);
				echo '<div class="col-sm-6 col-md-4">';
				echo '<div class="thumbnail">';
				echo "<a class='lightbox' href='$photoRow[3]'>";
				echo "<img src='$photoRow[3]' alt='photo'>";
				echo '</a>';
				echo '<div class="caption">';
				echo "<h3>$photoRow[1]</h3>";
				echo "<p>$photoRow[2]</p>";
				$editedDateUploaded = date('j. F Y', strtotime($photoRow[4]));
				echo "<p>$editedDateUploaded</p>";
				$jeZasebna = $photoRow[5];
				/*if($jeZasebna)
					echo '<span class="label label-danger">private</span>';
				else
					echo '<span class="label label-success">public</span>';*/
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
		} else {
			echo "<p>There are no photos in this gallery!<p>";
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';


//TU SPOD NAREDI, DA PRIKAZE NELOGINANEMU USERJU SLIKE KI JIH JE ON DODAL V SKUPNO (IZ COOKIJEV DOBIT IDJE AL NEKI)

}
?>

<?php
if(isset($_SESSION['email'])) {
	if(isset($_POST['selectedGallery'])) {
		echo '<div class="row">';
		echo '<div class="box">';
		echo '<div class="col-lg-12">';
		echo '<div class="container gallery-container">';
		$queryGalleryInfo = "SELECT * FROM galleries WHERE galleries_id='".$_POST['selectedGallery']."'";
		$resultGalleryInfo = mysqli_query($db, $queryGalleryInfo);
		$vrsticaGalleryInfo = mysqli_fetch_row($resultGalleryInfo);
		echo "<h2><small>NOW VIEWING - $vrsticaGalleryInfo[1]</small></h2>";
		echo "<p>Description: $vrsticaGalleryInfo[2]<br>";
		$editedDateCreated = date('j. F Y', strtotime($vrsticaGalleryInfo[3]));
		$editedDateModified = date('j. F Y', strtotime($vrsticaGalleryInfo[4]));
		echo "Date created: $editedDateCreated<br>";
		echo "Date modified: $editedDateModified<br>";
		$querySteviloSlik = "SELECT COUNT(*) FROM photos WHERE galleries_galleries_id='".$_POST['selectedGallery']."'";
		$resultSteviloSlik = mysqli_query($db, $querySteviloSlik);
		$steviloSlik = mysqli_fetch_row($resultSteviloSlik)[0];
		echo "# of photos: $steviloSlik</p>";

		$vrednostURL = $_SERVER['PHP_SELF'];
		$izbranaGalerija = $_POST['selectedGallery'];

		if($izbranaGalerija==10) { //če je galerija javna in če je prijavljen admin, se mu prikažejo opcije
			if(strcmp($_SESSION['email'], "admin@lookatthis.com")==0) {
				echo "<div style='float: left; padding: 5px;'>";
				echo "<form action='$vrednostURL' method='get'>";
				echo "<input type='hidden' name='updateGallery' value='$izbranaGalerija'/>";
				echo "<input type='submit' class='btn btn-info' value='Edit gallery'></form></div>";
				echo "<div style='float: left; padding: 5px;'><form onsubmit='return validateDelete(this);' action='$vrednostURL' method='get'>";
				//$izbranaGalerija je definirana že zgoraj, zato ni treba še 1x
				echo "<input type='hidden' name='deleteGallery' value='$izbranaGalerija'/>";
				echo "<input type='submit' class='btn btn-danger' value='Delete gallery'></td></form></div><br>";
			}
		} else { //če ni javna galerija, se vsem izpišejo možnosti (prijavljenim)
			echo "<div style='float: left; padding: 5px;'>";
			echo "<form action='$vrednostURL' method='get'>";
			echo "<input type='hidden' name='updateGallery' value='$izbranaGalerija'/>";
			echo "<input type='submit' class='btn btn-info' value='Edit gallery'></form></div>";
			echo "<div style='float: left; padding: 5px;'><form onsubmit='return validateDelete(this);' action='$vrednostURL' method='get'>";
			//$izbranaGalerija je definirana že zgoraj, zato ni treba še 1x
			echo "<input type='hidden' name='deleteGallery' value='$izbranaGalerija'/>";
			echo "<input type='submit' class='btn btn-danger' value='Delete gallery'></td></form></div><br>";
		}


		$queryPhotosInGallery = "SELECT * FROM photos WHERE galleries_galleries_id='".$_POST['selectedGallery']."'";
		$resultPhotosInGallery = mysqli_query($db, $queryPhotosInGallery);

		$numOfPhotos = mysqli_num_rows($resultPhotosInGallery);
		if($numOfPhotos > 0) {
			echo "<form action='$vrednostURL' method='post'>";
			echo '<div class="tz-gallery">';
			echo '<div class="row">';
			for ($j = 0 ; $j < $numOfPhotos ; $j++) {
				$photoRow = mysqli_fetch_row($resultPhotosInGallery);
				echo '<div class="col-sm-6 col-md-4">';
				echo '<div class="thumbnail">';
				echo "<a class='lightbox' href='$photoRow[3]'>";
				echo "<img src='$photoRow[3]' alt='photo'>";
				echo '</a>';
				echo '<div class="caption">';
				echo "<h3>$photoRow[1]</h3>";
				echo "<p>$photoRow[2]</p>";
				$editedDateUploaded = date('j. F Y', strtotime($photoRow[4]));
				echo "<p>$editedDateUploaded</p>";
				$jeZasebna = $photoRow[5];
				/*if($jeZasebna) //ZAKOMENTIRAL KER NE UPORABLJAŠ NITI TEGA
					echo '<span class="label label-danger">private</span>';
				else
					echo '<span class="label label-success">public</span>';
					*/
					$emailUserLoggedIn = $_SESSION['email'];
					$queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
					$resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
					$vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
					$idUserLoggedIn = $vrsticaUserLoggedIn[0];

					$stmtDobiVseKeywordeTeSlike = "SELECT title FROM keywords WHERE photos_photos_id=$photoRow[0] AND users_users_id=$idUserLoggedIn";
					$resultDobiVseKeywordeTeSlike = mysqli_query($db, $stmtDobiVseKeywordeTeSlike);
					$st_vrstic = mysqli_num_rows($resultDobiVseKeywordeTeSlike);
					if($st_vrstic > 0) {
						for($k = 0; $k<$st_vrstic; $k++) {
							$vrsticaKeyword = mysqli_fetch_row($resultDobiVseKeywordeTeSlike);
							echo '<span class="label label-default">' . $vrsticaKeyword[0] . '</span>&nbsp;'; //teh label keywordov ne izpisujes za unregistered users
						}
					}

							//tu dodaš še checkboxe, a prvo preveriš
							if(($_POST['selectedGallery']*1)==10) { //če je javna, treba posebej obravnavat access
								//poiskati v access za id trenutne slike, če je vnesena in če je id userja owner
								$stmtHasAccess = "SELECT * FROM user_has_access_to_photos WHERE users_users_id=$idUserLoggedIn AND photos_photos_id=$photoRow[0]";
								$resultHasAccess = mysqli_query($db, $stmtHasAccess);

								$numOfAccess = mysqli_num_rows($resultHasAccess);
								if($numOfAccess > 0) { ?>
									<br><br>
									<div class="pretty p-default p-round p-thick">
						        <input type="checkbox" value="<?php echo $photoRow[0]?>" name="checked[]">
						        <div class="state">
						            <label>Select photo</label>
						        </div>
						    	</div>
								<?php
								}


							} else { //doda checkbox kr vsem ?>
								<br><br>
								<div class="pretty p-default p-round p-thick">
									<input type="checkbox" value="<?php echo $photoRow[0]?>" name="checked[]">
									<div class="state">
											<label>Select photo</label>
									</div>
								</div>
							<?php }



				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
?>

			<input type="submit" class="btn btn-info" onclick ="return jeIzbranaVsajEnaSlik(); " name="edit_button" value="Edit selected" />
			<input type="submit" class="btn-danger" onclick="return jeIzbranaVsajEnaSlikDelete();" name="delete_button" value="Delete selected" />
<?php
			echo "<p>-----------------------------------------------<br> or move selected photos to gallery: <p>";
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
				$st_vrstic = mysqli_num_rows($result);
					echo '<select name="movedGallery" style="width: 50%" class="form-control">';
					$vrsticaJavna = mysqli_fetch_row($resultJavna);
					if(!strcmp($vrsticaJavna[0],$_POST['selectedGallery'])==0) { //doda javno, če ta ni izbrana
						echo "<option value='$vrsticaJavna[0]'>$vrsticaJavna[1]</option>";
					}
					for ($j = 0 ; $j < $st_vrstic ; $j++) {
						$vrstica = mysqli_fetch_row($result);
						if(!strcmp($vrstica[0],$_POST['selectedGallery'])==0) { //doda vse galerije razen izbrano
							echo "<option value='$vrstica[0]'>$vrstica[1]</option>";
						}
					}
					echo '</select>';
		} ?>

		<!--še gumb za poslat izbrano in gumb za izbris, + gumb na pos sliki za ibris-->
		<input type="submit" class="btn-warning" onclick="return jeIzbranaVsajEnaSlik();" name="move_button" value="Move selected" />

		<?php
			echo "</form>";
		} else {
			echo "<p><br>There are no photos in this gallery!<p>"; //delete all-currently
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

	}
}
?>
    </div>
    <!-- /.container -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.8.1/baguetteBox.min.js"></script>
		<script>baguetteBox.run('.tz-gallery');</script>

<?php
function pretvoriVPolje($input) {
    $output = array();
    for($i=0; $i<count($input); $i++)
      $output[] = $input[$i];
    return $output;
}
?>

<?php include 'includes/footer.php';?>
