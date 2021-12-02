<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="styles/main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
</head>
<body>
<h1>Main Page</h1>
<!--Navigation bar-->
<section class="navbar">
    <a onclick="document.getElementById('create').style.display='block'">Create Quiz</a>
    <a onclick="document.getElementById('signup').style.display='block'">View quiz</a>
</section>

<?php
session_start();
$username = $_SESSION['username'];

function createQuiz()
{
    return '
  <form id="create" class="popup-content" method="post" style="display: none">
        <div class="column_container" style="justify-content: space-between">
            <div>
                <h1 style="font-size: 50px; font-weight: 700">Login</h1>
            </div>
            <div>
                <!--Cancel button-->
                <button type="button" style="border-radius: 30px"
                        onclick="document.getElementById(\'create\').style.display=\'none\'">X</button>
            </div>
        </div>
        <div id="loadHere">
        
        </div>
        <input type="button" id="add" value="Add">
        <label for="username">User ID</label>
        <input type="text" name="username" placeholder="Enter Username..." required>
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Enter Password..." required>
        <button type="submit" value="save">Save</button>
    </form>
    <script>
    $(\'#plusButton\').click(function(){
        $(\'#loadHere\').append("Name: <input type=\"text\" name=\"name[]\" id=\"name\" /> Age <input type=\"text\" name=\"age[]\" id=\"age\" /> Address <input type=\"text\" name=\"address[]\" id=\"end\"><br />");
    });
</script>
    ';
}

function viewQuiz()
{
    header("refresh:5; url=viewQuiz.php");
}
