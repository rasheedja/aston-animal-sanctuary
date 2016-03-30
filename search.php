<?php
session_start();
if (isset($_SESSION['name'])) {
    // store the username in a variable
    $username = $_SESSION['name'];
    // connect to the database
    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    include_once('display_animal.php');
    // store the search query in a variable
    $search_query = $_GET["search_bar"];
    // check if search query is a number
    if (is_numeric($search_query)) {
        // store the age (or id) in a variable
        $age = $search_query;
        // number of years to subtract from the date;
        $earliest = $age + 1;
        $latest = $earliest - 1;
        // find the earliest and latest possible date of birth
        $earliest_date_of_birth = strtotime("-$earliest year", time());
        $latest_date_of_birth = strtotime("+1 year", $earliest_date_of_birth);
        $earliest_date_of_birth = date("Y-m-d", $earliest_date_of_birth);
        $latest_date_of_birth = date("Y-m-d", $latest_date_of_birth);
        $query = "SELECT * FROM animals WHERE available=1 AND date_of_birth<='$latest_date_of_birth' AND date_of_birth>='$earliest_date_of_birth' OR id=$age";
    } else {
        // search for animals with a name or type that was searched for
        $query = "SELECT * FROM animals WHERE available=1 AND name LIKE '%$search_query%' OR type LIKE '%$search_query%'";
    }
    try {
        $result = $db->query($query);
        // check and store whether a user is a staff member or not
        $query_user = "SELECT * FROM users WHERE username = '$username'";
        $result_user = $db->query($query_user);
        $user = $result_user->fetch();
        $user_id = $user['id'];
        $staff = $user['staff'] == 1;
    } catch (PDOException $e) {
        echo "<p class='error'> Database Error Occurred: . $e->getMessage()</p>";
    }

} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Aston Animal Sanctuary: <?php echo $search_query ?></title>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
    <link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body>
    <div id="main">
        <div id = "nav-bar">
            <ul id="nav">
                <!--Use the correct links depending on whether the user is a staff member or not-->
                <li>
                    <a href=<?php if($staff) {echo "staff_home.php";} else {echo "home.php";} ?>>Home</a>
                </li>
                <li>
                    <a href=<?php if($staff) {echo "staff_animals.php";} else {echo "animals.php";} ?>><?php if(!$staff) {echo "Available ";}?>Animals</a>
                </li>
                <li>
                    <a href=<?php if($staff) {echo "staff_requests.php";} else {echo "requests.php";}?>>Adoption Requests</a>
                </li>
                <li id="search">
                    <form action="search.php" method="get">
                        <input type="search" name="search_bar" id="search_bar">
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
                <h3>Search Results</h3>
                <?php
                // display information on animals that meet the search query
                $search_found = false;
                while ($animal_info = $result -> fetch()) {
                    if ($staff) {
                        parse_animal_info($animal_info);
                        $search_found = true;
                    } else {
                        parse_animal_info($animal_info, "Adopt", null, "make_request.php", "get");
                        $search_found = true;
                    }
                }
                if (!$search_found) {
                    echo "<p class='error'>$search_query returned no results</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>