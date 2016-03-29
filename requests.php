<?php
session_start();
if (isset($_SESSION['name'])) {
    // store the username in a variable
    $username = $_SESSION['name'];
    // connect to the database
    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // store user information in a variable
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $db->query($query);
    $user = $result->fetch();
    $user_id = $user['id'];

    include_once('display_animal.php');
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Aston Animal Sanctuary</title>
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
        <link rel="stylesheet" type="text/css" href="stylesheet.css" />
    </head>
    <body>
    <div id="main">
        <div id = "nav-bar">
            <ul id="nav">
                <li>
                    <a href="home.php">Home</a>
                </li>
                <li>
                    <a href="animals.php">Available Animals</a>
                </li>
                <li>
                    <a href="requests.php">Adoption Requests</a>
                </li>
                <li id="search">
                    <form action="search.php" method="get">
                        <input type="text" name="search_bar" id="search_bar">
                    </form>
                </li>
                <li id="greeting">
                    <a href="#">Hello, <?php echo $username; ?></a>
                </li>
                <li id="logout">
                    <a href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
        <div id="inner-center">
            <h3>Your Successful Adoption Requests</h3>
            <div class="animals">
                <?php
                // display successful adoption requests from the user
                $query = "SELECT * FROM adoption_request WHERE user_id = $user_id AND approved=1";
                $result = $db->query($query);
                $has_approved_adoption_request = false;
                while ($animal = $result->fetch()) {
                    print_animal_info($animal, $db, false);
                    $has_approved_adoption_request = true;
                }
                if (!$has_approved_adoption_request) {
                    echo "<p class=error>You have no approved adoption requests</p>";
                }
                ?>
            </div>
            <h3>Your Pending Adoption Requests</h3>
            <div class="animals">
                <?php
                // display open adoption requests from the user
                $query = "SELECT * FROM adoption_request WHERE user_id = $user_id AND approved=0";
                $result = $db->query($query);
                $has_adoption_request = false;
                while ($animal = $result->fetch()) {
                    print_animal_info($animal, $db, false);
                    $has_adoption_request = true;
                }
                if (!$has_adoption_request) {
                    echo "<p class=error>You have no pending adoption requests</p>";
                }
                ?>
            </div>
            <h3>Your Denied Adoption Requests</h3>
            <div class="animals">
                <?php
                // display denied adoption requests from the user
                $query = "SELECT * FROM adoption_request WHERE user_id = $user_id AND approved=2";
                $result = $db->query($query);
                $has_denied_adoption_request = false;
                while ($animal = $result->fetch()) {
                    print_animal_info($animal, $db, false);
                    $has_denied_adoption_request = true;
                }
                if (!$has_denied_adoption_request) {
                    echo "<p class=error>You have no denied adoption requests</p>";
                }
                ?>
            </div>
        </div>
    </body>
</html>