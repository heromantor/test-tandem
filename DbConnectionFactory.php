<?php
declare(strict_types=1);
namespace App;

use PDO;

/**
 * Фабрика создания соеднинения с БД
 */
class DbConnectionFactory
{
    /**
     * @var string
     */
    private $dsn;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $password;

    /**
     * Конструктор.
     *
     * @param string $dsn DSN
     * @param string $user имя пользователя
     * @param string $password Пароль
     */
    public function __construct(string $dsn, string $user, string $password)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Создает соединение с БД
     *
     * @return PDO Соеднинение с БД
     */
    public function createConnection(): PDO
    {
        $pdo = new PDO($this->dsn, $this->user, $this->password);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}