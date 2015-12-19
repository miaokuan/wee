<?php
/**
 * $Id: Form.php 473 2014-09-25 14:43:05Z svn $
 * @author miaokuan
 */

namespace Wee;

class Form
{
    public static function open($action = '', $attributes = 'method="post"', array $hidden = array())
    {
        $form = '<form action="' . $action . '" ' . $attributes . '>';
        if (!empty($hidden)) {
            foreach ($hidden as $name => $value) {
                $form .= self::hidden($name, $value);
            }
        }
        return $form;
    }

    public static function close()
    {
        return "</form>\n";
    }

    public static function hidden($name, $value = '')
    {
        $form = '';
        if (!is_array($value)) {
            $form .= '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />';
        } else {
            foreach ($value as $k => $v) {
                $k = is_numeric($k) ? '' : $k;
                $form .= '<input type="hidden" name="' . htmlspecialchars($name) . '[' . htmlspecialchars($k) . ']" value="' . htmlspecialchars($v) . '" />';
            }
        }
        return $form;
    }

    public static function input($name, $value = '', $extra = '')
    {
        return '<input type="text" id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" ' . $extra . ' />';
    }

    public static function password($name, $extra = '')
    {
        return '<input type="password" id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" ' . $extra . ' />';
    }

    public static function textarea($name, $value = '', $extra = '')
    {
        return '<textarea id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" ' . $extra . '>' . htmlspecialchars($value) . '</textarea>';
    }

    public static function submit($txt)
    {
        return '<button type="submit">' . htmlspecialchars($txt) . '</button>';
    }

    public static function select($name, array $options = array(), $selected = array(), $extra = '')
    {
        if (!is_array($selected)) {
            $selected = array($selected);
        }
        $multiple = (count($selected) > 1 && strpos($extra, 'multiple') === false) ? ' multiple="multiple"' : '';
        $form = '<select id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '" ' . $extra . $multiple . ">";
        foreach ($options as $key => $val) {
            $key = (string) $key;
            if (is_array($val) && !empty($val)) {
                $form . '<optgroup label="' . htmlspecialchars($key) . '">';
                foreach ($val as $optgroup_key => $optgroup_val) {
                    $sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';
                    $form .= '<option value="' . htmlspecialchars($optgroup_key) . '"' . $sel . '>' . htmlspecialchars((string) $optgroup_val) . '</option>';
                }
                $form .= '</optgroup>';
            } else {
                $sel = in_array($key, $selected) ? ' selected="selected"' : '';
                $form .= '<option value="' . htmlspecialchars($key) . '"' . $sel . '>' . htmlspecialchars((string) $val) . '</option>';
            }
        }
        $form .= '</select>';
        return $form;
    }

    public static function checkbox($name, array $options = array(), $checked = array())
    {
        $form = '';
        $name = (string) $name;
        $i = 0;
        foreach ($options as $key => $val) {
            $i++;
            $key = (string) $key;
            $val = (string) $val;
            $sel = (in_array($key, $checked) ? ' checked="checked"' : '');
            $form .= '<label for="_' . htmlspecialchars($name) . '_' . $i . '"><input id="_' . htmlspecialchars($name) . '_' . $i . '" type="checkbox" name="' . htmlspecialchars($name) . '[]" value="' . htmlspecialchars($key) . '" ' . $sel . ' />' . htmlspecialchars($val) . '</label>';
        }
        return $form;
    }

    public static function radio($name, array $options = array(), $checked = '')
    {
        $form = '';
        $name = (string) $name;
        $i = 0;
        foreach ($options as $key => $val) {
            $i++;
            $key = (string) $key;
            $val = (string) $val;
            $sel = ($key == $checked ? ' checked="checked"' : '');
            $form .= '<label for="_' . htmlspecialchars($name) . '_' . $i . '"><input id="_' . htmlspecialchars($name) . '_' . $i . '" type="radio" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($key) . '" ' . $sel . ' />' . htmlspecialchars($val) . '</label>';
        }
        return $form;
    }

}
