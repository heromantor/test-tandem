<?php
namespace App\Entity;

use DateTimeImmutable;

/**
 * Предложение недвижимости
 */
class RealtyOffer
{
    /**
     * @var Realty Объект недвижимости
     */
    private $realty;
    /**
     * @var Client Клиент
     */
    private $client;
    /**
     * @var DateTimeImmutable|null Дата обновления
     */
    private $updatedAt;
    /**
     * @var DateTimeImmutable|null Дата удаления
     */
    private $deletedAt;
    /**
     * @var DateTimeImmutable Дата создания
     */
    private $createdAt;

    public function __construct(
        Client $client,
        Realty $realty,
        DateTimeImmutable $createdAt
    )
    {
        $this->client = $client;
        $this->realty = $realty;
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getRealty(): Realty
    {
        return $this->realty;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}