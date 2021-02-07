<?php

namespace WebSK\CRUD\Demo;

use WebSK\Entity\Entity;

/**
 * Class DemoUser
 * @package WebSK\CRUD\Demo
 */
class DemoUser extends Entity
{
    const DB_TABLE_NAME = 'crud_demo_users';

    const _NAME = 'name';
    protected string $name = '';

    const _FIRST_NAME = 'first_name';
    protected string $first_name = '';

    const _LAST_NAME = 'last_name';
    protected string $last_name = '';

    const _EMAIL = 'email';
    protected string $email = '';

    const _BIRTHDAY = 'birthday';
    protected string $birthday = '';

    const _PHONE = 'phone';
    protected string $phone = '';

    const _CITY = 'city';
    protected string $city = '';

    const _ADDRESS = 'address';
    protected string $address = '';

    const _COMPANY = 'company';
    protected string $company = '';

    const _COMMENT = 'comment';
    protected string $comment = '';

    const _PHOTO = 'photo';
    protected string $photo = '';

    const _PASSW = 'passw';
    protected ?string $passw = null;

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
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName(string $last_name): void
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
     * @return string|null
     */
    public function getPassw(): ?string
    {
        return $this->passw;
    }

    /**
     * @param string|null $passw
     */
    public function setPassw(?string $passw): void
    {
        $this->passw = $passw;
    }
}
