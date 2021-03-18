<?php
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

//If query contains an underscor, assume user is searching for usernames
if (strpos($query, '_') !== false) {
    $users_returned_query = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
}    //If there are 2 words assume thet are first and last name 
else if (count($names) == 2) {
    $users_returned_query = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
} //If query has one word only only search first names or last names
else {
    $users_returned_query = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");
}

if ($query != "") {
    while ($row = mysqli_fetch_array($users_returned_query)) {
        $user = new User($con, $userLoggedIn);
        if ($row['username'] != $userLoggedIn)
            $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
        else
            $mutual_friends = "";

        echo "<div class='resultDisplay'>
                <a href='" . $row['username'] . "' style='color: #1485BD'>
                    <div class='liveSearchProfilePic'>
                        <img src='" . $row['profile_pic'] . "'>
                    </div>

                    <div class='liveSearchText'>
                        " . $row['first_name'] . " " . $row['last_name'] . "
                        <p>" . $row['username'] . "</p>
                        <p id='grey'>" . $mutual_friends . "</p>
                    </div>
                </a>
            </div>";
    }
}
