<?php

namespace WebSK\CRUD\Table\Filters;

use Slim\Http\Request;
use WebSK\CRUD\Table\InterfaceCRUDTableFilter;
use WebSK\CRUD\Table\InterfaceCRUDTableFilterVisible;

/**
 * Class CRUDTableFilterGroupCollapse
 * @package WebSK\CRUD\Table\Filters
 */
class CRUDTableFilterGroupCollapse implements InterfaceCRUDTableFilterGroup
{
    protected string $filter_uniq_id;
    protected string $title;

    /** @var InterfaceCRUDTableFilter[] */
    protected array $filters_arr;

    /**
     * CRUDTableFilterGroupCollapse constructor.
     * @param string $filter_uniq_id
     * @param string $title
     * @param array $filters_arr
     */
    public function __construct(
        string $filter_uniq_id,
        string $title,
        array $filters_arr = []
    ) {
        $this->setFilterUniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFiltersArr($filters_arr);
    }

    /** @inheritdoc */
    public function sqlConditionAndPlaceholderValue(Request $request): array
    {
        return ['', []];
    }

    /** @inheritdoc */
    public function getHtml(Request $request): string
    {
        $filters_arr = $this->getFiltersArr();

        if (!$filters_arr) {
            return '';
        }

        $html = '';

        $html .= '<div class="form-group">';
        $html .= '<label class="col-sm-4 text-right control-label"><a data-toggle="collapse" href="#' . $this->getFilterUniqId() . '" aria-expanded="false" aria-controls="report_filters" class="collapsed"><span class="fa fa-angle-down"></span> '. $this->getTitle() .'</a></label>';
        $html .= '</div>';

        $html .= '<div class="form-group collapse" id="' . $this->getFilterUniqId() . '" aria-expanded="false">';
        foreach ($filters_arr as $filter_obj) {
            if (!($filter_obj instanceof InterfaceCRUDTableFilterVisible)) {
                throw new \Exception('Filter doesnt implement interface');
            }

            $html .= '<div class="col-md-12">';
            $html .= '<div class="form-group">';

            $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getTitle() . '</label>';
            $html .= '<div class="col-sm-8">' . $filter_obj->getHtml($request) . '</div>';

            $html .= '</div>';
            $html .= '</div>';

        }
        $html .= '</div>';

        return $html;
    }

    /**
     * @return string
     */
    public function getFilterUniqId(): string
    {
        return $this->filter_uniq_id;
    }

    /**
     * @param string $filter_uniq_id
     */
    public function setFilterUniqId(string $filter_uniq_id): void
    {
        $this->filter_uniq_id = $filter_uniq_id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return InterfaceCRUDTableFilter[]
     */
    public function getFiltersArr(): array
    {
        return $this->filters_arr;
    }

    /**
     * @param InterfaceCRUDTableFilter[] $filters_arr
     */
    public function setFiltersArr(array $filters_arr): void
    {
        $this->filters_arr = $filters_arr;
    }
}