#!/usr/bin/env php
<?php
declare(strict_types=1);

use \Faker\Generator as FakerInterface;

require_once __DIR__ . '/vendor/autoload.php';

\Dotenv\Dotenv::create(__DIR__)->load();

$pdo = (new \App\DbConnectionFactory(getenv('PDO_DSN'), getenv('PDO_USER'), getenv('PDO_PASSWORD')))->createConnection();
$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);

$faker = Faker\Factory::create('ru_RU');

create_clients($faker, $pdo, 50);

/**
 * Создает клиентов в БД
 *
 * @param FakerInterface $faker Faker
 * @param PDO $pdo PDO
 * @param int $count Кол-во
 *
 * @return void
 */
function create_clients(FakerInterface $faker, PDO $pdo, int $count): void
{
    $sql = 'INSERT INTO clients (name, address, phone) VALUES (?, ?, ?)';

    $stmt = $pdo->prepare($sql);

    for($i = 0; $i < $count; $i++) {
        $stmt->execute([
            $faker->name,
            $faker->address,
            $faker->e164PhoneNumber
        ]);

        $client_id = $pdo->lastInsertId();

        create_realties($faker, $pdo, $client_id, $faker->numberBetween(10, 90));
    }
}

/**
 * Создает объекты недвижимости в БД
 *
 * @param FakerInterface $faker Faker
 * @param PDO $pdo PDO
 * @param string $clientId ID клиента
 * @param int $count Кол-во
 *
 * @return void
 */
function create_realties(FakerInterface $faker, PDO $pdo, string $clientId, int $count):void
{
    $sql = 'INSERT INTO realties (address, area, apartment_number, rooms, floor) VALUES (?, ?, ?, ?, ?)';

    $stmt = $pdo->prepare($sql);

    for ($i = 0; $i < $count; $i++) {
        $stmt->execute([
            $faker->address,
            $faker->randomFloat(3, 50, 150),
            $faker->numberBetween(1, 120),
            $faker->numberBetween(1, 5),
            $faker->numberBetween(1, 12),
        ]);

        $realty_id = $pdo->lastInsertId();

        create_client_realty_offer($faker, $pdo, $clientId, $realty_id);
        create_realty_photo($faker, $pdo, $realty_id, $faker->numberBetween(3, 8));
    }
}

/**
 * Создает предложения клиентов недвижимости в БД
 *
 * @param FakerInterface $faker Faker
 * @param PDO $pdo PDO
 * @param string $clientId ID клиента
 * @param string $realtyId ID объекта недвижимости
 *
 * @return void
 */
function create_client_realty_offer(FakerInterface $faker, PDO $pdo, string $clientId, string $realtyId): void
{
    $sql = 'INSERT INTO realty_offers (realty_id, client_id) VALUES (?, ?)';

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $realtyId,
        $clientId,
    ]);
}

/**
 * @param FakerInterface $faker Faker
 * @param PDO $pdo PDO
 * @param string $realtyId ID объекта недвижимости
 * @param int $count Кол-во
 *
 * @return void
 */
function create_realty_photo(FakerInterface $faker, PDO $pdo, string $realtyId, int $count): void
{
    $sql = 'INSERT INTO photos (realty_id, url) VALUES (?, ?)';

    $stmt = $pdo->prepare($sql);

    for ($i = 0; $i < $count; $i++) {
        $stmt->execute([
            $realtyId,
            $faker->imageUrl(800, 600),
        ]);
    }
}