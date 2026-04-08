<?php
declare(strict_types=1);

function database_config(): array
{
    static $config;

    if ($config !== null) {
        return $config;
    }

    $config = [
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'mini_pos_system',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ];

    $localConfigFile = __DIR__ . '/database.local.php';

    if (file_exists($localConfigFile)) {
        $localConfig = require $localConfigFile;

        if (is_array($localConfig)) {
            $config = array_merge($config, $localConfig);
        }
    }

    return $config;
}

function database(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = database_config();
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    try {
        $pdo = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        throw new RuntimeException(
            'Database connection failed. Update includes/config/database.local.php with your MySQL settings first.',
            0,
            $exception
        );
    }

    return $pdo;
}

