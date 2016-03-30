<?php
session_start();
if (isset($_SESSION['name'])) {
    // store the username in a variable
    $username = $_SESSION['name'];
    // connect to the database
    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        // store user information in a variable
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $db->query($query);
        $user = $result->fetch();
        $user_id = $user['id'];
    } catch (PDOException $e) {
        echo "<p class='error'> Database Error Occurred: . $e->getMessage()</p>";
    }
    // redirect non staff users to standard home page
    if ($user['staff'] == 0) {
        header("Location: home.php");
    }
    $animal_id = null;
    // don't execute the code in the if statement if a form has been submitted
    if ($_GET['edit'] != null) {
        // information on the animal to edit
        $animal_id = $_GET['edit'];
        $query = "SELECT * FROM animals WHERE id=$animal_id";
        $result = $db->query($query);
        $animal = $result->fetch();
        $name = $animal['name'];
        $birthday = $animal['date_of_birth'];
        $description = $animal['description'];
        $type = $animal['type'];
    }
    include_once('display_animal.php');
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
            <form action="edit_animal.php" method="post" enctype="multipart/form-data">
                Name:
                <input type="text" name="name" size="15" maxlength="32" value="<?php echo $name; ?>" />
                <br />
                Date of Birth:
                <input type="date" name="birthday" value="<?php echo $birthday?>"/>
                <br />
                Description:
                <textarea name="description" rows="1" cols="1" maxlength="2000"><?php echo $description; ?></textarea>
                <br />
                Type:
                <input type="text" name="type" size="15" maxlength="32" value="<?php echo $type; ?>" />
                <br />
                Picture (Leave blank if you want to use the old picture):
                <input type="file" name="picture">
                <br />
                <input class = "button" type="submit" name="submit" value="Submit" />
                <!--Set the animal id as the submitted value-->
                <input type="hidden" name="submitted" value="<?php echo $animal_id ?>" />
            </form>
        </div>
        <?php
        if (isset($_POST['submitted'])) {
            // set target to null if a new picture was not uploaded
            $picture = null;
            // boolean to use to check if an error has been thrown
            $error_thrown = false;
            // check the file if a user has uploaded one
            if (basename($_FILES['picture']['name']) != null) {
                // set the upload location for the picture
                $picture = "pictures/";
                $picture = $picture . basename($_FILES['picture']['name']);
                // boolean to use to check if the picture is valid
                $picture_valid = true;
                // store the file type of the image
                $file_type = pathinfo($picture, PATHINFO_EXTENSION);
                // Check if image file is a fake
                $is_image = getimagesize($_FILES['picture']['tmp_name']);
                if($is_image === false && $error_thrown == false) {
                    echo "<p class='error'>File is not an image.</p>";
                    $picture_valid = false;
                    $error_thrown = true;
                }
                // Check if file name exists
                if (file_exists($picture) && $error_thrown == false) {
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
                // Allow certain file formats (must be lower case)
                if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg"
                    && $file_type != "gif" && $error_thrown == false) {
                    echo "<p class='error'>File type not valid. Only jpg, png, jpeg, and gif file types are accepted.</p>";
                    $picture_valid = false;
                    $error_thrown = true;
                }
                if ($picture_valid == true) {
                    if (move_uploaded_file($_FILES['picture']['tmp_name'], $picture)) {
                        echo "<p>Picture uploaded successfully.</p>";
                    } else {
                        echo "<p class='error'>Error uploading file. Try again later.</p>";
                        $error_thrown = true;
                    }
                }
            }
            // updated (or original) information
            $name = $_POST['name'];
            $birthday = $_POST['birthday'];
            $description = $_POST['description'];
            $type = $_POST['type'];
            $animal_id = $_POST['submitted'];
            if ($error_thrown == false) {
                $query = "UPDATE animals SET name='$name', date_of_birth='$birthday', description='$description', type='$type' WHERE id='$animal_id'";
                $db->exec($query);
                if ($picture != null) {
                    // use the specified picture if one was uploaded
                    $query = "UPDATE animals SET photo='$picture' WHERE id='$animal_id'";
                    $db->exec($query);
                }
            }
            if ($error_thrown == false) {
                // take user back to animals page
                header("Location: staff_animals.php");
            }
        }
        ?>
    </div>
</div>
</body>
</html>
