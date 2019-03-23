<?php
namespace App\Repository;

use App\Entity\Client;
use App\Entity\Realty;
use App\Entity\RealtyOffer;
use App\Entity\RealtyPhoto;
use PDO;

class RealtyRepository
{
    /**
     * @var PDO
     */
    private $pdo;
    /**
     * @var \DateTimeZone
     */
    private $utc_timezone;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->utc_timezone = new \DateTimeZone('UTC');
    }

    /**
     * @param array|string[] $realtyIds
     * @return array|RealtyPhoto[]
     */
    private function getPhotosForRealties(array $realtyIds): array
    {
        if(count($realtyIds) < 1) {
            return [ ];
        }

        $sql = $this->buildQuery('
            SELECT
                p.id
              , p.realty_id realty_id
              , p.url url
            FROM photos p
        ', [
            'p.realty_id IN (' . implode(', ', array_fill(0, count($realtyIds), '?')) . ')'
        ]);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_map('strval', $realtyIds));

        $result = [ ];

        while(false !== ($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
            $realty_id = $row['realty_id'];

            if(!isset($result[$realty_id])) {
                $result[$realty_id] = [ ];
            }

            $result[$realty_id][] = $this->deserializePhotoRow($row);
        }

        return $result;
    }

    private function deserializePhotoRow(array $row): RealtyPhoto
    {
        return new RealtyPhoto($row['id'], $row['url']);
    }

    /**
     * @return iterable|Realty[]
     */
    public function getRealtyOffersForClient(Client $client, ?int $roomsCount, bool $isSkipDeleted = true): iterable
    {
        if(null !== $roomsCount && $roomsCount < 1) {
            throw new \InvalidArgumentException(sprintf("Rooms counts must be > 1, '%d' given", $roomsCount));
        }

        if(null === $client->getId()) {
            throw new \InvalidArgumentException("Client with empty id given");
        }

        $where = [
            'c.id=?',
        ];

        $whereParams = [
            $client->getId()
        ];

        if(null !== $roomsCount) {
            $where[] = 'r.rooms=?';
            $whereParams[] = $roomsCount;
        }

        if($isSkipDeleted) {
            $where[] = 'ro.deleted_at IS NULL';
        }

        $sql = $this->buildQuery('
            SELECT
                  r.id id
                , r.address address
                , r.area area
                , r.apartment_number apartment_number
                , r.rooms rooms
                , r.floor floor
                
                , UNIX_TIMESTAMP(ro.updated_at) updated_at
                , UNIX_TIMESTAMP(ro.deleted_at) deleted_at
                , UNIX_TIMESTAMP(ro.created_at) created_at
            FROM realties r
              JOIN realty_offers ro ON ro.realty_id=r.id
              JOIN clients c ON c.id=ro.client_id
        ', $where);

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($whereParams);

        $result = [ ];

        while(false !== ($row = $stmt->fetch(\PDO::FETCH_ASSOC))) {
            $result[] = $this->deserializeRealtyOfferRow($client, $row);
        }

        return $this->extendRealtyOffersWithPhotos($result);
    }

    private function deserializeRealtyOfferRow(Client $client, array $row): RealtyOffer
    {
        $realty = $this->deserializeRealtyRow($row);
        $offer = new RealtyOffer($client, $realty,  $this->convertTimestampToDatetime($row['created_at']));

        if(null !== $row['updated_at']) {
            $offer->setUpdatedAt($this->convertTimestampToDatetime($row['updated_at']));
        }

        if(null !== $row['deleted_at']) {
            $offer->setDeletedAt($this->convertTimestampToDatetime($row['deleted_at']));
        }

        return $offer;
    }

    private function deserializeRealtyRow(array $row): Realty
    {
        return new Realty(
            $row['id'],
            $row['address'],
            $row['area'],
            $row['apartment_number'],
            $row['rooms'],
            $row['floor']
        );
    }

    private function convertTimestampToDatetime(?string $timestamp): ?\DateTimeImmutable
    {
        if(null === $timestamp) {
            return null;
        }

        return new \DateTimeImmutable('@' . $timestamp, $this->utc_timezone);
    }

    /**
     * @param array|RealtyOffer[] $offers
     * @return array|RealtyOffer[]
     */
    private function extendRealtyOffersWithPhotos(array $offers): array
    {
        $photos = $this->getPhotosForRealties(array_map(function(RealtyOffer $offer) {
            return $offer->getRealty()->getId();
        }, $offers));

        foreach ($offers as $realty_offer) {
            $realty_id = $realty_offer->getRealty()->getId();

            if(isset($photos[$realty_id])) {
                foreach ($photos[$realty_id] as $photoEntity) {
                    $realty_offer->getRealty()->addPhoto($photoEntity);
                }
            }
        }

        return $offers;
    }

    private function buildQuery(string $sql, array $where): string
    {
        if(count($where)) {
            $sql .= ' WHERE ' . implode(' AND ', array_map('strval', $where));
        }

        return trim($sql);
    }
}
