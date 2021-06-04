<?php

namespace WebSK\CRUD\Demo;

use WebSK\Entity\Entity;

/**
 * Class DemoCompany
 * @package WebSK\CRUD\Demo
 */
class DemoCompany extends Entity
{
    const DB_TABLE_NAME = 'crud_demo_company';

    const _NAME = 'name';
    protected string $name = '';

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
}
