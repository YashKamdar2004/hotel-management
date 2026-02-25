<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

session_start();

// Check if user is logged in
if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    echo 'not_logged_in';
    exit;
}

if(isset($_POST['update_profile']))
{
    $data = filteration($_POST);
    $user_id = $_SESSION['uId'];

    // Check if phone number is already used by another user
    $phone_check = select("SELECT * FROM `user_cred` WHERE `phonenum`=? AND `id`!=? LIMIT 1",
        [$data['phonenum'], $user_id], "si");

    if(mysqli_num_rows($phone_check) != 0){
        echo 'phone_already';
        exit;
    }

    // Handle profile image upload if provided
    $profile_img = null;
    if(isset($_FILES['profile']) && $_FILES['profile']['error'] == 0){
        $img = uploadUserImage($_FILES['profile']);

        if($img == 'inv_img'){
            echo 'inv_img'; 
            exit;
        }
        else if($img == 'upd_failed'){
            echo 'upd_failed';
            exit;
        }

        // Delete old profile image
        $old_img_res = select("SELECT `profile` FROM `user_cred` WHERE `id`=?", [$user_id], "i");
        $old_img = mysqli_fetch_assoc($old_img_res)['profile'];
        
        if($old_img != 'default.jpg'){
            deleteImage($old_img, USERS_FOLDER);
        }

        $profile_img = $img;
    }

    // Update user data
    if($profile_img != null){
        $query = "UPDATE `user_cred` SET `name`=?, `phonenum`=?, `address`=?, `profile`=? WHERE `id`=?";
        $values = [$data['name'], $data['phonenum'], $data['address'], $profile_img, $user_id];
        $result = update($query, $values, 'ssssi');

        // Update session profile picture
        if($result){
            $_SESSION['uPic'] = $profile_img;
        }
    } else {
        $query = "UPDATE `user_cred` SET `name`=?, `phonenum`=?, `address`=? WHERE `id`=?";
        $values = [$data['name'], $data['phonenum'], $data['address'], $user_id];
        $result = update($query, $values, 'sssi');
    }

    // Update session name
    if($result){
        $_SESSION['uName'] = $data['name'];
        $_SESSION['uPhone'] = $data['phonenum'];
        echo 1;
    } else {
        echo 0;
    }
}

?>
