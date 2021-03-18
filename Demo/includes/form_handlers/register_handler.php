<?php
//Declaring vars to preven errors
$fname = ""; //First name
$lname = ""; //Last name
$em = ""; //email
$em2 = ""; //email2
$password = ""; //password
$password2 = ""; //password2
$data = ""; //Sign up data
$error_array = array(); // Holds error messages

if (isset($_POST['register_button'])) {
    //Registeration form values
    $fname = strip_tags($_POST['reg_fname']); //Remove html tags
    $fname = str_replace(' ', '', $fname); //Remove spaces
    $fname = ucfirst(strtolower($fname)); //Uppercase first latter
    $_SESSION['reg_fname'] = $fname; //Store first name into session varaible 

    $lname = strip_tags($_POST['reg_lname']); //Remove html tags
    $lname = str_replace(' ', '', $lname); //Remove spaces
    $lname = ucfirst(strtolower($lname)); //Uppercase first latter    $em = strip_tags($_POST['reg_fname']);
    $_SESSION['reg_lname'] = $lname; //Store last name into session varaible 


    $em = strip_tags($_POST['reg_email']); //Remove html tags
    $em = str_replace(' ', '', $em); //Remove spaces
    $em = ucfirst(strtolower($em)); //Uppercase first latter    $em = strip_tags($_POST['reg_fname']);
    $_SESSION['reg_email'] = $em; //Store email  into session varaible 

    $em2 = strip_tags($_POST['reg_email2']); //Remove html tags
    $em2 = str_replace(' ', '', $em2); //Remove spaces
    $em2 = ucfirst(strtolower($em2)); //Uppercase first latter    $em = strip_tags($_POST['reg_fname']);
    $_SESSION['reg_email2'] = $em2; //Store email2  into session varaible 


    $password = strip_tags($_POST['reg_password']); //Remove html tags
    //$_SESSION['reg_password'] = $password; //Store password  into session varaible 
    $password2 = strip_tags($_POST['reg_password2']); //Remove html tags
    //$_SESSION['reg_password2'] = $password2; //Store password2  into session varaible 

    $data = date('Y-m-g'); // Get the current date


    if ($em == $em2) {
        //check if emails is in a valid format
        if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
            $em = filter_var($em, FILTER_VALIDATE_EMAIL);

            //Check if email already exist
            $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

            //Count number of rows returned
            $num_rows = mysqli_num_rows($e_check);
            if ($num_rows > 0) {
                array_push($error_array, "Email already in use<br>");
            }
        } else {
            array_push($error_array, "Invalid format <br>");
        }
    } else {
        array_push($error_array, "Emails don't match <br>");
    }

    if (strlen($fname) > 25 || strlen($fname) < 2) {
        array_push($error_array, "Your first name most be between 2 and 25 characters <br>");
    }
    if (strlen($lname) > 25 || strlen($lname) < 2) {
        array_push($error_array, "Your last name most be between 2 and 25 characters <br>");
    }
    if ($password != $password2)
        array_push($error_array, "Your passwords do not matched <br>");
    else if (preg_match('/[^A-Za-z0-9]/', $password))
        array_push($error_array, "Your password can only contain english characters or numbers <br>");
    else if (strlen($password) > 30 || strlen($password) < 5)
        array_push($error_array, "Your password most be between 5 and 30 characters <br>");



    if (empty($error_array)) {
        $password = md5($password); //Encript assword before sending to database

        //Genarate username by concatenating first name and last name 
        $username = strtolower($fname . "_" . $lname);
        $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
        $i = 0;
        //If username exisit add number to username
        while (mysqli_num_rows($check_username_query) != 0) {
            $i++; //Add 1 to i
            $temp = $username . "_" . $i;
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$temp'");
        }
        if ($i != 0)
            $username = $username . "_" . $i;

        //Profile picture assigmrnt
        $rand = rand(1, 2); //Random number between 1 and 2 
        if ($rand == 1)
            $profile_pic = "./assets/images/profile_pics/defaults/head_deep_blue.jpg";
        else
            $profile_pic = "./assets/images/profile_pics/defaults/head_emerald.png";
        $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$data', '$profile_pic', '0', '0', 'no', ',')");

        array_push($error_array, "<span style='color: #14C800'> You signed up! Goahead and login! </span><br>");
        $_SESSION['reg_fname'] = "";
        $_SESSION['reg_lname'] = "";
        $_SESSION['reg_email'] = "";
        $_SESSION['reg_email2'] = "";
    }
}
