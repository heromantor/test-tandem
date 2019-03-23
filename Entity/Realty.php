<?php
namespace App\Entity;


class Realty
{
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var array|RealtyPhoto[]
     */
    private $photos = [ ];
    /**
     * @var string
     */
    private $address = '';
    /**
     * @var float
     */
    private $area = '';
    /**
     * @var int
     */
    private $appartmentNumber = '';
    /**
     * @var int
     */
    private $rooms = '';
    /**
     * @var int
     */
    private $floor = '';


    public function __construct(
        string $id,
        string $address,
        float $area,
        int $appartmentNumber,
        int $rooms,
        int $floor
    )
    {
        $this->id = $id;
        $this->address = $address;
        $this->area = $area;
        $this->appartmentNumber = $appartmentNumber;
        $this->rooms = $rooms;
        $this->floor = $floor;
    }

    public function addPhoto(RealtyPhoto $realtyPhoto): self
    {
        $this->photos[] = $realtyPhoto;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return array|RealtyPhoto[]
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getArea(): float
    {
        return $this->area;
    }

    public function getAppartmentNumber(): int
    {
        return $this->appartmentNumber;
    }

    public function getRooms(): int
    {
        return $this->rooms;
    }

    public function getFloor(): int
    {
        return $this->floor;
    }


}