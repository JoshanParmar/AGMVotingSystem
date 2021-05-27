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
            <h1 class="font-weight-light"> Your prompt for the minigame is:</h1>
            <?php
            require_once "config.php";
            /** @var $mysqli mysqli */

            // Get the list of elections
            $stmt = "SELECT id, prompt FROM minigame_prompts WHERE used = 0 ORDER BY rand() LIMIT 1";
            $result = $mysqli->query($stmt);

            // Display the list of elections as clickable buttons

            if ($result !== false && $result-> num_rows == 1) {
                $row = $result->fetch_assoc();
                    $prompt = $row["prompt"];
                    $id = $row["id"];
                    echo "<h2 class='font-weight-bolder'> " . $prompt . "</h2>";
                    echo "<h2 class='text-danger'> Do not refresh this page until the next round.</h2>";
                    $sql_change_status = "UPDATE minigame_prompts SET used = 1 WHERE id = ?";

                    if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                        $stmt_change_status->bind_param("i", $id);
                        if ($stmt_change_status->execute()) {
                            echo "This prompt has been marked as used on the system";
                        } else {
                            echo $mysqli->error;
                        }
                    }
                    $stmt_change_status->close();
            } else {
                echo "No unused prompts left.";
            }


            $mysqli->close();

            ?>
        </div>
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
