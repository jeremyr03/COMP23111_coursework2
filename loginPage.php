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
//    Log in
    if (empty($_POST))
    {
        echo showLoginPopup();
        echo showSignUpPopup();
    }
    else
    {
        showPOSTdata();
        $user = getUserCredentials();
        if (isset($user['badinput']))
        {
            echo ("<br>something went wrong:<br>" . $user['badinput']);
            echo showLoginPopup();
            echo showSignUpPopup();
        }
        elseif (isset($user['validinput']))
        {
            echo ("<br>Logged in:<br>" . $user['validinput']);
            header("refresh:5; url=mainPage.php");
        }
        else
        {
            echo ("<br>hmmmm");
        }
    }
    ?>

</body>
</html>



<?php
include 'configure.php';
$configure = new Config();

function accessDatabase()
{
    global $configure;
    try {
        $pdo = new pdo("mysql:host=dbhost.cs.man.ac.uk;dbname=t11915jr","t11915jr", "Dd-17.o.TTaS");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }
    catch (PDOException $e)
    {
        echo "Connection failed" . $e->getMessage();
        $pdo = null;
    }
    return $pdo;
}

function registerUser($user)
{
    $pdo = accessDatabase();
    $userID = $user['username'];
    $sql = $sql = "SELECT * FROM User WHERE user_ID='$userID'";
    $query = getFromDatabase($user,$sql);
    if (mysqli_num_rows($query) > 0) {
        $user['badinput'] = "User exists already";
        return $user;
    }
    $sql = "INSERT INTO User (user_ID, user_name, user_password) VALUES (:userId, :username, :userpassword);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $user['username'],
        'username' => $user['name'],
        'userpassword' => $user['password']]);
    $user['validinput'] = "User Created";
    return $user;
}

function loginUser($user)
{
    $userID = $user['username'];
    $pass = $user['password'];
    $sql = "SELECT * FROM User WHERE user_ID='$userID'";
    $query = getFromDatabase($user, $sql);
    if (mysqli_num_rows($query) == 0)
    {
        $user['badinput'] = "User does not exist";
        return $user;
    }
    elseif (mysqli_num_rows($query) > 1) {
        $user['badinput'] = "An error has occurred";
        return $user;
    }
//    $result = $query->use_result();
    $result = 0;
    if ($result != $pass)
    {
        $user['badinput'] = "Incorrect Password entered";
        return $user;
    }

    $sql = "SELECT user_ID, user_password FROM User WHERE  user_ID = :userId";
    $pdo = accessDatabase();
    if (empty($pdo))
    {
        $user['badinput'] = 'Cannot connect to database';
        return $user;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId'=>$user['username']]);

    $pass = $user['password'];
    $user['validinput'] = "User Created";
    return $user;
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

function getUserCredentials()
{
    $user = array();
    if (isset($_POST['password'])) {
        $user['username'] = $_POST['username'];
        $user['password'] = $_POST['password'];
    }
    else{
        if ($_POST['password1'] != $_POST['password2'])
        {
            $user['badinput'] = "Passwords do not match";
            return $user;
        }

        $user['username'] = $_POST['username'];
        $user['name'] = $_POST['name'];
        $user['password'] = $_POST['password1'];
        if (strlen($user['password'])< 6)
        {
            $user['badinput'] = "Password too short";
            return $user;
        }
        $user = registerUser($user);
    }
    return $user;

}

function getFromDatabase($user, $sql)
{
    $db = mysqli_connect('dbhost.cs.man.ac.uk', 't11915jr', 'Dd-17.o.TTaS', 't11915jr');
    return mysqli_query($db, $sql);
}

function showPOSTdata()
{
    foreach ($_POST as $key => $value)
    {
        echo ("<br>$key:$value");
    }
}
?>
