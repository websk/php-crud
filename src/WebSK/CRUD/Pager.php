<?php

namespace WebSK\CRUD;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Pager
 * @package WebSK\CRUD
 */
class Pager
{
    /**
     * @param string $table_index_on_page
     * @return string
     */
    protected static function pageSizeFormFieldName(string $table_index_on_page): string
    {
        return 'table_' . $table_index_on_page . '_page_size';
    }

    /**
     * @param string $table_index_on_page
     * @return string
     */
    protected static function pageOffsetFormFieldName(string $table_index_on_page): string
    {
        return 'table_' . $table_index_on_page . '_' . 'page_offset';
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @return int
     */
    public static function getPageOffset(ServerRequestInterface $request, string $table_index_on_page): int
    {
        $page_offset = 0;
        $page_offset_param = $request->getParam(self::pageOffsetFormFieldName($table_index_on_page), null);
        if (is_null($page_offset_param)) {
            return $page_offset;
        }

        $page_offset = intval($page_offset_param);
        if ($page_offset < 0) {
            $page_offset = 0;
        }
        return $page_offset;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @param int $default_page_size
     * @return int
     */
    public static function getPageSize(
        ServerRequestInterface $request,
        string $table_index_on_page,
        int $default_page_size = CRUD::DEFAULT_PAGE_SIZE
    ): int
    {
        $page_size_param = $request->getParam(self::pageSizeFormFieldName($table_index_on_page), null);
        if (is_null($page_size_param)) {
            return $default_page_size;
        }

        $page_size = intval($page_size_param);
        if ($page_size < 1) {
            return $default_page_size;
        }
        if ($page_size > 1000) {
            return $default_page_size;
        }

        return $page_size;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @param int $default_page_size
     * @return int
     */
    protected static function getNextPageStart(
        ServerRequestInterface $request,
        string $table_index_on_page,
        int $default_page_size = CRUD::DEFAULT_PAGE_SIZE
    ): int
    {
        $start = self::getPageOffset($request, $table_index_on_page);
        $page_size = self::getPageSize($request, $table_index_on_page, $default_page_size);
        return $start + $page_size;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @param int $default_page_size
     * @return int
     */
    protected static function getPrevPageStart(
        ServerRequestInterface $request,
        string $table_index_on_page,
        int $default_page_size = CRUD::DEFAULT_PAGE_SIZE
    ): int
    {
        $start = self::getPageOffset($request, $table_index_on_page);
        $page_size = self::getPageSize($request, $table_index_on_page, $default_page_size);
        return $start - $page_size;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @return bool
     */
    protected static function hasPrevPage(ServerRequestInterface $request, string $table_index_on_page): bool
    {
        $start = self::getPageOffset($request, $table_index_on_page);

        if ($start > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @param int $elements_count Количество элементов на текущей странице. Если меньше размера страницы - значит,
     * следующей страницы нет. Если null - значит оно не передано (т.е. неизвестно),
     * при этом считаем что следующая страница есть.
     * @param int $default_page_size
     * @return bool
     */
    protected static function hasNextPage(
        ServerRequestInterface $request,
        string $table_index_on_page,
        int $elements_count,
        int $default_page_size = CRUD::DEFAULT_PAGE_SIZE
    ): bool
    {
        if (is_null($elements_count)) {
            return true;
        }

        $page_size = self::getPageSize($request, $table_index_on_page, $default_page_size);

        if ($elements_count < $page_size) {
            return false;
        }

        return true;
    }

    /**
     * "Дальше" рисуется всегда, если параметр $elements_count не передан
     *
     * @param ServerRequestInterface $request
     * @param string $table_index_on_page
     * @param int|null $elements_count
     * @param bool $display_total_rows_count
     * @param int $total_rows_count
     * @param int $default_page_size
     * @return string
     */
    public static function renderPager(
        ServerRequestInterface $request,
        string $table_index_on_page,
        ?int $elements_count = null,
        bool $display_total_rows_count = false,
        int $total_rows_count = 0,
        int $default_page_size = CRUD::DEFAULT_PAGE_SIZE
    ): string {
        $pager_needed = false;
        if (self::hasPrevPage($request, $table_index_on_page)) {
            $pager_needed = true;
        }

        if (is_null($elements_count) ||
            self::hasNextPage($request, $table_index_on_page, $elements_count, $default_page_size) ||
            $display_total_rows_count
        ) {
            $pager_needed = true;
        }

        $html = '<ul class="pagination" data-page-size="' .
            self::getPageSize($request, $table_index_on_page, $default_page_size) . '" data-page-offset="' .
            self::getPageOffset($request, $table_index_on_page) . '">';

        if ($pager_needed) {
            $request_query_params_arr = $request->getQueryParams();
            if (self::hasPrevPage($request, $table_index_on_page)) {
                $first_page_query_params_arr = $request_query_params_arr;
                $first_page_query_params_arr[self::pageOffsetFormFieldName($table_index_on_page)] = 0;
                $first_page_query_params_arr[self::pageSizeFormFieldName($table_index_on_page)] =
                    self::getPageSize($request, $table_index_on_page, $default_page_size);
                $html .= '<li><a data-page-offset="0" href="?' .
                    http_build_query($first_page_query_params_arr) .
                    '"><span class="glyphicon glyphicon-home"></span> 0-' .
                    self::getPageSize($request, $table_index_on_page, $default_page_size) .
                    '</a></li>';

                $previous_page_query_params_arr = $request_query_params_arr;
                $previous_page_query_params_arr[self::pageOffsetFormFieldName($table_index_on_page)] =
                    self::getPrevPageStart($request, $table_index_on_page, $default_page_size);
                $previous_page_query_params_arr[self::pageSizeFormFieldName($table_index_on_page)] =
                    self::getPageSize($request, $table_index_on_page, $default_page_size);

                $html .= '<li><a data-page-offset="' . self::getPrevPageStart($request, $table_index_on_page, $default_page_size) .
                    '" href="?' . http_build_query($previous_page_query_params_arr) .
                    '"><span class="glyphicon glyphicon-arrow-left"></span> ' .
                    self::getPrevPageStart($request, $table_index_on_page, $default_page_size) . '-' .
                    (self::getPrevPageStart($request, $table_index_on_page, $default_page_size) + self::getPageSize($request, $table_index_on_page, $default_page_size)) .
                    '</a></li>';
            } else {
                $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>';
                $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-left"></span></a></li>';
            }

            $html .= '<li class="active"><a data-page-offset="' .
                self::getPageOffset($request, $table_index_on_page) .
                '" href="#">' . self::getPageOffset($request, $table_index_on_page) . '-' .
                (self::getPageOffset($request, $table_index_on_page) + self::getPageSize($request, $table_index_on_page, $default_page_size)) .
                '</a></li>';

            if (!$elements_count || self::hasNextPage($request, $table_index_on_page, $elements_count, $default_page_size)) {
                $next_page_query_params_arr = $request_query_params_arr;
                $next_page_query_params_arr[self::pageOffsetFormFieldName($table_index_on_page)] =
                    self::getNextPageStart($request, $table_index_on_page, $default_page_size);
                $next_page_query_params_arr[self::pageSizeFormFieldName($table_index_on_page)] =
                    self::getPageSize($request, $table_index_on_page, $default_page_size);

                $html .= '<li><a data-page-offset="' . self::getNextPageStart($request, $table_index_on_page, $default_page_size) .
                    '" class="next-page" href="?' . http_build_query($next_page_query_params_arr) . '">' .
                    self::getNextPageStart($request, $table_index_on_page, $default_page_size) . '-' .
                    (self::getNextPageStart($request, $table_index_on_page, $default_page_size) + self::getPageSize($request, $table_index_on_page, $default_page_size)) .
                    ' <span class="glyphicon glyphicon-arrow-right"></span></a></a></li>';
            } else {
                $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-right"></span></a></li>';
            }

            if ($display_total_rows_count) {
                $html .= '<li class="disabled"><a href="#">Всего записей: ' . $total_rows_count . '</a></li>';
            }
        }

        $html .= "</ul>";

        return $html;
    }
}
