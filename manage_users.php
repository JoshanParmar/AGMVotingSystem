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

// Create the new election in the SQL table
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $position_name = trim($_POST["position_name"]);
    $positions_available = trim($_POST["positions_available"]);

    if ($position_name != "") {
        require_once "config.php";
        /** @var $mysqli mysqli */

        $sql_write_election = "INSERT INTO elections (position_name, positions_available) VALUES (?, ?)";

        if ($stmt_write_election = $mysqli->prepare($sql_write_election)) {
            $stmt_write_election->bind_param("si", $param_position_name, $param_positions_available);

            $param_position_name = $position_name;
            $param_positions_available = $positions_available;

            // Refresh the page on complete
            if ($stmt_write_election->execute()) {
                header("location: create_election.php");
                exit;
            } else {
                echo $mysqli->error;
            }

            $stmt_write_election->close();
        }
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
        if (isset($_GET['y_pos'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */
            $delete_user_id = $_GET['delete_user'];


            $sql_delete_row = "DELETE FROM users WHERE id = ?";
            if ($stmt_delete_row = $mysqli->prepare($sql_delete_row)) {
                $stmt_delete_row->bind_param("i", $delete_user_id);

                if ($stmt_delete_row->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully removed user id " . $delete_user_id . " from your AGM</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_delete_row->close();

        }


        // If user has just clicked delete user, delete the user from the SQL table
        if (isset($_GET['delete_user'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */
            $delete_user_id = $_GET['delete_user'];


            $sql_delete_row = "DELETE FROM users WHERE id = ?";
            if ($stmt_delete_row = $mysqli->prepare($sql_delete_row)) {
                $stmt_delete_row->bind_param("i", $delete_user_id);

                if ($stmt_delete_row->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully removed user id " . $delete_user_id . " from your AGM</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_delete_row->close();

        }

        // If the user has just clicked make_admin, make the change to the admin status in the SQL table
        if (isset($_GET['make_admin'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */

            $make_admin_user_id = $_GET['make_admin'];

            $sql_change_status = "UPDATE users SET administrator = ? WHERE id = ?";

            if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                $admin_status_code = 1;
                $stmt_change_status->bind_param("ii", $admin_status_code, $make_admin_user_id);

                if ($stmt_change_status->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully made user id " . $make_admin_user_id . " an admin.</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }

        // If the user has just clicked remove_admin, make the change to the admin status in the SQL table
        if (isset($_GET['remove_admin'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */

            $remove_admin_user_id = $_GET['remove_admin'];

            $sql_change_status = "UPDATE users SET administrator = ? WHERE id = ?";

            if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                $unadmin_status_code = 0;
                $stmt_change_status->bind_param("ii", $unadmin_status_code, $remove_admin_user_id);

                if ($stmt_change_status->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully made user id " . $remove_admin_user_id . " no longer an admin.</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }

        // If the user has just clicked make_admin, make the change to the admin status in the SQL table
        if (isset($_GET['make_verify'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */

            $make_admin_user_id = $_GET['make_verify'];

            $sql_change_status = "UPDATE users SET verified = ? WHERE id = ?";

            if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                $verified_status_code = 1;
                $stmt_change_status->bind_param("ii", $verified_status_code, $make_admin_user_id);

                if ($stmt_change_status->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully verified user id " . $make_admin_user_id . ".</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }

        if (isset($_GET['remove_verify'])) {
            require_once "config.php";
            /** @var $mysqli mysqli */

            $make_admin_user_id = $_GET['remove_verify'];

            $sql_change_status = "UPDATE users SET verified = ? WHERE id = ?";

            if ($stmt_change_status = $mysqli->prepare($sql_change_status)) {
                $unverified_status_code = 0;
                $stmt_change_status->bind_param("ii", $unverified_status_code, $make_admin_user_id);

                if ($stmt_change_status->execute()) {
                    echo "<h4 class='font-weight-light text-success'> You have successfully unverified user id " . $make_admin_user_id . ".</h4>";
                } else {
                    echo $mysqli->error;
                }

            }
            $stmt_change_status->close();

        }

        ?>

        <h1 class="font-weight-light">For you AGM, you currently have the users registered:</h1>

        <?php

        require_once "config.php";
        /** @var $mysqli mysqli */

        // Get list of elections from the SQL table and create a table on the HTML page displaying the list of elections
        $sql_get_users = "SELECT id, username, realname, administrator, verified FROM users";
        if ($stmt_get_users = $mysqli->prepare($sql_get_users)) {
            if ($stmt_get_users->execute()) {
                $stmt_get_users->store_result();

                if ($stmt_get_users->num_rows > 0) {
                    echo "<table class='table table-hover mt-4'>";
                    echo "<thead class='thead-dark'>";
                    echo "<tr>";
                    echo "<th scope='col'>#</th>";
                    echo "<th scope='col'>Username</th>";
                    echo "<th scope='col'>Real Name</th>";
                    echo "<th scope='col'>Administrator</th>";
                    echo "<th scope='col'>Verify</th>";
                    echo "<th scope='col'>Delete</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $stmt_get_users->bind_result($r_user_id, $r_username, $r_realname, $r_admin, $r_verified);

                    while ($stmt_get_users->fetch()) {
                        echo "<tr>";
                        echo "<th scope='row'>" . $r_user_id . "</th>";
                        echo "<td>" . $r_username . "</td>";
                        echo "<td>" . $r_realname . "</td>";
                        echo "<td>";
                            switch ($r_admin) {
                                case 0:
                                    echo "<a type='button' class='btn btn-warning' onClick='save_scroll()' href = '?make_admin=" . $r_user_id . "' >Make Admin</a>";
                                    break;
                                case 1:
                                    echo "<a type='button' class='btn btn-danger' onClick='save_scroll()' href = '?remove_admin=" . $r_user_id . "' >Remove Admin</a>";
                                    break;
                            }
                        echo "</td>";
                        echo "<td>";
                            switch ($r_verified) {
                                case 0:
                                    echo "<a type='button' class='btn btn-success' onClick='save_scroll()' href = '?make_verify=" . $r_user_id . "' >Verify</a>";
                                    break;
                                case 1:
                                    echo "<a type='button' class='btn btn-danger' onClick='save_scroll()' href = '?remove_verify=" . $r_user_id . "' >Unverify</a>";
                                    break;
                            }
                        echo "</td>";
                        echo "<td><a type='button' class='btn btn-danger' onClick='save_scroll()' href = '?delete_user=" . $r_user_id . "' >Delete User</a></td>";

                    }
                    echo "</tbody>";
                    echo "</table>";

                } else {
                    echo "<h4 class='font-weight-light text-danger'> You have no users registered to your online system</h4>";

                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt_get_users->close();
        }

        $mysqli->close();

        ?>


    </div>
</main>

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

