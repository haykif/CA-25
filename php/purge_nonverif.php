<?php
require_once '/www/wwwroot/ca25.charles-poncet.net/php/database.php';

try {
    $deleteStmt = $pdo->prepare("
        DELETE FROM User 
        WHERE Mail_verif = 1 
          AND Verifier IS NULL
    ");
    $deleteStmt->execute();
    $nb = $deleteStmt->rowCount();

    file_put_contents('/tmp/purge_log.txt', date('Y-m-d H:i:s') . " - $nb utilisateur(s) supprimé(s)\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents('/tmp/purge_log.txt', date('Y-m-d H:i:s') . " - Erreur : " . $e->getMessage() . "\n", FILE_APPEND);
}
?>
