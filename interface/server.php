<?php

include('config.php');
session_start();
// variable declaration
$username = "";
$mobile = "";
$email = "";
$errors = array();

// call the register() function if register_btn is clicked
if(isset($_POST['register_btn'])){
    register();
}
// call the login() function if login_btn is clicked
if(isset($_POST['login_btn'])){
    login();
}
// call the sendmail() function if recpassword_btn is clicked
if(isset($_POST['recpassword_btn'])){
    sendmail();
}





// LOGIN USER
function login(){
    global $db, $username, $errors;

    // grap from values
    $emailid = e($_POST['emailid']);
    $password = e($_POST['password']);
echo $emailid,$password;
    // make sure form is filled properly
    if(empty($emailid)){
        array_push($errors, "E-mail id is required");
    }
    if(empty($password)){
        array_push($errors, "Password is required");
    }

    // attempt login if no errors on form
    if(count($errors)==0){
        $password = md5($password);

        $query = "SELECT * FROM registation WHERE emailid='$emailid' AND password='$password' LIMIT 1";
        $results = mysqli_query($db, $query);

        if (mysqli_num_rows($results) == 1){
            // user found
            // check if user is admin or user
            $logged_in_user = mysqli_fetch_assoc($results);
            if($logged_in_user['mrole'] == 'Admin'){

                $_SESSION['user'] = $logged_in_user;
                $_SESSION['success'] = "You are now logged in";
                $_SESSION['username'] = $logged_in_user['username'];
                header('location: Admin/Dashbord.php');
            }else{
                $_SESSION['id'] = session_id();
                $_SESSION['user'] = $logged_in_user;
                $_SESSION['success'] = "You are now logged in";
                $_SESSION['userid'] = $logged_in_user['userid'];
                $_SESSION['username'] = $logged_in_user['username'];
                $_SESSION['cerstatus'] = $logged_in_user['cerstatus'];
                $_SESSION['proup'] = $logged_in_user['proup'];
                // Useronline();
                header('location: User/Dashbord.php');
            }
            }else{
                array_push($errors, "Invalid email id or password");
            }
        }
    }






// REGISTER USER
function register(){

    global $db, $errors;
    $username = e($_POST['username']);
    $email = e($_POST['email']);
    $mobile = e($_POST['mobile']);
    $password_1 = e($_POST['password_1']);
    $password_2 = e($_POST['password_2']);

    
   // from validation: ensure that the values are filled properly
         if(empty($username)){
            array_push($errors, "Username is required");
         }
         if(empty($mobile)){
            array_push($errors,"Mobile no. is required");
         }else{
            usermobexists($mobile);
         }

         if(!preg_match('/^\d{10}+$/', $mobile)){
            array_push($errors, "Invalid mobile number");
         }
         if(empty($email)){
            array_push($errors, "Email is required");
         }else{
            userexists($email);
         }

         if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            // $emailErr = "Invalid email format";
            array_push($errors, "Invalid email");
         }
         if(empty($password_1)){
            array_push($errors, "Password is required");
         }
         if($password_1 != $password_2){
            array_push($errors, "The two passwords do not match");
         }
         if(empty($sponsorid)){
            $sponsorid=1001;
         }

    if(count($errors)==0){
        $password = md5($password_1);
        $maxid = Userid();

    $query = "INSERT INTO registation (username, userid, sponsorid, mobileno, emailid, mrole, password, regdate)
                 VALUE('$username', '$maxid', '$sponsorid', '$mobile', '$email', '$password', NOW())";
         mysqli_query($db, $query);
         $_SESSION['username'] = $username;
         $_SESSION['success'] = "You are now logged in";
           //welcomeemail();
        header('location: thankyou.php');
    //header('location: User/UserDashbord.php');
    }
    // echo $username, $email, $mobile, $password_1, $password_2, $sponsorid;
}


// return user exists or not - made by me
function Userexists($email){
    global $db, $errors;
    $query = "SELECT emailid FROM registation WHERE emailid-'$email'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($results);
    if($email==$row["emailid"]){
        array_push($errors, "Email already registered".$row["emailid"]);
    }
}

// return user mobile ni exists or not - made by me
function Usermobexists($mobile){

    global $db, $errors;
    $query = "SELECT mobileno FROM registation WHERE mobileno-'$mobile'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($results);
    if($mobile==$row["mobileno"]){
        array_push($errors, "Mobile no. already registered".$row["mobileno"]);
    }
}

// return uniqe user id made by me
function Userid(){

    global $db;
    $query = "SELECT MAX(`userid`) AS maximum FROM `registation`";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($results);
    // echo "user all ready registered".$row["maximum"];
    $row1=$row["maximum"]+1;
    return $row1;
}



//--------------------------------//
function isLoggedIn(){
    if(isset($_SESSION['user'])){
        return true;
    }else{
        return false;
    }
}

// check user role is amin
function isAdmin(){
    if(isset($_SESSION['user']) && $_SESSION['user']['mrole']=='Admin'){
        return true;
    }else{
        return false;
    }
}

// check user role is user
function isUser(){
    if(isset($_SESSION['user']) && $_SESSION['user']['mrole']=='User'){
        return true;
    }else{
        return false;
    }
}

// escape string
function e($val)
{
		global $db;
		return mysqli_real_escape_string($db, trim($val));
}
function display_error() {
		global $errors;

		if (count($errors) > 0){
			echo '<div class="alert alert-info">';
				foreach ($errors as $error){
					echo $error .'<br>';
				}
			echo '</div>';
		}
	}

?>