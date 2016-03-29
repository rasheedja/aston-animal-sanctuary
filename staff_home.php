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
    // redirect non staff users to standard home page
    if ($user['staff'] == 0) {
        header("Location: home.php");
    }
    include_once('display_animal.php');
    //DELETE FROM owns WHERE user_id = 1 AND animal_id = 1
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
                <a href="staff_home.php">Home</a>
            </li>
            <li>
                <a href="staff_animals.php">Available Animals</a>
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
        <div class="animals">
            <h3>Available Animals</h3>
            <?php
            // display the information on the animals owned by the user
            $query = "SELECT * FROM owns WHERE user_id = $user_id";
            $result = $db->query($query);
            $has_animal = false;
            while ($animal = $result->fetch()) {
                print_animal_info($animal, $db, false);
                $has_animal = true;
            }
            if (!$has_animal) {
                echo "<p class=error>You have no animals</p>";
            }
            ?>
        </div>
        <div class="animals">
            <h3>Adoption Requests</h3>
            <?php
            // display all pending adoption requests
            $query = "SELECT * FROM adoption_request WHERE approved=0";
            $result = $db->query($query);
            $has_adoption_request = false;
            while ($animal = $result->fetch()) {;
                $adoption_id = $animal['adoption_id'];
                $adopter = $animal['user_id'];
                print_animal_info($animal, $db, "Approve", "Deny", "handle_request.php", "get", $adoption_id, $adopter);
                $has_adoption_request = true;
            }
            if (!$has_adoption_request) {
                echo "<p class=error>There are no adoption requests at the moment</p>";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>