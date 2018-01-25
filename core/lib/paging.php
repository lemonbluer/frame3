<?php
namespace frame3\core\lib;
/**
 * @name 分页工具
 */
class paging {

    public $current_page; // 当前页数，默认为1
    public $pages_count; // 总页数目
    public $target_url; // 分页地址
    public $html;

    /**
     * @param int   $current_page [description]
     * @param int   $pages_count  [description]
     * @param array $target_url         [description]
     * @param array $options
     */
    function __construct($current_page = 1, $pages_count = 1, $target_url = '', $options = []) {
        $this->current_page = $current_page;
        $this->pages_count = $pages_count;
        $this->target_url = $target_url;
        if (isset($options['html'])) {
            $this->html = [
                'first' => $options['html']['first'],
                'last' => $options['html']['last'],
                'pages' => $options['html']['pages'],
                'template' => $options['html']['template'],
                'template' => $options['html']['template'],
            ];
        } else {
            $this->html = [
                'first' => <<<EOT
<li __CLASS__><a __URL__ aria-label="First">
    <span aria-hidden="true">&laquo;</span>
</a></li>
EOT
                ,
                'last'=><<<EOT
<li __CLASS__><a __URL__ aria-label="Last">
    <span aria-hidden="true">&raquo;</span>
</a></li>
EOT
                ,
                'pages'=><<<EOT
<li __CLASS__><a __URL__>__NUM__</a></li>
EOT
                ,
                'template'=><<<EOT
<nav class="page" aria-label="Page navigation">
  <ul class="pagination pagination-sm">
    __FIRST__
    __PAGES__
    __LAST__
  </ul>
</nav>
EOT
            ];
        }
    }

    /**
     * @name 分页的html
     * @return [type] [description]
     */
    public function html() {
        // 首页
        $class = '';
        $url = '';
        if ($this->current_page == 1) {
            $class = 'class="disabled"';
        } else {
            $url = 'href="' . $this->target_url . '"';
        }
        $first = str_replace(['__CLASS__', '__URL__'], [$class, $url], $this->html['first']);

        // 内容1|2|3
        $pages = '';
        for ($i = 1; $i <= $this->pages_count; $i++) {
            if ($i == $this->current_page) {
                $class = 'class="disabled"';
                $url = '';
            } else {
                $class = '';
                $url = 'href="' . $this->target_url . '?' . http_build_query(array_merge($_GET, ['page' => $i])) . '"';
            }
            $pages = $pages . str_replace(['__CLASS__', '__URL__', '__NUM__'], [$class, $url, $i], $this->html['pages']);
        }

        // 尾页
        if ($this->current_page == $this->pages_count) {
            $class = 'class="disabled"';
            $url = '';
        } else {
            $class = '';
            $url = 'href="' . $this->target_url . '?' . http_build_query(array_merge($_GET, ['page' => $this->pages_count])) . '"';
        }
        $last = str_replace(['__CLASS__', '__URL__'], [$class, $url], $this->html['last']);

        $html = str_replace(['__FIRST__', '__PAGES__', '__LAST__'], [$first, $pages, $last], $this->html['template']);
        return $html;
    }
}