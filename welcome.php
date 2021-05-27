<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<?php include "header.php"; ?>
<body>
<header>
    <?php include "navigation.php"; ?>
</header>

<main>
    <section class="jumbotron jumbotron-image text-center" style="background-image: url(imgs/vince3.jpg);">
        <div class="container jumbotron-container">
            <h1 class="jumbotron-heading text-white jumbotron-text">CULA Online</h1>
        </div>
    </section>
    <div class="container-sm">
        <div class="page-header">
            <h1 class="font-weight-light">Hi, <b><?php echo htmlspecialchars($_SESSION["realname"]); ?></b></h1>
            <h1  class="font-weight-light"> Welcome to our site.</h1>

        </div>
        <p>
            <a href="votes-list.php" class="btn btn-primary my-1">Vote in SD Motion Poll</a>
            <a href="minigame_user_prompt.php" class="btn btn-warning my-1">SDs Mini Game</a>
            <?php
            // If the user is an administrator, give them the button to access the manage elections page
            if ($_SESSION["administrator"]){
                echo "<a href='create_election.php' class='btn btn-info my-1'>Manage Polls</a>
                      <a href='manage_prompts.php' class='btn btn-info my-1'>Manage Prompts</a>
                      <a href='manage_users.php' class='btn btn-success my-1'>Manage Users</a>";
            } ?>
            <a href="logout.php" class="btn btn-danger my-1">Sign Out of Your Account</a>
        </p>
    </div>
</main>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

</body>
</html>
