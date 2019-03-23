#!/usr/bin/env php
<?php

use App\Repository\ClientRepository;
use App\Repository\RealtyRepository;
use App\XmlExporter;

require_once __DIR__ . '/vendor/autoload.php';

\Dotenv\Dotenv::create(__DIR__)->load();


if($argc < 2) {
    printf("Usage %s OUTPUT_FILE\n", $argv[0]);

    exit(1);
}

$pdo = (new \App\PdoFactory(getenv('PDO_DSN'), getenv('PDO_USER'), getenv('PDO_PASSWORD')))->createPdo();

$realty_repo = new RealtyRepository($pdo);
$client_repo = new ClientRepository($pdo);
$exporter = new XmlExporter();


$exporter->exportToFile($argv[1], get_offers($realty_repo, $client_repo));

exit(0);


function get_offers(RealtyRepository $realtyRepository, ClientRepository $clientRepository) {
    $client1 = $clientRepository->getRandomClient();
    $client2 = $clientRepository->getRandomClient();
    $client3 = $clientRepository->getRandomClient();

    // клиент1 - однокомнатные
    foreach ($realtyRepository->getRealtyOffersForClient($client1, 1) as $offer) {
        yield $offer;
    }

    // клиент1 - трехкомнатные
    foreach ($realtyRepository->getRealtyOffersForClient($client2, 3) as $offer) {
        yield $offer;
    }

    // клиент3 - все
    foreach ($realtyRepository->getRealtyOffersForClient($client3, null) as $offer) {
        yield $offer;
    }
}

