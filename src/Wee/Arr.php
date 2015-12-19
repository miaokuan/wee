<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Arr
{

    public static function shuffle($list)
    {
        if (!is_array($list)) {
            return $list;
        }

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }

        return $random;
    }

    /**
     * alias array_column
     * 从数组中提取一个值作为键值， 一个值作为值组成一个新的数组
     * @param array $value
     * @param string $columnKey
     * @param string $indexKey
     * @return array
     */
    public static function column(array $input, $columnKey, $indexKey = null)
    {
        $result = array();

        if (null === $indexKey) {
            if (null === $columnKey) {
                // trigger_error('What are you doing? Use array_values() instead!', E_USER_NOTICE);
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }

        return $result;
    }

}
