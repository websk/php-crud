<?php

namespace WebSK\CRUD\Table\Filters;

use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterEqualInline
 * @package WebSK\CRUD
 */
class CRUDTableFilterEqualInline extends CRUDTableFilterEqual
    implements InterfaceCRUDTableFilterVisible
{
    /**
     * CRUDTableFilterEqualInline constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param string $field_name
     * @param string $placeholder
     * @param int $timeout
     */
    public function __construct(
        string $filter_uniq_id,
        string $title,
        string $field_name,
        string $placeholder = '',
        int $timeout = self::DEFAULT_TIMEOUT
    ) {
        $this->setInline(true);

        parent::__construct($filter_uniq_id, $title, $field_name, $placeholder, $timeout);
    }
}
