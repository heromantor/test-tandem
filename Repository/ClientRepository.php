<?php
namespace App\Repository;

use App\Entity\Client;
use PDO;

class ClientRepository
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRandomClient(): Client
    {
        // ORDER BY RAND() - только для примера

        $sql = '
            SELECT
                  c.id
                , c.name
                , c.address
                , c.phone
            FROM clients c
            ORDER BY RAND()
            LIMIT 1 
        ';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new RepositoryException(sprintf("Error while executing sql query: %s", $e->getMessage()), 0, $e);
        }

        if(false === $row) {
            return null;
        }

        return $this->deserializeClientRow($row);
    }

    private function deserializeClientRow(array $row): Client
    {
        return new Client($row['id'], $row['address'], $row['name'], $row['phone']);
    }

}