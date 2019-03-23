<?php
namespace App\Entity;


class Client
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $name = '';
    /**
     * @var string
     */
    private $address = '';
    /**
     * @var string
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