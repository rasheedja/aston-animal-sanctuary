<?php
/**
 * @param $animal SQL result of an adoption request
 * @param $db SQL database
 * @param $button_text_1 Text for the first button
 * @param $button_text_2 Text for the second button
 * @param $action Page to load when a button is clicked
 * @param $method Method to use when a button is clicked
 * @param $value Value to be passed when a button is clicked
 * @param $user_id User id used to show the ID of an Adopter
 */
function print_animal_info($animal, $db, $button_text_1, $button_text_2, $action, $method, $value, $user_id) {
    $animal_id = $animal['animal_id'];
    $_GET['animal_id'] = $animal_id;
    try {
        $query = "SELECT * FROM animals WHERE id = $animal_id";
        $result = $db->query($query);
    } catch (PDOException $e) {
        echo "<p class='error'> Database Error Occurred: . $e->getMessage()</p>";
    }
    while ($animal_info = $result -> fetch()) {
        parse_animal_info($animal_info, $button_text_1, $button_text_2, $action, $method, $value, $user_id);
    }
}

/**
 * @param $animal_info SQL result of an animal
 * @param $button_text_1 Text for the first button
 * @param $button_text_2 Text for the second button
 * @param $action Page to load when a button is clicked
 * @param $method Method to use when a button is clicked
 * @param $value Value to be passed when a button is clicked
 * @param $user_id User id used to show the ID of an Adopter
 */
function parse_animal_info($animal_info, $button_text_1, $button_text_2, $action, $method, $value, $user_id) {
    $name = $animal_info['name'];
    $dob = $animal_info['date_of_birth'];
    $age = date_diff(date_create($dob), date_create('today'))->y;
    $description = $animal_info['description'];
    $photo = $animal_info['photo'];
    $animal_id = $animal_info['id'];
    echo "<br />";
    echo "<img class='animal-picture' src=$photo />";
    echo "<ul class='animal-info'>";
    echo "<li>$name</li>";
    echo "<li>$age</li>";
    echo "<li>$dob</li>";
    echo "<li>$description</li>";
    if ($user_id != null) {
        echo "<li>Adopter's ID: $user_id</li>";
    }
    print_buttons($animal_id, $button_text_1, $button_text_2, $action, $method, $value);
    echo "</ul>";
}

/**
 * @param $animal_id ID for an animal which is set to the value of a button if no value has been passed
 * @param $button_text_1 Text for the first button
 * @param $button_text_2 Text for the second button
 * @param $action Page to load when a button is clicked
 * @param $method Method to use when a button is clicked
 * @param $value Value to be passed when a button is clicked
 */
function print_buttons($animal_id, $button_text_1, $button_text_2, $action, $method, $value) {
    if ($value == null) {
        $value = $animal_id;
    }
    if ($button_text_1 != null) {
        $name = strtolower($button_text_1);
        echo "<li><form action=$action method=$method><button class='button' name=$name type='submit' value=$value>$button_text_1</button></form></li>";
    }
    if ($button_text_2 != null) {
        $name = strtolower($button_text_2);
        echo "<li><form action=$action method=$method><button class='button' name=$name type='submit' value=$value>$button_text_2</button></form></li>";
    }
}
?>