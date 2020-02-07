<?php
namespace App\Entity;

/**
 * Фотография объекта недвижимости
 */
class RealtyPhoto
{
    /**
     * @var string ID
     */
    private $id;
    /**
     * @var string URL
     */
    private $url;

    public function __construct(string $id, string $url)
    {
        $this->id = $id;
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getId(): string
    {
        return $this->id;
    }
}