<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $city = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '';
    $bos = isset($_POST['bos']) ? htmlspecialchars($_POST['bos']) : '';
    $bostel = isset($_POST['bostel']) ? htmlspecialchars($_POST['bostel']) : '';
    $mail = isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '';

    if (!empty($name) && !empty($city) && !empty($bos) && !empty($bostel)) {
        $message = "Город: $city\n";
        $message .= "Название: $name\n";
        $message .= "Организатор: $bos ($bostel)\n";
        $message .= "E-MAIL: $mail\n";

        $headers = "Content-type: text/plain; charset=utf-8 \n";
        
        // Отправка письма
        if (mail('artemlitivnov@gmail.com, artemlitvinoov81@gmail.com', 'Заявка с сайта', $message, $headers)) {
            echo "success"; // Отправляем AJAX ответ
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}
?>
