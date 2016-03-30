<?php
session_start();
if (isset($_SESSION['name'])) {
    // store the username in a variable
    $username = $_SESSION['name'];
    // connect to the database
    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // store user information in a variable
    try {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $db->query($query);
        $user = $result->fetch();
        $user_id = $user['id'];
        // store information on all animals in the system
        $query = "SELECT * FROM animals";
        $result = $db->query($query);
        include_once('display_animal.php');
        // redirect non staff users to standard home page
        if ($user['staff'] == 0) {
            header("Location: home.php");
        }
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
    <title>Aston Animal Sanctuary: All Animals</title>
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
                <a href="staff_animals.php">Animals</a>
            </li>
            <li>
                <a href="staff_requests.php">Adoption Requests</a>
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
            <h3>Input Details Below</h3>
            <form action="add_animal.php" method="post" enctype="multipart/form-data">
                Name:
                <input type="text" name="name" size="15" maxlength="32" />
                <br />
                Date of Birth:
                <input type="date" name="birthday" />
                <br />
                Description:
                <textarea name="description" rows="1" cols="1" maxlength="2000"></textarea>
                <br />                
                Type:
                <input type="text" name="type" size="15" maxlength="32" />
                <br />
                Picture (leave blank to use the default picture):
                <input type="file" name="picture">
                <br />
                <input class = "button" type="submit" name="submit" value="Submit" />
                <input type="hidden" name="submitted" value="TRUE" />
            </form>
        </div>
        <?php
            if (isset($_POST['submitted'])) {
                // set target to the default picture
                $target = "pictures/no_avatar.png";
                // boolean to use to check if an error has been thrown
                $error_thrown = false;
                // check the file if a user has uploaded one
                if (basename($_FILES['picture']['name']) != null) {
                    // set the upload location for the picture
                    $target = "pictures/";
                    $target = $target . basename($_FILES['picture']['name']);
                    // boolean to use to check if the picture is valid
                    $picture_valid = true;
                    // store the file type of the image
                    $file_type = pathinfo($target, PATHINFO_EXTENSION);
                    // Check if image file is a fake
                    $is_image = getimagesize($_FILES['picture']['tmp_name']);
                    if($is_image === false && $error_thrown == false) {
                        echo "<p class='error'>File is not an image.</p>";
                        $picture_valid = false;
                        $error_thrown = true;
                    }
                    // Check if file name exists
                    if (file_exists($target) && $error_thrown == false) {
                        echo "<p class='error'>File already exists.</p>";
                        $picture_valid = false;
                        $error_thrown = true;
                    }
                    // Limit file upload size to 1000000kB
                    if ($_FILES['picture']['size'] >= 1000000 && $error_thrown == false) {
                        echo "<p class='error'>File too large. Max size is 4000kB</p>";
                        $picture_valid = false;
                        $error_thrown = true;
                    }
                    // Allow certain file formats
                    if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg"
                        && $file_type != "gif" && $error_thrown == false) {
                        echo "<p class='error'>File type not valid. Only jpg, png, jpeg, and gif file types are accepted.</p>";
                        $picture_valid = false;
                        $error_thrown = true;
                    }
                    if ($picture_valid == true) {
                        if (move_uploaded_file($_FILES['picture']['tmp_name'], $target)) {
                            echo "<p>Picture uploaded successfully.</p>";
                        } else {
                            echo "<p class='error'>Error uploading file. Try again later.</p>";
                            $error_thrown = true;
                        }
                    }
                }
                // store animal information from POST into variables
                $name = $_POST['name'];
                $birthday = $_POST['birthday'];
                $description = $_POST['description'];
                $type = $_POST['type'];
                // check if any field was left empty
                if ($name == null && $error_thrown == false) {
                    echo "<p class='error'>Enter a name for the animal.</p>";
                    $error_thrown = true;
                }
                if ($birthday == null && $error_thrown == false) {
                    echo "<p class='error'>Choose a birthday for the animal.</p>";
                    $error_thrown = true;
                }
                if ($description == null && $error_thrown == false) {
                    echo "<p class='error'>Enter a description for the animal</p>";
                    $error_thrown = true;
                }
                if ($type == null) {
                    echo "<p class='error'>Enter a type for the animal</p>";
                }
                if ($error_thrown == false) {
                    try {
                        $query = "INSERT INTO animals (id, name, date_of_birth, description, photo, available, type) VALUES (NULL, '$name', '$birthday', '$description', '$target', '1', '$type')";
                        $db->exec($query);
                        echo "<p>Animal uploaded to database</p>";
                        // update owns table so animal is owned by the owner
                        $last_id = $db->lastInsertId();
                        $query = "INSERT INTO owns (user_id, animal_id) VALUES ('1', '$last_id')";
                        $db->exec($query);
                    } catch (PDOException $e) {
                        echo "<p class='error'> Database Error Occurred: . $e->getMessage()</p>";
                    }
                }
            }
        ?>
    </div>
</div>
</body>
</html>
