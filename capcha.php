<?php
// Функция для проверки движения мыши
function is_human($movements) {
    if(count($movements) < 2) return false; // Изменено: теперь нужно минимум 5 движений

    $speeds = [];
    for($i = 1; $i < count($movements); $i++) {
        $dx = $movements[$i]['x'] - $movements[$i-1]['x'];
        $dy = $movements[$i]['y'] - $movements[$i-1]['y'];
        $dt = ($movements[$i]['t'] - $movements[$i-1]['t']) / 1000;
        $speed = sqrt($dx*$dx + $dy*$dy) / $dt;
        $speeds[] = $speed;
    }

    $speed_variation = max($speeds) - min($speeds);
    return $speed_variation > 20;
}

// Функция для генерации математического примера
function generate_question() {
    $a = rand(1, 10);
    $b = rand(1, 10);
    $ops = ['+', '-', '*'];
    $op = $ops[array_rand($ops)];

    switch($op) {
        case '+': $answer = $a + $b; break;
        case '-': $answer = $a - $b; break;
        case '*': $answer = $a * $b; break;
    }

    $_SESSION['captcha_answer'] = $answer;
    $_SESSION['mouse_data'] = [];

    return "$a $op $b = ?";
}

// Подключаем файлы базы данных и функций
require_once 'config.php';
require_once 'functions.php';

// Инициализация переменных
$username = "";
$password = "";
$email = "";
$errors = [];
$success_message = "";
$captcha_passed = false; // Флаг, показывающий, пройдена ли капча

// Генерируем вопрос капчи
$question = generate_question();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $email = trim($_POST["email"]);
    $answer = $_POST['answer'] ?? null;
    $mouse_data = json_decode($_POST['mouse_data'] ?? '[]', true);

    // Проверяем капчу, только если нажата кнопка "Зарегистрироваться"
    if (isset($_POST['register'])) {
        // Проверяем наличие данных для капчи
        if (isset($answer, $_SESSION['captcha_answer'], $mouse_data)) {
            $is_correct = ($answer == $_SESSION['captcha_answer']);
            $is_human = is_human($mouse_data);

            if ($is_correct && $is_human) {
                $captcha_passed = true;
                unset($_SESSION['captcha_answer']);
                unset($_SESSION['captcha_question']);
                unset($_SESSION['mouse_data']);
            } else {
                $errors[] = "Неверно решена капча!";
                $question = generate_question(); // Обновляем вопрос
            }
        } else {
            $errors[] = "Капча не была решена!";
        }
    }

    // Проверяем наличие данных для регистрации и пройдена ли капча
    if (isset($username, $password, $email) && $captcha_passed) {
        // Проверяем данные пользователя
        if (empty($username)) {
            $errors[] = "Имя пользователя обязательно.";
        }
        if (empty($password)) {
            $errors[] = "Пароль обязательно.";
        }
        if (empty($email)) {
            $errors[] = "Email обязательно.";
        }

        if (empty($errors)) {
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashedPassword, $email]);

                $success_message = "Регистрация прошла успешно! Теперь вы можете войти.";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errors[] = "Имя пользователя или email уже зарегистрированы.";
                } else {
                    $errors[] = "Ошибка при регистрации: " . $e->getMessage();
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text);
            background: var(--light);
            line-height: 1.7;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        h1, h2, h3, h4 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            flex: 1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .header {
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 5px 25px rgba(170, 0, 255, 0.3);
        }

        .navigation-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
        }

        .logo {
            font-size: 2rem;
            font-weight: 900;
            color: white;
            text-decoration: none;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: -1px;
        }

        .navigation {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }

        .navigation a {
            text-decoration: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            position: relative;
            transition: var(--transition);
            padding: 0.5rem 0;
        }

        .navigation a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: 0;
            left: 0;
            background-color: var(--secondary);
            transition: var(--transition);
        }

        .navigation a:hover {
            color: var(--secondary);
            transform: translateY(-3px);
        }

        .navigation a:hover:after {
            width: 100%;
        }

        .user-actions {
            display: flex;
            gap: 1.5rem;
        }

        .user-actions a {
            padding: 0.7rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
            font-size: 1rem;
        }

        .user-actions a:first-child {
            color: white;
            border: 2px solid white;
        }

        .user-actions a:last-child {
            background: white;
            color: var(--accent);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        .user-actions a:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        input[type="submit"],
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        
        input[type="submit"]:hover,
        button:hover {
            background-color: #45a049;
        }
        
        input[type="submit"]:disabled,
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        p {
            text-align: center;
            margin-top: 20px;
        }
        
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        #captcha-container {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        #mouse-area {
            padding: 20px;
            text-align: center;
            background-color: #f0f0f0;
            border-radius: 4px;
            margin: 15px 0;
            cursor: pointer;
            border: 2px dashed #ccc;
            transition: all 0.3s;
        }
        
        #mouse-area.active {
            border-color: #4CAF50;
            background-color: #e8f5e9;
        }
        
        .mouse-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        #error-message {
            color: #e74c3c;
            margin: 10px 0;
            min-height: 20px;
        }
        
        .question {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .instructions {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        div[style*="color: red"],
        div[style*="color: green"] {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        div[style*="color: red"] {
            background-color: #ffebee;
            border: 1px solid #ef9a9a;
        }
        
        div[style*="color: green"] {
            background-color: #e8f5e9;
            border: 1px solid #a5d6a7;
        }
    </style>
</head>
<body>
    <h1>Регистрация</h1>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div style="color: green;">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>

    <form method="post">
        <label for="username">Имя пользователя:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br><br>

        <label for="password">Пароль:</label><br>
        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>

        <div id="captcha-container">
            <div class="captcha-content">
                <h3>Подтвердите, что вы не робот</h3>
                <p class="instructions">Для продолжения решите пример и проведите мышкой по выделенной области</p>

                <div class="question"><?php echo $question; ?></div>

                <input type="text" name="answer" id="answer-input" placeholder="Ваш ответ" required>

                <div id="mouse-area">
                    <div>
                        <div class="mouse-icon">🖱️</div>
                        <div>Переместите курсор мыши по этой области</div>
                    </div>
                </div>

                <div id="error-message"></div>

                <input type="hidden" name="mouse_data" id="mouse-data">
                <button type="button" id="submit-btn" disabled onclick="checkCaptcha()">Проверить ответ</button>
            </div>
        </div>

        <input type="submit" name="register" value="Зарегистрироваться" disabled id="register-btn">
    </form>
    <p>Уже зарегистрированы? <a href="login.php">Авторизоваться</a></p>

    <script>
        const mouseArea = document.getElementById('mouse-area');
        const mouseData = document.getElementById('mouse-data');
        const captchaForm = null; // Убираем ссылку на несуществующую форму
        const errorMessage = document.getElementById('error-message');
        const registerBtn = document.getElementById('register-btn'); // Кнопка "Зарегистрироваться"
        const answerInput = document.getElementById('answer-input');
        const captchaSubmitBtn = document.getElementById('submit-btn'); // Кнопка "Проверить"

        let movements = [];
        let mouseMoved = false;
        let answerFilled = false;
        let captchaPassed = false;

        function checkFormReady() {
             captchaSubmitBtn.disabled = !(mouseMoved && answerFilled); // Активируем кнопку, только если оба условия выполнены
        }

        mouseArea.addEventListener('mousemove', (e) => {
            if (!mouseMoved) {
                mouseMoved = true;
                mouseArea.classList.add('active');
                mouseArea.innerHTML = '<div>Отлично! Теперь введите ответ и нажмите "Проверить"</div>';
                checkFormReady();
            }

            const rect = mouseArea.getBoundingClientRect();
            movements.push({
                x: e.clientX - rect.left,
                y: e.clientY - rect.top,
                t: Date.now()
            });

            mouseData.value = JSON.stringify(movements);
        });

        answerInput.addEventListener('input', (e) => {
            answerFilled = e.target.value.trim() !== '';
            checkFormReady();
        });

        function checkCaptcha() {
            console.log("checkCaptcha() called");
            if (!mouseMoved) {
                errorMessage.textContent = 'Пожалуйста, проведите курсором по указанной области!';
                mouseArea.style.borderColor = '#e74c3c';
                mouseArea.style.backgroundColor = '#ffebee';

                mouseArea.animate([
                    { transform: 'translateX(0)' },
                    { transform: 'translateX(-10px)' },
                    { transform: 'translateX(10px)' },
                    { transform: 'translateX(0)' }
                ], {
                    duration: 500,
                    iterations: 3
                });
                return;
            }

            if (!answerFilled) {
                errorMessage.textContent = 'Пожалуйста, введите ответ!';
                answerInput.style.borderColor = '#e74c3c';
                return;
            }

            // Капча пройдена
            captchaPassed = true;
            checkFormReady();
            errorMessage.textContent = '';
            captchaSubmitBtn.style.display = 'none';
            alert("Капча пройдена, можете регистрироваться");
        }

        mouseArea.addEventListener('mouseenter', () => {
            if (!mouseMoved) {
                mouseArea.innerHTML = `
                    <div>
                        <div class="mouse-icon">🖱️</div>
                        <div>Перемещайте курсор мыши здесь</div>
                    </div>`;
            }
        });

        answerInput.addEventListener('focus', () => {
            errorMessage.textContent = '';
            answerInput.style.borderColor = '#ddd';
        });
    </script>
</body>
</html>