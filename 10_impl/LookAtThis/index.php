<?php include 'includes/header.php';?>
<?php include 'includes/mysqli_connect.php';?>
<?php
if((isset($_POST['email'])) && (isset($_POST['pass']))) {
  define("SOL", "moje-solno-besedilo");
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pravilnoGeslo = false;
  $pravilnoGeslo = login($email, $pass, $db);

  if($pravilnoGeslo) {
    $_SESSION["email"] = $email;
  }
  else {
    $message1 = "Wrong password or the user does not exist! Try again.";
    echo "<script type='text/javascript'>alert('$message1'); location='index.php';</script>";
  }
}

function login($email, $password, $db) {
  $isAuthenticated = false;
  $saltedPassword = SOL . $password;
  $bazniMail = "";
  $baznoGeslo = "";
  $hashedPassword = sha1($saltedPassword);
  $query = $db->prepare("SELECT * FROM users WHERE email = ?");
  $query->bind_param("s", $email);
  $query->execute();
  $rezultat = $query->get_result();
  if($rezultat->num_rows > 0) {
    for ($j=0; $j<$rezultat->num_rows; $j++) {
      $vrstica = mysqli_fetch_row($rezultat);
      $bazniMail = $vrstica[3];
      $baznoGeslo = $vrstica[4];
      if((strcmp($bazniMail, $email)==0)&&(strcmp($baznoGeslo, $hashedPassword)==0))
        $isAuthenticated = true;
      else
        $isAuthenticated = false;
    }
  }
  return $isAuthenticated;
}

if(isset($_POST['logout'])) {
  if(strcmp($_POST['logout'], "me")==0) {
    session_destroy();
    echo "<script type='text/javascript'>location='index.php';</script>";
  }
}
?>
    <div class="container">

        <div class="row">
            <div class="box">
                <div class="col-lg-12 text-center">
                  <?php if(!isset($_SESSION['email'])) { ?>

                    <h2>
                        <small>Welcome to</small>
                    </h2>
                    <h1>
                        <span class="brand-name">Look At This!</span>
                    </h1>
                    <hr class="tagline-divider">
                    <h2>
                      <small>LOGIN</small>
                    </h2>

                    <form onsubmit="return loginValidation()" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="text-align: center">
                		<fieldset>
                		<div>
                			<label class="name">
                				<input type="text" id="email" name="email" value="" placeholder="Email" required>
                			</label>
                		</div>
                		<div>
                			<label class="phone">
                				<input type="password" id="pass" name="pass"  value="" placeholder="Geslo" required>
                			</label>
                		</div>
                		<div class="buttons-wrapper">
                			<input type="submit" class="btn btn-1" value="Submit" />
                		</div>


                				<center><p id="feedback_reg"></p></center>

                		<h2><small>Without an account? Create one here.</small></h2>

                		</fieldset>
                	</form>
                  <div class="buttons-wrapper">
                    <a href="registration.php" style="color: black;"><button class="btn btn-1">Register</button></a>
                  </div>
                  <br />
                  <a href="photoUP.php"><h6><small>Continue without login (limited)</small></h6></a>
                <?php } else { ?>
                  <h2><small>Do you wish to logout?</small></h2>
                  <h4><small><?php echo $_SESSION['email'] ?></small></h4>
                  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="text-align: center">
                    <input type="hidden" name="logout" value="me" />
                    <input type="submit" class="btn btn-1" value="Click here" />
                  </form>
                <?php } ?>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container -->

<?php include 'includes/footer.php';?>
