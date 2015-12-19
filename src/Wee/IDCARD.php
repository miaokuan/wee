<?php
/**
 * 身份证验证
 * @author miaokuan
 */

namespace Wee;

class IDCARD
{
    public static function factory()
    {
        return new self();
    }

    public function valid($id_card)
    {
        $id_card = $this->to18($id_card);

        if (strlen($id_card) == 18) {
            return $this->checksum18($id_card);
        }

        return false;
    }

    public function to18($idcard)
    {
        if (strlen($idcard) == 18) {
            return $idcard;
        }

        if (strlen($idcard) == 15) {
            if (in_array(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
            $idcard .= $this->getVerifyNumber($idcard);
            return $idcard;
        }

        return '';
    }

    private function getVerifyNumber($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];

        return $verify_number;
    }

    private function checksum18($idcard)
    {
        if (strlen($idcard) != 18) {
            return false;
        }

        $idcard_base = substr($idcard, 0, 17);
        if ($this->getVerifyNumber($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

    private function countAge($birth_year, $birth_month, $birth_date, $type = 0)
    {
        $now_age = 1;
        $full_age = 0;
        $now_year = date('Y', time());
        $now_date_num = date('z', time());
        $birth_date_num = date('z', mktime(0, 0, 0, $birth_month, $birth_date, $birth_year));
        $difference = $now_date_num - $birth_date_num;
        if ($difference > 0) {
            $full_age = $now_year - $birth_year;
        } else {
            $full_age = $now_year - $birth_year - 1;
        }
        $now_age = $full_age + 1;

        if ($type == 0) {
            return $now_age;
        } else {
            return $full_age;
        }
    }

    public function getAge($idcard, $type = 0)
    {
        if ($this->valid($idcard) === true) {
            $NewIDcard = $this->to18($idcard);
            $birth = substr($NewIDcard, 6, 8);
            $birth_year = substr($birth, 0, 4);
            $birth_month = substr($birth, 4, 2);
            $birth_date = substr($birth, 6, 2);
            return $this->countAge(substr($birth, 0, 4), substr($birth, 4, 2), substr($birth, 6, 2), $type);
        } else {
            return null;
        }
    }

    public function isMale($idcard)
    {
        if ($this->valid($idcard) === true) {
            $NewIDcard = $this->to18($idcard);
            $_S = substr($NewIDcard, 16, 1);
            return ($_S % 2 == 0) ? false : true;
        } else {
            return null;
        }
    }

    public function getBirthday($idcard, $type = '-')
    {
        if ($this->valid($idcard) == true) {
            $NewIDcard = $this->to18($idcard);
            return substr($idcard, 6, 4) . $type . substr($idcard, 10, 2) . $type . substr($idcard, 12, 2);
        } else {
            return '0000' . $type . '00' . $type . '00';
        }
    }

    public function checkBirthday($idcard)
    {
        if ($this->valid($idcard) === true) {
            $now_year = date('Y');
            $NewIDcard = $this->to18($idcard);
            $birth_year = substr($idcard, 6, 4);
            $birth_m = substr($idcard, 10, 2);
            $birth_d = substr($idcard, 12, 2);
            if ($birth_year > $now_year || $birth_year < 1900) {
                return false;
            } else {
                if (checkdate($birth_m, $birth_d, $birth_year)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

}
