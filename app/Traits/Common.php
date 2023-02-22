<?php

namespace App\Traits;

use App\Exceptions\ApiException;
use Illuminate\Support\Str;

trait Common
{

    public function randomString($length = 32): string
    {
        return Str::random($length);
    }

    public function randomPersonnelCode($length = 7): string
    {
        return 'FF-P-' . Str::random($length);
    }

    public function randomProductCode($length = 7): string
    {
        return 'FF-Pr-' . Str::random($length);
    }

    public function cleanInput(&$input, $keys)
    {
        $additional_fields = ['page', 'per_page', 'order_by'];

        array_walk($keys, function ($key) use (&$input, $additional_fields) {
            if (!in_array($key, $additional_fields)) {
                if (array_key_exists($key, $input)) {
                    if (is_array($input[$key])) {

                        if ($this->isAssoc($input[$key])) {
                            $this->cleanInput($input[$key], array_keys($input[$key]));
                        } else {
                            for ($i = 0; $i < count($input[$key]); $i++) {
                                if (is_array($input[$key][$i])) {
                                    if ($this->isAssoc($input[$key][$i])) {
                                        $this->cleanInput($input[$key][$i], array_keys($input[$key][$i]));
                                    } else {

                                        $input[$key][$i] = $this->cleanTextNumber($input[$key][$i]);
                                    }
                                } else {
                                    $input[$key][$i] = $this->cleanTextNumber($input[$key][$i]);
                                }
                            }
                        }

                    } else {
                        $input[$key] = $this->cleanTextNumber($input[$key]);
                    }
                }

            }
        });
    }

    function isAssoc(array $arr): bool
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * clean Text and replace code of invalid character
     * @param String|null $param Text to be Cleaned
     *
     * 8206 : Left To Right Mark
     * 8207 : Right To Left Mark
     * 8233 : Paragraph Separator
     * Valid : 1740 = [ی] = 1610| [ی]  and 1609 = [ى] and 1746 = [ے]
     * Valid : 1705 = [ک] | inValid :1603  = [ك]
     * Valid : 32 = [ ] | inValid : 160 = [ ]
     * Valid : 8204 = [‌] | inValid : 8206 = [‌] and 8233
     * Valid : 1607 = [ه] | inValid : 1726 = [ھ] and 1729 = [ہ] and 1749 = [ە]
     * Valid : 1574 = [ئ] | inValid : 1730 = [ۂ]
     * + Replace Persian Numbers With English Numbers
     *
     * @return String|null : Cleaned Text
     * @author Mohammad Reza Rassouli
     * @access public
     */
    private function cleanTextNumber(?string $param): ?string
    {
        if (is_array($param)) {
            $text = $param[0];
        } else {
            $text = $param;
        }

        if (is_null($text) or empty($text)) {
            return $text;
        }

        // تبدیل بیش از 1 فضای خالی به 1 فضای خالی
        $text = preg_replace('/\s\s+/', ' ', $text);

        $results = array();
        $codes = "";
        $newStr = "";
        preg_match_all('/[\W\w]/u', $text, $results);
        for ($i = 0; $i < count($results[0]); $i++) {
            $codes .= "'" . $this->unicodeTextToCode($results[0][$i]) . "',";
        }
        $codes = substr($codes, 0, strlen($codes) - 1);

        // Clean Invalid Character With NULL
        $incorrectChar = array("'8206'", "'8207'", "'8233'");
        for ($i = 0; $i < count($incorrectChar); $i++) {
            $codes = str_replace($incorrectChar[$i], "", $codes);
        }
        $codes = str_replace(",,", ",", $codes);
        // Clean Persian Number (1632-1641) With English (48-57)
        // Replace Invalid Characters with Valid Characters

        /**
         * user: behzad mirzaBabaei
         * incorrectCharactersAndNumbers & correctCharactersAndNumbers array
         * @type {Array}
         */
        $incorrectCharactersAndNumbers = array(
            "'1649'", "'65153'",
            "'65164'", "'65163'",
            "'65165'", "'65166'",
            "'65167'", "'65170'", "'65168'", "'65169'",
            "'65173'", "'65175'", "'65174'", "'65176'",
            "'65178'", "'65179'", "'65180'",
            "'65182'", "'65184'", "'65183'", "'65181'",
            "'65188'", "'65187'", "'65186'",
            "'65192'", "'65191'", "'65190'",
            "'65193'", "'65194'",
            "'65196'", "'65195'",
            "'65197'", "'65198'",
            "'65200'", "'65199'",
            "'65202'", "'65201'", "'65204'", "'65203'",
            "'65205'", "'65206'", "'65208'", "'65207'",
            "'65210'", "'65212'", "'65211'",
            "'65215'", "'65216'", "'65213'",
            "'65218'", "'65220'", "'65219'",
            "'65224'", "'65223'",
            "'65226'", "'65228'", "'65227'", "'65225'",
            "'65230'", "'65232'", "'65231'", "'65229'",
            "'65233'", "'65234'", "'65236'", "'65235'",
            "'65237'", "'65238'", "'65240'", "'65239'",
            "'65245'", "'65246'", "'65248'", "'65247'",
            "'65249'", "'65250'", "'65252'", "'65251'",
            "'65254'", "'65253'", "'65256'", "'65255'",
            "'1749'", "'1726'", "'65260'", "'65257'", "'65259'", "'65258'", "'64420'", "'1728'", "'1577'", "'1729'",
            "'1734'", "'65261'", "'65262'", "'1738'",
            "'64345'", "'64344'", "'64343'",
            "'64379'", "'64381'", "'64380'",
            "'64394'",
            "'65243'", "'65243'", "'64399'", "'64401'", "'64400'", "'1603'", "'1706'", "'65242'", "'1707'", "'65244'",
            "'64403'", "'64402'", "'64405'", "'64404'",
            "'65265'", "'65266'", "'64510'", "'65264'", "'65268'", "'65263'", "'65267'", "'64508'", "'64508'", "'64511'", "'1610'", "'1609'", "'1746'", "'1742'", "'1744'",
            "'8206'", "'8233'",
            "'8201'", "'8202'",
            "'1776'", "'1777'", "'1778'", "'1779'", "'1780'", "'1781'", "'1782'", "'1783'", "'1784'", "'1785'",
            "'1632'", "'1633'", "'1634'", "'1635'", "'1636'", "'1637'", "'1638'", "'1639'", "'1640'", "'1641'",
            "'61692'", // other char, example: ballet char
            "'160'" // breaking-space (نیم فاصله)
        );
        $correctCharactersAndNumbers = array(
            "'1570'", "'1570'",
            "'1574'", "'1574'",
            "'1575'", "'1575'",
            "'1576'", "'1576'", "'1576'", "'1576'",
            "'1578'", "'1578'", "'1578'", "'1578'",
            "'1579'", "'1579'", "'1579'",
            "'1580'", "'1580'", "'1580'", "'1580'",
            "'1581'", "'1581'", "'1581'",
            "'1582'", "'1582'", "'1582'",
            "'1583'", "'1583'",
            "'1584'", "'1584'",
            "'1585'", "'1585'",
            "'1586'", "'1586'",
            "'1587'", "'1587'", "'1587'", "'1587'",
            "'1588'", "'1588'", "'1588'", "'1588'",
            "'1589'", "'1589'", "'1589'",
            "'1590'", "'1590'", "'1590'",
            "'1591'", "'1591'", "'1591'",
            "'1592'", "'1592'",
            "'1593'", "'1593'", "'1593'", "'1593'",
            "'1594'", "'1594'", "'1594'", "'1594'",
            "'1601'", "'1601'", "'1601'", "'1601'",
            "'1602'", "'1602'", "'1602'", "'1602'",
            "'1604'", "'1604'", "'1604'", "'1604'",
            "'1605'", "'1605'", "'1605'", "'1605'",
            "'1606'", "'1606'", "'1606'", "'1606'",
            "'1607'", "'1607'", "'1607'", "'1607'", "'1607'", "'1607'", "'1607'", "'1607'", "'1607'", "'1607'",
            "'1608'", "'1608'", "'1608'", "'1608'",
            "'1662'", "'1662'", "'1662'",
            "'1670'", "'1670'", "'1670'",
            "'1688'",
            "'1705'", "'1705'", "'1705'", "'1705'", "'1705'", "'1705'", "'1705'", "'1705'", "'1705'", "'1705'",
            "'1711'", "'1711'", "'1711'", "'1711'",
            "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'", "'1740'",
            "'8204'", "'8204'",
            "'32'", "'32'",
            "'48'", "'49'", "'50'", "'51'", "'52'", "'53'", "'54'", "'55'", "'56'", "'57'",
            "'48'", "'49'", "'50'", "'51'", "'52'", "'53'", "'54'", "'55'", "'56'", "'57'",
            "'10003'", // other char, example: ballet char
            "'32'" // space
        );

        for ($i = 0; $i < count($incorrectCharactersAndNumbers); $i++) {
            $codes = str_replace($incorrectCharactersAndNumbers[$i], $correctCharactersAndNumbers[$i], $codes);
        }
        $newArray = explode(",", str_replace("'", "", $codes));
        for ($i = 0; $i < count($newArray); $i++) {
            $newStr .= $this->codeToUnicodeText($newArray[$i]);
        }

        return $newStr;
    }

    /**
     * Return Code of Unicode Text
     * @param String $u Unicode Text
     * @return float|int : Code of Unicode Text
     * @author Mohammad Reza Rassouli
     * @access public
     */
    private function unicodeTextToCode(string $u): float|int
    {
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));

        return $k2 * 256 + $k1;
    }

    /**
     * Return Unicode Text of Code
     * @param Number $num Code of Unicode Text
     * @return String : Unicode Text
     * @author Mohammad Reza Rassouli
     * @access public
     */
    private function codeToUnicodeText($num): string
    {
        if ($num < 128) {
            return chr($num);
        }
        if ($num < 2048) {
            return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        }
        if ($num < 65536) {
            return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }
        if ($num < 2097152) {
            return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }

        return '';
    }

    /**
     * @param $search_value "value" that set in input data for search
     * @param $fieldStr "field" name in format like this
     * fieldStr sample fieldType1:FieldName1;FieldName2|fieldType2:fieldName3;FieldName3;FieldName5
     * @param string $table sql table name if empty must set name before each fields
     * @param string $condition_kind for each sections default is OR
     * @return array
     */
    public function GWC($search_value, $fieldStr, string $table = '', string $condition_kind = 'OR'): array
    {
        if (is_null($search_value) || $search_value == 'null') {
            return [];
        }
        $filed1 = explode('|', $fieldStr ?? '');
        foreach ($filed1 as $field_id => $field_names) {
            $filed2 = explode(':', $field_names ?? '');
            $filed3 = explode(';', $filed2[1] ?? '');
            foreach ($filed3 as $key => $value) {
                $field [] = [
                    'fieldType' => $filed2[0],
                    'fieldName' => $value
                ];
            }
        }
        return [
            'condition_kind' => $condition_kind,
            'table' => $table,
            'search_value' => $search_value ?? '',
            'fields' => $field
        ];
    }

    /**
     * @param $all_data
     * @param $param_array
     * @param string $condition_between_word if you change to OR conditions between two word change to OR
     * @return string
     */
    public function generateWhereCondition($all_data, &$param_array, string $condition_between_word = 'AND'): string
    {
        $all_data = array_filter($all_data);
        foreach ($all_data as $data) {
            if ($data['search_value'] != '') {
                $table = $data['table'];
                $search_value = $data['search_value'];
                $condition_kind = $data['condition_kind'];
                foreach ($data['fields'] as $field_id => $field_value) {
                    $field_type = $field_value['fieldType'];
                    $field_name = $field_value['fieldName'];
                    $field_whole_name = $table == '' ? $field_name : "$table.$field_name";

                    switch ($field_type) {
                        case 'number':
                            $conditions [] = "$field_whole_name = ?";
                            $param_array[] = $search_value;
                            break;

                        case 'array':
                            if (is_array($search_value)) {
                                $array_data = '';
                                foreach ($search_value as $item) {
                                    $array_data .= '?,';
                                    $param_array[] = $item;
                                }
                                $array_data = rtrim($array_data, ',');
                            } else {
                                $array_data = '?';
                                $param_array[] = $search_value;
                            }
                            $conditions [] = "$field_whole_name IN ($array_data)";
                            break;

                        case 'date':
                            if (strlen(trim($search_value)) == 10) {
                                $date_yaer = (int)substr($search_value, 0, 4);
                                $date_month = (int)substr($search_value, 5, 2);
                                $date_day = (int)substr($search_value, 8, 2);
                                if (\Morilog\Jalali\CalendarUtils::checkDate($date_yaer, $date_month, $date_day, true)) {
                                    $date = \Morilog\Jalali\CalendarUtils::toGregorian($date_yaer, $date_month, $date_day);
                                    $date = implode('-', $date);
                                    $conditions [] = "$field_whole_name = ?";
                                    $param_array[] = $date;
                                }
                            }
                            break;

                        case 'string':
                            $conditions [] = "$field_whole_name like ?";
                            $param_array[] = "%$search_value%";
                            break;

                        case 'advanced':
                            // eq, gt, lt, gte, lte, between
                            $operand = $search_value[0];
                            $value = $search_value[1];
                            $param_array[] = $value;

                            switch ($operand) {
                                case 'eq':
                                    if (strlen($value) > 10) { // For Timestamp columns
                                        $conditions [] = "DATE_FORMAT($field_whole_name,'%Y/%m/%d %H:%i') = ?";
                                    } else {
                                        $conditions [] = "$field_whole_name = ?";
                                    }
                                    break;
                                case 'gt':
                                    $conditions [] = "$field_whole_name > ?";
                                    break;
                                case 'lt':
                                    $conditions [] = "$field_whole_name < ?";
                                    break;
                                case 'gte':
                                    $conditions [] = "$field_whole_name >= ?";
                                    break;
                                case 'lte':
                                    $conditions [] = "$field_whole_name <= ?";
                                    break;
                                case 'between':
                                    $second_value = $search_value[2];
                                    $param_array[] = $second_value;

                                    $conditions [] = "$field_whole_name >= ? and $field_whole_name <= ?";
                                    break;
                            }
                            break;

                        default:
                            break;
                    }
                }
                $conditional_sentence_per_section [] = '(' . implode(' ' . $condition_kind . ' ', $conditions) . ')';
                $conditions = [];
            }
        }
        if (isset($conditional_sentence_per_section)) {
            $where = '(' . implode(' ' . $condition_between_word . ' ', $conditional_sentence_per_section) . ')';
        }

        if (isset($where) && $where) {
            $return_value = $where;
        } else {
            $return_value = '?';
            $param_array[] = true;
        }
        return $return_value;
    }

    /**
     * ساختارمند کردن پیغام خروجی validation
     * @param $data
     * @return array
     */
    public function structureValidationMessage($data): array
    {
        $message = [];
        foreach ($data->messages() as $item) {
            $message[] = $item[0];
        }

        return $message;
    }

    public function errorHandling(\Exception $e): string
    {
        $message = '';
        switch ($e->getCode()) {
            case 23000:
                if ($e->errorInfo[1] == 1062) {
                    $message = __('messages.this_values_is_duplicated');
                } elseif ($e->errorInfo[1] == 1451) {
                    $message = __('messages.this_record_used');
                } elseif ($e->errorInfo[1] == 1452) {
                    $message = __('messages.bad_request');
                }
                break;
            default:
                $message = __('messages.error_process_data');
                break;
        }

        return $message;
    }

    public function calculatePerPage($inputs): int
    {
        $per_page = 10;
        if (isset($inputs['per_page'])) {
            if ($inputs['per_page'] > 100) {
                $per_page = 50;
            } elseif ($inputs['per_page'] == -1) {
                $per_page = 500;
            } else {
                $per_page = $inputs['per_page'];
            }
        }

        return $per_page;
    }

    public function orderBy($inputs, $table): string
    {
        if (isset($inputs['order_by'])) {
            $order_by_arr = explode('-', $inputs['order_by'] ?? '');
            $order_by = '';
            foreach ($order_by_arr as $value) {
                list($field, $type) = explode(':', $value ?? '');
                $order_by .= "$table.$field $type,";
            }
            $order_by = rtrim($order_by, ',');
        } else {
            $order_by = "$table.id desc";
        }

        return $order_by;
    }

    public function convertModelNameToNamespace($model_name): string
    {
        return match ($model_name) {
            'place' => \App\Models\Place::class,
            'company' => \App\Models\Company::class,
            'person' => \App\Models\Person::class,
            'customer' => \App\Models\Customer::class,
            'cloth_buy' => \App\Models\ClothBuy::class,
            'accessory_buy' => \App\Models\AccessoryBuy::class,
            'salary' => \App\Models\Salary::class,
        };
    }

    /**
     * تبدیل code به id
     * @param $resource
     * @param $resource_id
     * @return string
     * @throws ApiException
     */
    public function getResourceId($resource, $resource_id): string
    {
        try {
            return match ($resource) {
                'user' => \App\Models\User::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                'account' => \App\Models\Account::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                'person' => \App\Models\Person::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                'notif' => \App\Models\Notif::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                'company' => \App\Models\Company::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                'cloth' => \App\Models\Cloth::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                'product' => \App\Models\Product::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                'customer' => \App\Models\Customer::query()->select('id')->whereCode($resource_id)->first()->id ?? 0,
                default => $resource_id,
            };
        } catch (\Exception $e) {
            throw new ApiException($e);
        }
    }

    /**
     * شرکت جاری کاربر
     * @param $user
     * @return int
     * @throws ApiException
     */
    public function getCurrentCompanyOfUser($user): int
    {
        try {
            $person_company = $user->person->person_company->where('is_enable', 1)->first();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }

        return $person_company->company_id;
    }

}
