<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the user is an administrator, otherwise redidrect them to the homepage
if (!$_SESSION["administrator"]) {
    header("location: welcome.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prompt_text = trim($_POST["prompt_text"]);
    if ($prompt_text != "") {
        require_once "config.php";
        /** @var $mysqli mysqli */

        $sql_write_prompt = "INSERT INTO minigame_prompts (prompt) VALUES (?)";

        if ($stmt_write_prompt = $mysqli->prepare($sql_write_prompt)) {
            $stmt_write_prompt->bind_param("s", $param_prompt_text);

            $param_prompt_text = $prompt_text;

            // Refresh the page on complete
            if ($stmt_write_prompt->execute()) {
                header("location: manage_prompts.php");
                exit;
            } else {
                echo $mysqli->error;
            }

            $stmt_write_prompt->close();
        }
    } else {
        echo "Prompt was Blank";
    }
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
        <?php
        // If user has just clicked delete user, delete the user from the SQL table
        if (isset($_GET['delete_prompt'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */
            $delete_prompt_id = $_GET['delete_prompt'];


            $sql_delete_row = "DELETE FROM minigame_prompts WHERE id = ?";
            if ($stmt_delete_row = $mysqli->prepare($sql_delete_row)) {
                $stmt_delete_row->bind_param("i", $delete_prompt_id);

                if ($stmt_delete_row->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully removed user id " . $delete_prompt_id . " from your list</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_delete_row->close();

        }

        // If the user has just clicked make_admin, make the change to the admin status in the SQL table
        if (isset($_GET['mark_used'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */

            $mark_used_id = $_GET['mark_used'];

            $sql_change_status = "UPDATE minigame_prompts SET used = 1 WHERE id = ?";

            if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                $stmt_change_status->bind_param("i", $mark_used_id);

                if ($stmt_change_status->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully marked prompt " . $mark_used_id . " as used.</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }

        // If the user has just clicked make_admin, make the change to the admin status in the SQL table
        if (isset($_GET['mark_unused'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */

            $mark_unused_id = $_GET['mark_unused'];

            $sql_change_status = "UPDATE minigame_prompts SET used = 0 WHERE id = ?";

            if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                $stmt_change_status->bind_param("i", $mark_unused_id);

                if ($stmt_change_status->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully marked prompt " . $mark_unused_id . " as unused.</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }

        ?>

        <h1 class="font-weight-light">For you Minigame, you currently have the following prompts:</h1>

        <?php

        require_once "config.php";
        /** @var $mysqli mysqli */

        // Get list of elections from the SQL table and create a table on the HTML page displaying the list of elections
        $sql_get_prompts = "SELECT id, prompt, used FROM minigame_prompts";
        if ($stmt_get_prompts = $mysqli->prepare($sql_get_prompts)) {
            if ($stmt_get_prompts->execute()) {
                $stmt_get_prompts->store_result();

                if ($stmt_get_prompts->num_rows > 0) {
                    echo "<table class='table table-hover mt-4'>";
                    echo "<thead class='thead-dark'>";
                    echo "<tr>";
                    echo "<th scope='col'>#</th>";
                    echo "<th scope='col'>Prompt</th>";
                    echo "<th scope='col'>Used Status</th>";
                    echo "<th scope='col'>Delete Prompt</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $stmt_get_prompts->bind_result($r_prompt_id, $r_prompt, $r_used_status);

                    while ($stmt_get_prompts->fetch()) {
                        echo "<tr>";
                        echo "<th scope='row'>" . $r_prompt_id . "</th>";
                        echo "<td>" . $r_prompt . "</td>";
                        echo "<td>";
                        switch ($r_used_status) {
                            case 0:
                                echo "<a type='button' class='btn btn-success' onClick='save_scroll()' href = '?mark_used=" . $r_prompt_id . "' >Mark Used</a>";
                                break;
                            case 1:
                                echo "<a type='button' class='btn btn-danger' onClick='save_scroll()' href = '?mark_unused=" . $r_prompt_id . "' >Mark Unused</a>";
                                break;
                        }
                        echo "</td>";
                        echo "<td><a type='button' class='btn btn-danger' onClick='save_scroll()' href = '?delete_prompt=" . $r_prompt_id . "' >Delete Prompt</a></td>";

                    }
                    echo "</tbody>";
                    echo "</table>";

                } else {
                    echo "<h4 class='font-weight-light text-danger'> You have no prompts set up for your minigame</h4>";

                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt_get_prompts->close();
        }

        $mysqli->close();

        ?>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addPromptModal">
            Add additional prompt
        </button>

    </div>

</main>

<div class="modal fade" id="addPromptModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPromptModalLabel">Add Prompt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="prompt_text">Prompt Text</label>
                        <input type="text" name="prompt_text" class="form-control" id="prompt_text"
                               placeholder="Text (e.g. CULA invading SolCal)">
                    </div>

                    <div class="form-group mt-2">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-secondary" value="Reset">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"
        integrity="sha512-aUhL2xOCrpLEuGD5f6tgHbLYEXRpYZ8G5yD+WlFrXrPy2IrWBlu6bih5C9H6qGsgqnU6mgx6KtU8TreHpASprw=="
        crossorigin="anonymous"></script>
<script src="scrollfix.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

</body>
</html>

