<?php include 'includes/header.php';?>
<?php include 'includes/mysqli_connect.php';?>

    <div class="container">



        <div class="row">
            <div class="box">
                <div class="col-lg-12">
                  <h2><small>Search by keyword(s)</small></h2>
            			<?php $vrednostURL = $_SERVER['PHP_SELF'];
              			echo "<form method='post' action='$vrednostURL'>";
              			echo '<div class="form-group">';
              			echo '<label for="sel1">Please, separate multiple keywords with comma.</label>';
              			echo '<input type="text" name="searchString" style="width: 50%" class="form-control" id="sel1">';
              			echo '</div>';
              			echo '<input type="submit" class="btn btn-success" value="Search"/>';
              			echo '</form>';
                  ?>
                </div>
            </div>
        </div>

        <?php
        if(isset($_SESSION['email'])) {
          if(isset($_POST['searchString'])) {
            echo '<div class="row">';
            echo '<div class="box">';
            echo '<div class="col-lg-12">';

            $emailUserLoggedIn = $_SESSION['email'];
            $queryUserLoggedIn = "SELECT users_id FROM users WHERE email='$emailUserLoggedIn'";
            $resultUserLoggedIn = mysqli_query($db, $queryUserLoggedIn);
            $vrsticaUserLoggedIn = mysqli_fetch_row($resultUserLoggedIn);
            $idUserLoggedIn = $vrsticaUserLoggedIn[0];

            $searchString = $_POST['searchString'];
            $inputtedKeywords = explode(',', $searchString);
            $IDjiNajdenihSlik = array();
            for($i=0; $i<count($inputtedKeywords); $i++) { //distinct pobrise duplikate pri poizvedbi iz baze
              $stmtSearch = "SELECT DISTINCT photos_photos_id FROM keywords WHERE title='$inputtedKeywords[$i]' AND users_users_id=$idUserLoggedIn";
          		$resultSearch = mysqli_query($db, $stmtSearch);
              $st_vrstic = mysqli_num_rows($resultSearch);
							if($st_vrstic > 0) {
								for($k = 0; $k<$st_vrstic; $k++) {
									$vrsticaResult = mysqli_fetch_row($resultSearch);
									$IDjiNajdenihSlik[] = $vrsticaResult[0];
								}
							}
            }
            $IDjiNajdenihSlik = array_unique($IDjiNajdenihSlik); //pobrise duplikate iz polja

            $steviloZadetkov = count($IDjiNajdenihSlik);
            echo "<h2><small>Search results ($steviloZadetkov)</small></h2>";


            $vrednostURL = $_SERVER['PHP_SELF'];

            if(count($IDjiNajdenihSlik)>0) {
              echo '<div class="container gallery-container">';
              echo "<form action='gallery.php' method='post'>";
              echo '<div class="tz-gallery">';
              echo '<div class="row">';
              for($i=0; $i<count($IDjiNajdenihSlik); $i++) {
                $queryPhotos = "SELECT * FROM photos WHERE photos_id=$IDjiNajdenihSlik[$i]";
                $resultPhotos = mysqli_query($db, $queryPhotos);
                $photoRow = mysqli_fetch_row($resultPhotos);
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
                $stmtDobiVseKeywordeTeSlike = "SELECT title FROM keywords WHERE photos_photos_id=$photoRow[0] AND users_users_id=$idUserLoggedIn";
        				$resultDobiVseKeywordeTeSlike = mysqli_query($db, $stmtDobiVseKeywordeTeSlike);
        				$st_vrstic = mysqli_num_rows($resultDobiVseKeywordeTeSlike);
        				if($st_vrstic > 0) {
        					for($k = 0; $k<$st_vrstic; $k++) {
        						$vrsticaKeyword = mysqli_fetch_row($resultDobiVseKeywordeTeSlike);
        						echo '<span class="label label-default">' . $vrsticaKeyword[0] . '</span>&nbsp;'; //teh label keywordov ne izpisujes za unregistered users
        					}
        				} ?>
                <br><br>
                <div class="pretty p-default p-round p-thick">
                  <input type="checkbox" value="<?php echo $photoRow[0]?>" name="checked[]">
                  <div class="state">
                      <label>Select photo</label>
                  </div>
                </div>
                <?php
                echo '</div>';
                echo '</div>';
                echo '</div>';

              }
              echo '</div>';
              echo '</div>';
              echo '</div>';

              ?>
              	<input type="submit" class="btn btn-info" onclick="return jeIzbranaVsajEnaSlik();" name="edit_button" value="Edit selected" />
              	<input type="submit" class="btn-danger" onclick="return jeIzbranaVsajEnaSlikDelete();" name="delete_button" value="Delete selected" />
              <?php
              echo "<p>-----------------------------------------------<br> or move selected photos to gallery: <p>";

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
                  if(!strcmp($_SESSION['email'],"admin@lookatthis.com")==0) {
        					       echo "<option value='$vrsticaJavna[0]'>$vrsticaJavna[1]</option>";
                  }
        					for ($j = 0 ; $j < $st_vrstic ; $j++) {
        						$vrstica = mysqli_fetch_row($result);
        						echo "<option value='$vrstica[0]'>$vrstica[1]</option>";
        					}
        					echo '</select>';
        		} ?>
            <!--še gumb za poslat izbrano in gumb za izbris, + gumb na pos sliki za ibris-->
        		<input type="submit" class="btn-warning" onclick="return jeIzbranaVsajEnaSlik();" name="move_button" value="Move selected" />

        		<?php
        			echo "</form>";
          } else {
            echo "<p>Search did not give any results!<p>"; //delete all-currently
          }

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
