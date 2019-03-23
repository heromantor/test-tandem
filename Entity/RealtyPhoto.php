<?php
namespace App\Entity;


class RealtyPhoto
{
    /**
     * @var string|null
     */
    private $url;
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id, string $url)
    {
        $this->id = $id;
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

}