<?php
/**
 * $Id: Pager.php 473 2014-09-25 14:43:05Z svn $
 * @author miaokuan
 */

namespace Wee;

class Pager
{
    public static function render($count, $page, $pagesize = 20,
        $urlmod = '?pn={pn}', $maxpage = 100, $size = 10,
        $pre = '&laquo;', $next = '&raquo;') {
        $multi = '';
        $holder = '{pn}';

        if ($count > $pagesize) {
            $offset = ceil($size / 2) - 1;
            $pages = max(1, ceil($count / $pagesize));
            if ($maxpage > 0) {
                $pages = min($pages, $maxpage);
            }
            $page = min($page, $pages);

            if ($size > $pages) {
                $from = 1;
                $to = $pages;
            } else {
                $from = $page - $offset;
                $to = $from + $size - 1;
                if ($from < 1) {
                    $to = $page + 1 - $from;
                    $from = 1;
                    if ($to - $from < $size) {
                        $to = $size;
                    }
                } elseif ($to > $pages) {
                    $from = $pages - $size + 1;
                    $to = $pages;
                }
            }

            if ($page > 1) {
                $multi .= '<li><a href="' . str_replace($holder, $page - 1, $urlmod)
                    . '">' . $pre . '</a></li>';
            }
            if ($page - $offset > 1 && $pages > $size) {
                $multi .= '<li><a href="' . str_replace($holder, 1, $urlmod) . '">1</a></li>';

                if ($page - $offset > 2) {
                    $multi .= '<li><a href="' . str_replace($holder, 2, $urlmod) . '">2</a></li>';
                }

                $multi .= '<li><span>...</span></li>';
            }

            for ($i = $from; $i <= $to; $i++) {
                $multi .= $i == $page ? '<li class="active"><span>' . $i . '</span></li>'
                : '<li><a href="' . str_replace($holder, $i, $urlmod) . '">' . $i . '</a></li>';
            }

            if ($to < $pages) {
                $multi .= '<li><span>...</span></li>';

                if ($to < $pages - 1) {
                    $multi .= '<li><a href="' . str_replace($holder, $pages - 1, $urlmod)
                        . '">' . ($pages - 1) . '</a></li>';
                }
                $multi .= '<li><a href="' . str_replace($holder, $pages, $urlmod) . '">' . $pages . '</a></li>';
            }

            if ($page < $pages) {
                $multi .= '<li><a href="' . str_replace($holder, $page + 1, $urlmod)
                    . '">' . $next . '</a></li>';
            }
        }

        return $multi ? '<ul>' . $multi . '</ul>' : '';
    }

    public static function bootstrap($count, $page, $pagesize = 20,
        $urlmod = '?pn={pn}', $maxpage = 100, $size = 10,
        $pre = '&laquo;', $next = '&raquo;') {
        $multi = '';
        $holder = '{pn}';

        if ($count > $pagesize) {
            $offset = ceil($size / 2) - 1;
            $pages = max(1, ceil($count / $pagesize));
            if ($maxpage > 0) {
                $pages = min($pages, $maxpage);
            }
            $page = min($page, $pages);

            if ($size > $pages) {
                $from = 1;
                $to = $pages;
            } else {
                $from = $page - $offset;
                $to = $from + $size - 1;
                if ($from < 1) {
                    $to = $page + 1 - $from;
                    $from = 1;
                    if ($to - $from < $size) {
                        $to = $size;
                    }
                } elseif ($to > $pages) {
                    $from = $pages - $size + 1;
                    $to = $pages;
                }
            }

            // previous
            if ($page > 1) {
                $multi .= '<li><a  href="' . str_replace($holder, max(1, $page - 1), $urlmod)
                    . '" aria-label="Previous"><span aria-hidden="true">' . $pre . '</span></a></li>';
            }

            if ($page - $offset > 1 && $pages > $size) {
                $multi .= '<li><a href="' . str_replace($holder, 1, $urlmod) . '">1</a></li>';

                if ($page - $offset > 2) {
                    $multi .= '<li><a href="' . str_replace($holder, 2, $urlmod) . '">2</a></li>';
                }

                $multi .= '<li><span>...</span></li>';
            }

            //  <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
            //  <li><a href="#">3</a></li>
            for ($i = $from; $i <= $to; $i++) {
                $multi .= $i == $page ? '<li class="active"><a>' . $i . '</a></li>'
                : '<li><a href="' . str_replace($holder, $i, $urlmod) . '">' . $i . '</a></li>';
            }

            if ($to < $pages) {
                $multi .= '<li><span>...</span></li>';

                if ($to < $pages - 1) {
                    $multi .= '<li><a href="' . str_replace($holder, $pages - 1, $urlmod)
                        . '">' . ($pages - 1) . '</a></li>';
                }
                $multi .= '<li><a href="' . str_replace($holder, $pages, $urlmod) . '">' . $pages . '</a></li>';
            }

            // next
            //  <li class="next"><a href="#"><img src="/img/next.png"></a></li>
            if ($page < $pages) {
                $multi .= '<li class="next"><a href="' . str_replace($holder, $page + 1, $urlmod)
                    . '">' . $next . '</a></li>';
            }
        }

        return $multi ? '<nav><ul class="pagination">' . $multi . '</ul></nav>' : '';
    }
}
