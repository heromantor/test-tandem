<?php
namespace App\Entity;

/**
 * Клиент
 */
class Client
{
    /**
     * @var string Идентификатор
     */
    private $id;
    /**
     * @var string Имя
     */
    private $name = '';
    /**
     * @var string Адрес
     */
    private $address = '';
    /**
     * @var string Телефон
     */
    private $phone = '';

    public function __construct(
        string $id,
        string $address,
        string $name,
        string $phone
    )
    {
        $this->id = $id;
        $this->address = $address;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}