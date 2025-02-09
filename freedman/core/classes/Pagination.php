<?php

class Pagination
{
    public $count_pages = 1;
    public $current_page = 1;
    public $uri = '';
    public $mid_size = 3;
    public $all_pages = 7;
    private $page;
    private $per_page;
    private $total;

    public function __construct($page = 1, $per_page = 1, $total = 1)
    {
        $this->page = (int)$page;
        $this->per_page = (int)$per_page;
        $this->total = (int)$total;

        $this->count_pages = $this->getCountPages();
        $this->current_page = $this->getCurrentPage();
        $this->uri = $this->getParams();
        $this->mid_size = $this->getMidSize();
    }

    private function getCountPages()
    {
        return ceil($this->total / $this->per_page) ?: 1;
    }

    private function getCurrentPage()
    {
        if ($this->page < 1) {
            $this->page = 1;
        }
        if ($this->page > $this->count_pages) {
            $this->page = $this->count_pages;
        }
        return $this->page;
    }

    public function getStart()
    {
        return ($this->current_page - 1) * $this->per_page;
    }

    private function getParams()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $uri = $url[0];
        if (isset($url[1]) && $url[1] != '') {
            $uri .= '?';
            $params = explode('&', $url[1]);
            foreach ($params as $param) {
                if (strpos($param, 'page=') === false) {
                    $uri .= "{$param}&";
                }
            }
        }
        return $uri;
    }

    public function getHtml()
    {
        $back = '';
        $forward = '';
        $start_page = '';
        $end_page = '';
        $pages_left = '';
        $pages_right = '';

        if ($this->current_page > 1) {
            $back = "<li class='page-item'><a href='" . $this->getLink($this->current_page - 1) . "' class='page-link'>&lt;</a></li>";
        }

        if ($this->current_page < $this->count_pages) {
            $forward = "<li class='page-item'><a class='page-link' href='" . $this->getLink($this->current_page + 1) . "'>&gt;</a></li>";
        }

        if ($this->current_page > $this->mid_size + 1) {
            $start_page = "<li class='page-item'><a class='page-link' href='" . $this->getLink(1) . "'>&laquo;</a></li>";
        }

        if ($this->current_page < ($this->count_pages - $this->mid_size)) {
            $end_page = "<li class='page-item'><a class='page-link' href='" . $this->getLink($this->count_pages) . "'>&raquo;</a></li>";
        }

        for ($i = $this->mid_size; $i > 0; $i--) {
            if ($this->current_page - $i > 0) {
                $pages_left .= "<li class='page-item'><a class='page-link' href='" . $this->getLink($this->current_page - $i) . "'>" . ($this->current_page - $i) . "</a></li>";
            }
        }

        for ($i = 1; $i <= $this->mid_size; $i++) {
            if ($this->current_page + $i <= $this->count_pages) {
                $pages_right .= "<li class='page-item'><a class='page-link' href='" . $this->getLink($this->current_page + $i) . "'>" . ($this->current_page + $i) . "</a></li>";
            }
        }

        return '<nav aria-label="Page navigation example"><ul class="pagination">' . $start_page . $back . $pages_left . '<li class="page-item active"><a class="page-link">' . $this->current_page . '</a></li>' . $pages_right . $forward . $end_page . '</ul></nav>';
    }

    private function getLink($page)
    {
        if ($page == 1) {
            return rtrim($this->uri, '?&');
        }

        if (strpos($this->uri, '&') !== false || strpos($this->uri, '?') !== false) {
            return "{$this->uri}page={$page}";
        } else {
            return "{$this->uri}?page={$page}";
        }
    }

    private function getMidSize()
    {
        return $this->count_pages <= $this->all_pages ? $this->count_pages : $this->mid_size;
    }

    public function __toString()
    {
        return $this->getHtml();
    }
}
