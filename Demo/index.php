<?php
include("includes/header.php");
//include("includes/classes/User.php");
//include("includes/classes/Post.php");



if (isset($_POST['post'])) {
    $uploadThisFile = true;
    $imageName = "";
    $errors = array();
    $imageArr = array();
    $extension = array("jpeg", "jpg", "png", "gif", "JPEG", "JPG", "PNG", "GIF");

    $bytes = 1024;
    $allowedKB = 100;
    $totalBytes = $allowedKB * $bytes;
    /*  
    if(isset($_FILES["fileToUpload"])==false)
    {
        echo "<b>Please, Select the files to upload!!!</b>";
        return;
    }
     */

    foreach ($_FILES["fileToUpload"]["tmp_name"] as $key => $tmp_name) {
        $uploadThisFile = true;
        $targetDir = "assets/images/posts/";
        $file_name = $_FILES["fileToUpload"]["name"][$key];
        $imageName = $targetDir . uniqid() . basename($file_name);
        $file_tmp = $_FILES["fileToUpload"]["tmp_name"][$key];

        $ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (!in_array(strtolower($ext), $extension)) {
            array_push($errors, "File type is invalid. Name:- " . $file_name);
            $uploadThisFile = false;
        }

        if ($_FILES["fileToUpload"]["size"][$key] > $totalBytes) {
            array_push($errors, "File size must be less than 100KB. Name:- " . $file_name);
            $uploadThisFile = false;
        }

        if (file_exists("Upload/" . $_FILES["fileToUpload"]["name"][$key])) {
            array_push($errors, "File is already exist. Name:- " . $file_name);
            $uploadThisFile = false;
        }

        if ($uploadThisFile) {
            $filename = basename($file_name, $ext);
            $newFileName = $filename . $ext;
            //move_uploaded_file($_FILES['fileToUpload']['tmp_name']);
            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$key], $imageName);
            array_push($imageArr, $imageName);
            // $query = "INSERT INTO UserFiles(FilePath, FileName) VALUES('Upload','" . $newFileName . "')";

            //mysqli_query($conn, $query);
        }
    }

    //mysqli_close($conn);
    $count = count($errors);
    if ($count > 0) {
        $imageArr = array();
    }
    $post = new Post($con, $userLoggedIn);
    $is_public = 1;
    if (isset($_POST['private'])) {
        $is_public = 0;
    }
    $post->submitPost($_POST['post_text'], 'none', $imageArr, $is_public);
}
$imageArr = array();
/*
    $uploadOk = 1;
    $imageArr = array();
    $imageName = $_FILES['fileToUpload']['name'];
    $errorMessage = "";

    if ($imageName != "") {
        $targetDir = "assets/images/posts/";
        $imageName = $targetDir . uniqid() . basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

        if ($_FILES['fileToUpload']['size'] > 10000000) {
            $errorMessage = "Sorry your file is too large";
            $uploadOk = 0;
        }

        if (strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
            $errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
            $uploadOk = 0;
        }

        if ($uploadOk) {

            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
                //image uploaded okay
            } else {
                //image did not upload
                $uploadOk = 0;
            }
        }
    }

    if ($uploadOk) {
        $post = new Post($con, $userLoggedIn);
        $is_public = 1;
        if (isset($_POST['private'])) {
            $is_public = 0;
        }
        $post->submitPost($_POST['post_text'], 'none', $imageName, $is_public);
    } else {
        echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
    }
    
}
*/


?>


<div class="user_details column">
    <a href="<?php echo $userLoggedIn ?>"><img src="<?php echo $user['profile_pic']; ?>"></a>
    <div class="user_details_left_right">
        <a href="<?php echo $userLoggedIn ?>">
            <?php
            echo $user['first_name'] . " " . $user['last_name']

            ?>

        </a>
        <br>
        <?php

        echo "Posts: " . $user['num_posts'] . "<br>";
        echo "Likes: " . $user['num_likes'];
        ?>

    </div>
</div>

<div class="main_column column">
    <form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="fileToUpload[]" id="fileToUpload" multiple="multiple">
        <textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
        <input type="submit" name="post" id="post_button" value="Post">
        <br>
        <h8>Private</h8>
        <input type="checkbox" name="private" id="post_private">
        <hr>

    </form>

    <div class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif">
</div>

<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function() {

        $('#loading').show();

        //Original ajax request for loading first post
        $.ajax({
            url: "./includes/handlers/ajax_load_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn,
            cache: false,

            success: function(data) {
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });

        $(window).scroll(function() {
            var height = $('.posts_area').height(); //Div containing posts 
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                $('#loading').show();


                var ajaxReq = $.ajax({
                    url: "./includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache: false,

                    success: function(response) {
                        $('.posts_area').find('.nextPage').remove(); //Remove current .nextPage
                        $('.posts_area').find('.noMorePosts').remove(); //Remove current .nextPage

                        $('#loading').hide();
                        $('.posts_area').append(response);
                    }
                });

            } // End if 
            return false;
        }); // End (window).scroll(function())
    });
</script>
</div>


</body>

</html>