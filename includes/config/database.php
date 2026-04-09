<?php
declare(strict_types=1);

function env_config_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

function parse_database_url(string $databaseUrl): array
{
    $parts = parse_url($databaseUrl);

    if ($parts === false) {
        return [];
    }

    parse_str($parts['query'] ?? '', $query);
    $scheme = strtolower((string) ($parts['scheme'] ?? ''));
    $driver = match ($scheme) {
        'pgsql', 'postgres', 'postgresql' => 'pgsql',
        'mysql' => 'mysql',
        default => '',
    };

    if ($driver === '') {
        return [];
    }

    return [
        'driver' => $driver,
        'host' => (string) ($parts['host'] ?? ''),
        'port' => isset($parts['port']) ? (string) $parts['port'] : ($driver === 'pgsql' ? '5432' : '3306'),
        'database' => isset($parts['path']) ? ltrim((string) $parts['path'], '/') : '',
        'username' => isset($parts['user']) ? rawurldecode((string) $parts['user']) : '',
        'password' => isset($parts['pass']) ? rawurldecode((string) $parts['pass']) : '',
        'charset' => (string) ($query['charset'] ?? 'utf8mb4'),
        'sslmode' => (string) ($query['sslmode'] ?? ($driver === 'pgsql' ? 'require' : 'prefer')),
    ];
}

function database_config(): array
{
    static $config;

    if ($config !== null) {
        return $config;
    }

    $config = [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'mini_pos_system',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'sslmode' => 'prefer',
    ];

    $localConfigFile = __DIR__ . '/database.local.php';

    if (file_exists($localConfigFile)) {
        $localConfig = require $localConfigFile;

        if (is_array($localConfig)) {
            $config = array_merge($config, $localConfig);
        }
    }

    $databaseUrl = env_config_value('DATABASE_URL');

    if (is_string($databaseUrl) && $databaseUrl !== '') {
        $config = array_merge($config, parse_database_url($databaseUrl));
    }

    $config = array_merge($config, array_filter([
        'driver' => env_config_value('DB_DRIVER'),
        'host' => env_config_value('DB_HOST'),
        'port' => env_config_value('DB_PORT'),
        'database' => env_config_value('DB_NAME', env_config_value('DB_DATABASE')),
        'username' => env_config_value('DB_USER', env_config_value('DB_USERNAME')),
        'password' => env_config_value('DB_PASS', env_config_value('DB_PASSWORD')),
        'charset' => env_config_value('DB_CHARSET'),
        'sslmode' => env_config_value('DB_SSLMODE'),
    ], static fn (mixed $value): bool => $value !== null && $value !== ''));

    return $config;
}

function database_driver(): string
{
    return (string) (database_config()['driver'] ?? 'mysql');
}

function database_is_pgsql(): bool
{
    return database_driver() === 'pgsql';
}

function database(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = database_config();
    $dsn = database_is_pgsql()
        ? sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['sslmode']
        )
        : sprintf(
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
            'Database connection failed. Update your database.local.php file or environment variables first.',
            0,
            $exception
        );
    }

    return $pdo;
}
