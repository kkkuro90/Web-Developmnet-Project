<?php
session_start();


function is_human($movements) {
    if(count($movements) < 10) return false;
    

    $speeds = [];
    for($i = 1; $i < count($movements); $i++) {
        $dx = $movements[$i]['x'] - $movements[$i-1]['x'];
        $dy = $movements[$i]['y'] - $movements[$i-1]['y'];
        $dt = ($movements[$i]['t'] - $movements[$i-1]['t']) / 1000;
        $speed = sqrt($dx*$dx + $dy*$dy) / $dt;
        $speeds[] = $speed;
    }
    
    $speed_variation = max($speeds) - min($speeds);
    return $speed_variation > 50;
}


$user_answer = $_POST['answer'] ?? null;
$correct_answer = $_SESSION['captcha_answer'] ?? null;
$mouse_data = json_decode($_POST['mouse_data'] ?? '[]', true);

if(!isset($user_answer, $correct_answer)) {
    die("Ошибка: данные капчи не найдены!");
}


unset($_SESSION['captcha_answer']);
unset($_SESSION['captcha_question']);


$is_correct = ($user_answer == $correct_answer);
$is_human = is_human($mouse_data);

if($is_correct && $is_human) {
    $message = "✔ Капча пройдена успешно!";
    $class = "success";
} elseif(!$is_human) {
    $message = "✖ Подозрительные движения мыши! Пройдите проверку ещё раз.";
    $class = "error";
} else {
    $message = "✖ Неверный ответ! Правильно: $correct_answer";
    $class = "error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Результат проверки</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0 auto; padding: 20px; }
        .message { padding: 15px; border-radius: 4px; margin: 20px 0;}
        .success { background-color: #dff0d8; color: #3c763d; }
        .error { background-color: #f2dede; color: #a94442; }
    </style>
</head>
<body>
    <div class="message <?php echo $class; ?>">
        <?php echo $message; ?>
    </div>
    <a href="index.php">Попробовать снова</a>
</body>
</html>