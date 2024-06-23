<?php

function logError($message) {
    // Define o caminho do arquivo de log
    $logFilePath = __DIR__ . '/error.log';

    // Obtém a data e hora atual
    $currentTime = date('Y-m-d H:i:s');

    // Formata a mensagem de log
    $logMessage = "[$currentTime] ERROR: $message\n";

    // Escreve a mensagem de erro no arquivo de log
    // Usando o flag FILE_APPEND para adicionar ao arquivo em vez de sobrescrevê-lo
    // E LOCK_EX para evitar que qualquer outro processo escreva no arquivo ao mesmo tempo
    file_put_contents($logFilePath, $logMessage, FILE_APPEND | LOCK_EX);
}
?>
