<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="styles/main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
</head>
<body>
<h1>Jeremy's web application</h1>
<!--Navigation bar-->
<section class="navbar">
    <a onclick="document.getElementById('login').style.display='block'">Login</a>
    <a onclick="document.getElementById('signup').style.display='block'">Sign Up</a>
</section>
    <?php
    session_start();
//    $_SESSION['example'] = 1;
//    print_r($_SESSION);
//    Log in
    if (empty($_POST))
    {
        echo showLoginPopup();
        echo showSignUpPopup();
    }
    else
    {
//        showPOSTdata();
        $user = getUserCredentials();
        if (isset($user['badinput']))
        {
            echo ("<div class='popup-content' style='display: block; margin: unset'>something went wrong:" . $user['badinput'] . "</div>");
            echo showLoginPopup();
            echo showSignUpPopup();
        }
        elseif (isset($user['validinput']))
        {
            echo ("<div class='popup-content' style='display: block; margin: unset'>Logged in:" . $user['validinput']. "</div>");
            $_SESSION['username'] = $user['username'];
            header("refresh:1; url=mainPage.php");
        }
        else
        {
            echo ("<div class='popup-content' style='display: block; margin: unset'>hmmmm. An error that shouldn't be possible has occurred. Well done!</div>");
            echo showLoginPopup();
            echo showSignUpPopup();
        }
    }
    ?>

</body>
</html>



<?php
include 'configure.php';
$configure = new Config();

function getUserCredentials()
{
    $user = array();
    if (isset($_POST['password'])) {
        $user['username'] = $_POST['username'];
        $user['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user = loginUser($user);
    }
    else{
        if ($_POST['password1'] != $_POST['password2'])
        {
            $user = array();
            $user['badinput'] = "Passwords do not match";
            return $user;
        }

        $user['username'] = $_POST['username'];
        $user['name'] = $_POST['name'];
        $user['password'] = password_hash($_POST['password1'], PASSWORD_DEFAULT);
        if (strlen($user['password'])< 6)
        {
            $user = array();
            $user['badinput'] = "Password too short";
            return $user;
        }
        $user = registerUser($user);
    }
    return $user;

}

function registerUser($user)
{
    $pdo = accessDatabase();
    $userID = $user['username'];
    $userName = $user['name'];
    $userPass = $user['password'];
    $sql = "SELECT * FROM User WHERE user_ID=:userID";
    $sql_arr = ['userID'=>$userID];
    $query = accessSchema($sql, $sql_arr);
    if ($query->rowCount() > 0) {
        $user = array();
        $user['badinput'] = "User exists already";
        return $user;
    }
    $sql = "INSERT INTO `t11915jr`.User (user_ID, user_name, user_password) VALUES (:userId, :username, :userpassword);";
    $sql_arr = ['userId'=>&$userID, 'username'=>&$userName, 'userpassword'=>&$userPass];
    $query = accessSchema($sql, $sql_arr);
    $user['validinput'] = "User Created";
    return $user;
}

function loginUser($user)
{
    $userID = $user['username'];
    $userPass = $user['password'];
    $sql = "SELECT user_password FROM User WHERE user_ID=:userID";
    $sql_arr = ['userID'=>$userID];
    $query = accessSchema($sql, $sql_arr);
    if ($query->rowCount() == 0) {
        $user = array();
        $user['badinput'] = "User does not exist";
        return $user;
    }
    elseif ($query->rowCount() > 1) {
        $user = array();
        $user['badinput'] = "Error when accessing database; Multiple accounts found!";
        return $user;
    }
    elseif ($query->fetch() == $userPass)
    {
        $user = array();
        $user['badinput'] = "Incorrect Password entered!";
        return $user;
    }else{
        $user['validinput'] = "Credentials accepted";
        return $user;
    }
}

function showLoginPopup()
{
    return '
  <form id="login" class="popup-content" method="post" style="display: none">
        <div class="column_container" style="justify-content: space-between">
            <div>
                <h1 style="font-size: 50px; font-weight: 700">Login</h1>
            </div>
            <div>
                <!--Cancel button-->
                <button type="button" style="border-radius: 30px"
                        onclick="document.getElementById(\'login\').style.display=\'none\'">X</button>
            </div>
        </div>
        <label for="username">User ID</label>
        <input type="text" name="username" placeholder="Enter Username..." required>
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Enter Password..." required>
        <button type="submit" value="Register">Login</button>
    </form>
    ';
}

function showSignUpPopup()
{
    return '
    <form id="signup" class="popup-content" method="post" style="margin: 10% auto 20% auto; display: none">
        <div class="column_container" style="justify-content: space-between">
            <div>
                <h1 style="font-size: 50px">Sign Up</h1>
            </div>
            <div>
                <!--Cancel button-->
                <button type="button" style="border-radius: 30px; font-weight: 700"
                        onclick="document.getElementById(\'signup\').style.display=\'none\'">X</button>
            </div>
        </div>
        <label for="username">User ID</label>
        <input type="text" name="username" placeholder="Enter Username..." required>
        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Enter Name..." required>
        <label for="password1">Password</label>
        <input type="password" name="password1" placeholder="Enter Password..." required>
        <label for="password2">Repeat Password</label>
        <input type="password" name="password2" placeholder="Enter Again..." required>
        <button type="submit" >Login</button>
    </form>
    ';
}

function accessSchema($sql, $sqlstmt=[])
{
    $sql_stmt = $sqlstmt;
    try {
        $pdo = new pdo('mysql:host=dbhost.cs.man.ac.uk;dbname=t11915jr', 't11915jr', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($sql_stmt);
        return $stmt;
    }
    catch (PDOException $e)
    {
        echo "conncection failed" . $e->getMessage();
        return null;
    }
}

function showPOSTdata()
{
    foreach ($_POST as $key => $value)
    {
        echo ("<br>$key:$value");
    }
}

function accessDatabase()
{
    global $configure;
    try {
        $pdo = new pdo("mysql:host=dbhost.cs.man.ac.uk;dbname=t11915jr","t11915jr", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }
    catch (PDOException $e)
    {
        echo "Connection failed" . $e->getMessage();
        $pdo = null;
    }
    return $pdo;
}
?>
