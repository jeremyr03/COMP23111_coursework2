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
    <a onclick="document.getElementById('quizlist').style.display='block'">Take quiz</a>
</section>

<?php
session_start();
$username = $_SESSION['username'];
$type = null;

echo createQuiz($username);
echo viewQuizzes($username);
if (empty($_POST))
    $input = array();
    $input = userInput($username);



function userInput($user)
{
    foreach ($_POST as $stuff)
    {
        // Requires PHP 8.0 to run
        if (str_contains($stuff, "question"))
        {

            $num = $_POST[substr(7,400)];
            $temp = $_POST[$stuff];
            $sql = 'INSERT IGNORE INTO `t11915jr`.Question(question_number, question) VALUES (:qn, :q) ON DUPLICATE KEY UPDATE question = :q';
            $sql_arr = ['qn'=>&$num,'q'=>&$temp];

        }
    }
}

function viewQuizzes($user)
{
    $userID = $user;
    $sql = "SELECT * FROM Quiz WHERE quiz_owner=:userID";
    $sql_arr = ['userID'=>$userID];
    $query = accessSchema($sql, $sql_arr);
    $toOutput = '';
    if ($query->rowCount()==0)
    {
        return '<div id="quizlist" style="display: none"><title>No quizzes</title></div>';
    }
    foreach ($query->fetchAll() as $key=>$value)
    {
        $toOutput .= '<div class="quiz"><div class="title">'. $value['quiz_name']. '</div></div>';
    }
    return $toOutput;
}

function updateQuiz($quiz_ID=null)
{
    echo "nya";
//    $sql = "INSERT INTO `t11915jr`.Quiz (user_ID, user_name, user_password) VALUES (:userId, :username, :userpassword);";
//    $sql_arr = ['userId'=>&$userID, 'username'=>&$userName, 'userpassword'=>&$userPass];
//    $query = accessSchema($sql, $sql_arr);
}

function createQuiz($user)
{
    $toPost = '<form id="create" class="popup-content" method="post" style="margin: 10% auto 20% auto; display: none">
        <div class="column_container" style="justify-content: space-between">
            <div>
                <h1 style="font-size: 50px">Sign Up</h1>
            </div>
            <div>
                <!--Cancel button-->
                <button type="button" style="border-radius: 30px; font-weight: 700"
                        onclick="document.getElementById(\'create\').style.display=\'none\'">X</button>
            </div>
        </div>';

    $toPost .= createQ(0);

    $toPost .= '<button type="submit" >Save</button></form>';

    return $toPost;

}

function createQ($num)
{
    $toPost = '<label for="question'.$num.'">Question '.($num+1).'</label>';
    $toPost .= '<input type="text" name="question'.$num.'" placeholder="Enter question..." required>';
    // answers
    $i = 0;
    $toPost .= '<div>';
    while ($i < 4)
    {
        $toPost .= '<div style="width: 20%; float: left">';
        $toPost .= '<br><br><input type="radio" name="question'.$num.'" value="answer?">';
        $toPost .= '</div>';
        $toPost .= '<div style="width: 80%; float: left">';
        $toPost .= '<label for="question'.$num.'a'.$i.'">Answer '.($i+1).'</label>';
        $toPost .= '<input type="text" name="question'.$num.'a'.$i.'" placeholder="Enter Answer...">';
        $toPost .= '</div>';

        $i ++;
    }
    $toPost .= '</div>';

    return $toPost;
}

function getQuestions($user, $create, $quiz_id=null)
{
    $toOutput = '';
    $sql = "SELECT quiz_ID FROM Quiz WHERE quiz_owner=:userID";
    $sql_arr = ['userID'=>$user];
    $query = accessSchema($sql, $sql_arr);

    if ($query->rowCount() == 1)
    {
        $quiz_id = $query->fetch();
        $sql = "SELECT * FROM Question WHERE quiz_owner=:quizID";
        $sql_arr = ['quizID'=>$quiz_id];
        $query_questions = accessSchema($sql, $sql_arr);
        foreach ($query_questions->fetchAll() as $key => $record) {
            $sql = "SELECT answer, is_correct FROM Answer WHERE quiz_owner=:quizID AND question_number=:num";
            $sql_arr = ['quizID' => $quiz_id, 'num' => $key];
            $query_answers = accessSchema($sql, $sql_arr);

            $toOutput .= addQ($record['question_number'], $record['question'], $query_answers, $create);

        }
    }
    return $toOutput;
}

function addQ($k, $v, $ans, $create)
{
//    <label for="username">User ID</label>
//    <input type="text" name="username" placeholder="Enter Username..." required>
    $str = '<label for="question' . $k . '">Question ' . $k . '</label>';
    $str .= 'Question ' . $k;
    $str .= '</label>';
    if ($create == true)
    {
        $str .= '<input type="text"  name="question' . $k . '" value="'. $v .'">';
    }
    foreach ($ans->fetchAll() as $key => $record)
    {
        //  <input type="radio" name="choice" value="Application"> Application
        //  <input type="radio" name="choice" value="None of These"> None of These
        if ($create != true)
        {
            $str .= '<input type="radio" name="question' . $k . '" value="' .$key. ': '. $record . '>' . $key. ': '. $record;
        }else
        {
            $str .= '<label for="question' . $k . 'a'. $key. '>Question ' . $k . 'a'. $key. "</label>";
            $str .= '<input type="text" name="question' . $k . 'a'.'" value="'.$record .'"';
        }
    }
    return $str;
}


/** @noinspection DuplicatedCode */
function accessSchema($sql, $sqlstmt=[])
{
    $sql_stmt = $sqlstmt;
    try {
        $pdo = new pdo('mysql:host=dbhost.cs.man.ac.uk;dbname=t11915jr', 't11915jr', 'Dd-17.o.TTaS');
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
?>
