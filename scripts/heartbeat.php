<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config/database.php';

try {
    $statement = database()->query(
        'SELECT CURRENT_TIMESTAMP AS checked_at, COUNT(*) AS user_count
         FROM users'
    );
    $result = $statement->fetch();

    $checkedAt = (string) ($result['checked_at'] ?? gmdate('Y-m-d H:i:s'));
    $userCount = (int) ($result['user_count'] ?? 0);

    fwrite(STDOUT, sprintf("Heartbeat OK at %s UTC. Users: %d\n", $checkedAt, $userCount));
    exit(0);
} catch (Throwable $throwable) {
    fwrite(STDERR, 'Heartbeat failed: ' . $throwable->getMessage() . PHP_EOL);
    exit(1);
}
