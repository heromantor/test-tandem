#!/usr/bin/env php
<?php
use \Faker\Generator as FakerInterface;

require_once __DIR__ . '/vendor/autoload.php';

\Dotenv\Dotenv::create(__DIR__)->load();



$pdo = (new \App\PdoFactory(getenv('PDO_DSN'), getenv('PDO_USER'), getenv('PDO_PASSWORD')))->createPdo();
$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);


$faker = Faker\Factory::create('ru_RU');


create_clients($faker, $pdo, 50);






function create_clients(FakerInterface $faker, PDO $pdo, int $count)
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

function create_realties(FakerInterface $faker, PDO $pdo, string $clientId, int $count)
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

function create_client_realty_offer(FakerInterface $faker, PDO $pdo, string $clientId, string $realtyId)
{
    $sql = 'INSERT INTO realty_offers (realty_id, client_id) VALUES (?, ?)';

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $realtyId,
        $clientId,
    ]);
}

function create_realty_photo(FakerInterface $faker, PDO $pdo, string $realtyId, int $count)
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