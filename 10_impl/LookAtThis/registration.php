<?php include 'includes/header.php';?>
<?php include 'includes/mysqli_connect.php';?>
<?php
$output = "";
if((isset($_POST['first_name'])) && (isset($_POST['last_name'])) && (isset($_POST['email'])) && (isset($_POST['pass'])) && (isset($_POST['pass1']))) {
  define("SOL", "moje-solno-besedilo");
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass1 = $_POST['pass1'];
  if(strcmp($pass, $pass1)==0) {
    $slanoGeslo = SOL . $pass;
    $hashanoGeslo = sha1($slanoGeslo);
    $query = $db->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $rezultat = $query->get_result();
    if($rezultat->num_rows > 0)
      $output = "User with this email already exists!";
    else {
      $stmt = $db->prepare("INSERT INTO users(first_name, last_name, email, pass) VALUES (?, ?, ?, ?)");
      $stmt->bind_param('ssss', $first_name, $last_name, $email, $hashanoGeslo);
      $stmt->execute();
      $message = "Registration succeded! Redirecting to login page...";
      echo "<script type='text/javascript'>alert('$message'); location='index.php';</script>";
    }
  }
  else
    $output = "Passwords didn't match! Try again.";
}
 ?>


    <div class="container">


        <div class="row">
            <div class="box">
                <div class="col-lg-12">

                  <form method="post" onsubmit="return registrationValidation()" action="<?php echo $_SERVER['PHP_SELF']; ?>" style=" text-align:center">
                    <h2>
                      <small>Registration</small>
                    </h2>
                  			<fieldset>
                  				<div>
                  					<label class="name">
                  						<input type="text" id="first_name" name="first_name" value="" placeholder="First name" required>
                  					</label>
                  				</div>
                  				<div>
                  					<label class="name">
                  						<input type="text" id="last_name" name="last_name" value="" placeholder="Last name" required>
                  					</label>
                  				</div>
                  				<div>
                  					<label class="name">
                  						<input type="text" id="email" name="email" value="" placeholder="Email" required>
                  					</label>
                  				</div>
                  				<div>
                  					<label class="phone">
                  						<input type="password" id="pass" name="pass"  value="" placeholder="Password" required>
                  					</label>
                  				</div>
                  				<div>
                  					<label class="phone">
                  						<input type="password" id="pass1" name="pass1"  value="" placeholder="Repeat password" required>
                  					</label>
                  				</div>
                  				<div class="buttons-wrapper">
                  					<input type="submit" class="btn btn-1" value="Complete registration" />
                  				</div>

                  				<br />
                  				<center><p id="feedback_reg"><?php print $output; ?></p></center>
                          <h2><small>Already have an account? Login here.</small></h2>
                  				<div class="buttons-wrapper">
                            <a href="index.php" style="color: black;"><button type="button" class="btn btn-1">Login</button></a>
                  				</div>
                             </fieldset>
                          </form>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container -->
<?php include 'includes/footer.php';?>
