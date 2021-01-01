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
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap and JQuery CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <title>Hello, world!</title>
</head>
<body>
<header>
    <?php include "navigation.php"; ?>
</header>

<main>
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">CULA AGM Online Voting</h1>
            <p class="lead text-muted">Below are the votes taking place at this election</p>
        </div>
    </section>
    <div class="container-sm">
        <div class="page-header">
            <h1 class="font-weight-light">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></h1>
            <h1  class="font-weight-light"> You can now vote in elections.</h1>
            <?php
            require_once "config.php";
            /** @var $mysqli mysqli */

            // Get the list of elections
            $stmt = "SELECT id, position_name, positions_available, status FROM elections";
            $result = $mysqli->query($stmt);

            // Display the list of elections as clickable buttons
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $button_class = "";
                    $button_text = "";
                    $button_link = "";

                    switch ($row["status"]) {
                        case 0:
                            $button_class = "btn-secondary";
                            $button_text = $row["position_name"] . " - Election yet to begin";
                            $button_link = "vote.php?election_id=".$row["id"];
                            break;
                        case 1:
                            $button_class = "btn-primary";
                            $button_text = $row["position_name"] . " - Vote";
                            $button_link = "vote.php?election_id=".$row["id"];
                            break;

                        case 2:
                            $button_class = "btn-success";
                            $button_text = $row["position_name"] . " - See Results";
                            $button_link = "vote.php?election_id=".$row["id"];
                            break;
                    }
                    echo "<a href='" . $button_link .  "#' class='btn " . $button_class . " my-2'>" . $button_text
                        . "</a> <br>";

                    echo "</tr>";
                }
            } else {
                echo "No elections have been scheduled for you AGM yet";
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
