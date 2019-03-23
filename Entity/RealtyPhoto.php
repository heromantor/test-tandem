<?php
namespace App\Entity;


class RealtyPhoto
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
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