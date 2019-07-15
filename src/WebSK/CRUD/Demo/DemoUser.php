<?php

namespace WebSK\CRUD\Demo;

use WebSK\Entity\Entity;

/**
 * Class DemoUser
 * @package WebSK\CRUD\Demo
 */
class DemoUser extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'crud.demo_user_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'crud.demo_user_repository';
    const DB_TABLE_NAME = 'crud_demo_users';

    const _NAME = 'name';
    /** @var string */
    protected $name = '';

    const _FIRST_NAME = 'first_name';
    /** @var string */
    protected $first_name = '';

    const _LAST_NAME = 'last_name';
    /** @var string */
    protected $last_name = '';

    /** @var string */
    protected $birthday = '';

    /** @var string */
    protected $phone = '';

    const _EMAIL = 'email';
    /** @var string */
    protected $email = '';

    /** @var string */
    protected $city = '';

    /** @var string */
    protected $address = '';

    /** @var string */
    protected $company = '';

    /** @var string */
    protected $comment = '';

    /** @var string */
    protected $photo = '';

    const _PASSW = 'passw';
    /** @var string */
    protected $passw;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    /**
     * @param null|string $first_name
     */
    public function setFirstName(?string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return null|string
     */
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    /**
     * @param null|string $last_name
     */
    public function setLastName(?string $last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * Путь к фото
     * @return string
     */
    public function getPhotoPath()
    {
        if (!$this->getPhoto()) {
            return '';
        }

        return 'user/'. $this->getPhoto();
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getPassw(): string
    {
        return $this->passw;
    }

    /**
     * @param string $passw
     */
    public function setPassw(string $passw): void
    {
        $this->passw = $passw;
    }
}
