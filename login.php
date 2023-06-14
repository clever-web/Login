<?php
# Initialize session
session_start();

# Check if user is already logged in, If yes then redirect him to index page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE) {
  echo "<script>" . "window.location.href='./'" . "</script>";
  exit;
}

# Include connection
require_once "./config.php";

# Define variables and initialize with empty values
$employee_id_err = "";
$employee_id = "";

# Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["employee_id"]))) {
    $employee_id_err = "Please enter your id.";
  } else {
    $employee_id = trim($_POST["employee_id"]);
  }

  # Validate credentials 
  if (empty($employee_id_err)) {
    # Prepare a select statement
    $sql = "SELECT employee_id FROM attendance WHERE employee_id = '$employee_id'";

    $result = $conn->query($sql);
    if ($result -> num_rows == 1) {

      #Check EmployeeID is active
      $sql = "SELECT employee_id FROM employee WHERE employee_id = '$employee_id' AND status='active'";
      $result = $conn->query($sql);

      if ($result -> num_rows == 1) {
        # Store data in session variables
        $_SESSION["employee_id"] = $employee_id;
        $_SESSION["loggedin"] = TRUE;
        # Set entry time and date
        $entry_time = date("H:i:s");
        $entry_date = date("Y-m-d");

        $sql = "UPDATE attendance SET entry_time='$entry_time', entry_date='$entry_date' where employee_id='$employee_id'";
        if ($conn->query($sql)) {
          # Redirect user to index page
          echo "<script>" . "window.location.href='./'" . "</script>";
          exit;
        } 
      } else {
        $_SESSION["status"] = "You are not active now!";
         # Redirect user to login page if the employee is not active
        echo "<script>" . "window.location.href='./login.php'" . "</script>";
      }
    } else {
      # If user doesn't exists show an error message
      $employee_id_err = "Invalid id";
    }
  }

  #Close connection
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User login system</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/main.css">
  <link rel="shortcut icon" href="./img/favicon-16x16.png" type="image/x-icon">
  <script defer src="./js/script.js"></script>
</head>

<body>
  <div class="container">
    <div class="row min-vh-100 justify-content-center align-items-center">
      <div class="col-lg-5">
        <?php
        if (isset($_SESSION["status"])) {
          echo "<div class='alert alert-warning'>" . $_SESSION["status"] . "</div>";
        }
        ?>
        <div class="form-wrap border rounded p-4">
          <h1>Log In</h1>
          <p>Please login to continue</p>
          <!-- form starts here -->
          <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="mb-3">
              <label for="employee_id" class="form-label">Enter your ID</label>
              <input type="text" class="form-control" name="employee_id" id="employee_id" value="<?= $employee_id; ?>">
              <small class="text-danger"><?= $employee_id_err; ?></small>
            </div>
            <div class="mb-3">
              <input type="submit" class="btn btn-primary form-control" name="submit" value="Log In">
            </div>
          </form>
          <!-- form ends here -->
        </div>
      </div>
    </div>
  </div>
</body>

</html>