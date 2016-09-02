<?php
/**
 * Created by PhpStorm.
 * User: warezgibzzz
 * Date: 30.08.16
 * Time: 1:17
 */
namespace Warezgibzzz\Fields;


use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class TzMultiField
 */
class TzMultiField
{
    const USER_TYPE_SETTINGS_CODE = 'USER_TYPE_SETTINGS';

    /**
     * Metadata for custom IBlock property.
     *
     * @return array
     */
    public function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "WarezgibzzzTzMultiField",
            "DESCRIPTION" => "Sortable MultiField",
            //optional handlers

            "PrepareSettings" => array(__CLASS__, "PrepareSettings"),
            "CheckFields" => array(__CLASS__, "CheckFields"),
            "GetLength" => array(__CLASS__, "GetLength"),
            "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
            "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
            "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
            "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
            "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
            "GetPublicEditHTML" => array(__CLASS__, "GetPublicEditHTML"),
            "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML")
        );
    }

    public function PrepareSettings($arFields)
    {
        $values = array(
            'SHOW_ORDER' => false,
            'INITIAL_FIELD_COUNT' => 10
        );
        $userTypeSettingsKeyName = self::USER_TYPE_SETTINGS_CODE;
        if (is_array($arFields[$userTypeSettingsKeyName])) {
            if (isset($arFields[$userTypeSettingsKeyName]['SHOW_ORDER'])) {
                $values['SHOW_ORDER'] = boolval($arFields[$userTypeSettingsKeyName]['SHOW_ORDER']);
            }
            if (isset($arFields[$userTypeSettingsKeyName]['INITIAL_FIELD_COUNT'])) {
                $values['INITIAL_FIELD_COUNT'] = intval($arFields[$userTypeSettingsKeyName]['INITIAL_FIELD_COUNT']);
            }
        }
        return $values;
    }

    /**
     * CheckFields method. Validates passed values by provided method logic.
     *
     * @param array $arProperty IBlock property metadata
     * @param array $value IBlock property value - array with VALUE and DESCRIPTION keys
     * @return array
     */
    public function CheckFields($arProperty, $value)
    {
        $arResult = [];
        // @TODO: implement some validation logic, if needed.
        return $arResult;
    }

    /**
     * GetLength method. Returns exact field length (int type) without spaces, tabs and carriage returns.
     *
     * @param array $arProperty IBlock property metadata
     * @param array $value IBlock property value - array with VALUE and DESCRIPTION keys
     * @return int
     */
    public function GetLength($arProperty, $value)
    {
        return strlen(trim(json_encode($value["VALUE"]), "\n\r\t "));
    }

    /**
     * ConvertToDB method. Converts passed value to db-specific value.
     *
     * @param array $arProperty IBlock property metadata
     * @param array $value IBlock property value - array with VALUE and DESCRIPTION keys
     * @return array
     */
    public function ConvertToDB($arProperty, $value)
    {
        /**
         * @INFO: It's a trap!
         *
         * serialize - slowpoke shit, use json_encode.
         * @see: https://repl.it/DAQP
         */
        if (count($value['VALUE']) > 0) {
            $value['VALUE'] = json_encode($value['VALUE']);
        }

        return $value;
    }

    /**
     * ConvertFromDB method. Converts passed value to developer readable value.
     *
     * @param array $arProperty IBlock property metadata
     * @param array $value IBlock property value - array with VALUE and DESCRIPTION keys
     * @return array
     */
    public function ConvertFromDB($arProperty, $value)
    {
        if (strlen($value['VALUE']) > 0) {
            /**
             * Second parameter used for assoc array return.
             */
            $value['VALUE'] = json_decode($value['VALUE'], true);
        }

        if (is_array($value['VALUE'])) {
            $sorted = self::sortByCallable($value['VALUE'], function ($item) {
                return intval($item['ORDER']);
            });

            $value['VALUE'] = $sorted;

            return $value;
        }

        return $value['VALUE'];
    }

    function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = array(
            "HIDE" => array(
                "HINT",
                "WITH_DESCRIPTION",
                "FILTRABLE",
                "DEFAULT_VALUE",
                "MULTIPLE",
                "SEARCHABLE",
                "SMART_FILTER",
                "FILTER_HINT",
                "DISPLAY_TYPE",
                "DISPLAY_EXPANDED",
                "MULTIPLE_CNT"
            ), //will hide the field
            "SET" => array(
                "FILTRABLE" => "N", "SEARCHABLE" => "N"
            ),
            "USER_TYPE_SETTINGS_TITLE" => "Sortable MultiField settings"
        );

        if ($arProperty[self::USER_TYPE_SETTINGS_CODE]["SHOW_ORDER"] == 'on') {
            $checkBoxValue = 'checked';
        } else {
            $checkBoxValue = '';
        }

        return '
        <tr>
            <td>Initial field count:</td>
            <td>
                <input type="text" size="50" 
                       name="' . $strHTMLControlName["NAME"] . '[INITIAL_FIELD_COUNT]"
                       value="' . $arProperty[self::USER_TYPE_SETTINGS_CODE]['INITIAL_FIELD_COUNT'] . '"
                >
            </td>
        </tr>
        <tr>
            <td>Show ordering:</td>
            <td>
                <input type="checkbox" name="' . $strHTMLControlName["NAME"] . '[SHOW_ORDER]" ' . $checkBoxValue . '/>
            </td>
        </tr>
        ';
    }

    public function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if (is_null($value['VALUE']) || count($value['VALUE']) < 1) {
            return 'No fields';
        }

        return count($value['VALUE']) . ' fields';
    }

    public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $orderFieldType = 'hidden';
        $valueFieldType = 'input';

        if ($arProperty['USER_TYPE_SETTINGS']['SHOW_ORDER'] == true) {
            $orderFieldType = 'text';
        }

        if (intval($arProperty['ROW_COUNT']) > 1) {
            $valueFieldType = 'textarea';
        }

        $fieldPrototype = "<tr><td><" . $valueFieldType . " type=\"text\" size='" . $arProperty['COL_COUNT'] . "' rows='" . $arProperty['ROW_COUNT'] . "' cols='" . $arProperty['COL_COUNT'] . "' data-type='mfieldval' ></" . $valueFieldType . "><input type=\"" . $orderFieldType . "\"  data-type='mfieldorder'  size='10'/></td></tr>";

        /**
         * JS injection
         */
        $html = self::getPropertyFieldJs();

        /** HTML form */
        $html .=
            '<table 
                cellpadding="0"
                cellspacing="0"
                border="0"
                width="100%" 
                class="nopadding" 
                id="tbmf' . md5($arProperty['NAME']) . '"
                data-name="' . $strHTMLControlName['VALUE'] . '"
                data-prototype="' . htmlentities($fieldPrototype) . '"
            >';

        if (is_array($value['VALUE'])) {
            foreach ($value['VALUE'] as $index => $item) {
                $html .= self::getAdminFieldHTML($index, $item, $arProperty, $strHTMLControlName);
            }
        } else {
            for ($i = 0; $i < $arProperty['USER_TYPE_SETTINGS']['INITIAL_FIELD_COUNT']; $i++) {
                $html .= self::getAdminFieldHTML($i, '', $arProperty, $strHTMLControlName);
            }
        }

        $html .= '</table>';
        $html .= '<input type="button" value="Add new field" onclick="addNewMultifieldRow($(\'#tbmf' . md5($arProperty['NAME']) . '\'))"/>';

        return $html;
    }

    public function GetPublicViewHTML()
    {
        // @TODO: implement GetPublicViewHTML()
    }

    public function GetPublicEditHTML()
    {
        // @TODO: implement GetPublicEditHTML()
    }

    /**
     * JS for dynamic field addition
     *
     * @return string
     */
    private static function getPropertyFieldJs()
    {
        return "
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
        <script type='text/javascript'>
            function addNewMultifieldRow(selector){
                var row = $(selector.data('prototype'));
                row.find('input,textarea').each(function(i, el){
                    if ($(el).data('type') === 'mfieldval') {
                        $(el).attr('name', selector.data('name')+'[' + selector.find('tr').length + ']' + '[VALUE]');
                    }
                    if ($(el).data('type') === 'mfieldorder') {
                        $(el).attr('name', selector.data('name')+'[' + selector.find('tr').length + ']' + '[ORDER]');
                        $(el).attr('value', selector.find('tr').length);
                    }
                });
                
                selector.find('tr:last').after(row);
            }
        </script>
        ";
    }

    private static function getAdminFieldHTML($index, $value, $props, $viewProps)
    {

        $fieldValue = '';
        $fieldValueType = 'input';
        $orderFieldValue = $index;
        $valueIsArray = is_array($value);

        if ($valueIsArray) {
            $fieldValue = $value['VALUE'];
        }

        if ($valueIsArray && $value['ORDER'] != '') {
            $orderFieldValue = $value['ORDER'];
        }

        if (intval($props['ROW_COUNT']) > 1) {
            $fieldValueType = 'textarea';
        }

        $fieldHtml = '<tr><td>';
        $fieldHtml .= '<' . $fieldValueType . ' type="text" size="' . $props['COL_COUNT'] . '" rows="' . $props['ROW_COUNT'] . '" cols="' . $props['COL_COUNT'] . '" name="' . $viewProps['VALUE'] . '[' . $index . '][VALUE]' . '" value="' . $fieldValue . '">' . (($fieldValueType == 'textarea') ? $fieldValue : '') . '</' . $fieldValueType . '>';
        $fieldHtml .= self::getOrderFieldPrototype($viewProps['VALUE'] . '[' . $index . '][ORDER]', $orderFieldValue, $props);
        $fieldHtml .= '</td></tr>';

        return $fieldHtml;
    }

    private function getOrderFieldPrototype($name, $value, $params)
    {
        //self::dump($params);
        $orderFieldPrototype = '<input type="hidden" name="' . $name . '" value="' . $value . '"/>';
        if ($params['USER_TYPE_SETTINGS']['SHOW_ORDER'] == true) {
            $orderFieldPrototype = '<input type="text" name="' . $name . '" value="' . $value . '" size="10"/>';
        }

        return $orderFieldPrototype;
    }

    /**
     * Yay, angry pirates with no copyright!
     * Initially i planned to include Underscore.php (anahkiasen/underscore-php), but overhead is too big for three functions.
     * So here comes copy-paste shit.
     *
     * @see http://anahkiasen.github.io/underscore-php/#Arrays-sort
     *
     * @param array|object $collection Multidimensional array to sort
     * @param null|closure|string $sorter What we use to sort array
     * @param string $direction asc or desc
     * @return array
     */
    public static function sortByCallable($collection, $sorter = null, $direction = 'asc')
    {
        $collection = (array)$collection;
        // Get correct PHP constant for direction
        $direction = (strtolower($direction) === 'desc') ? SORT_DESC : SORT_ASC;
        // Transform all values into their results
        if ($sorter) {
            $results = self::each($collection, function ($value) use ($sorter) {
                return is_callable($sorter) ? $sorter($value) : self::get($value, $sorter);
            });
        } else {
            $results = $collection;
        }
        // Sort by the results and replace by original values
        array_multisort($results, $direction, SORT_REGULAR, $collection);
        return $collection;
    }

    /**
     * Iterate over an array and modify the array's value.
     *
     * @see http://anahkiasen.github.io/underscore-php/#Arrays-each
     * @param array $array
     * @param \Closure $closure
     * @return array
     */
    public static function each($array, \Closure $closure)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $closure($value, $key);
        }
        return $array;
    }


    /**
     * Get a value from an collection using dot-notation.
     *
     * @see http://anahkiasen.github.io/underscore-php/#Arrays-get
     * @param array $collection The collection to get from
     * @param string $key The key to look for
     * @param mixed $default Default value to fallback to
     *
     * @return mixed
     */
    public static function get($collection, $key, $default = null)
    {
        if (is_null($key)) {
            return $collection;
        }
        $collection = (array)$collection;
        if (isset($collection[$key])) {
            return $collection[$key];
        }
        // Crawl through collection, get key according to object or not
        foreach (explode('.', $key) as $segment) {
            $collection = (array)$collection;
            if (!isset($collection[$segment])) {
                return $default instanceof Closure ? $default() : $default;
            }
            $collection = $collection[$segment];
        }
        return $collection;
    }

    /**
     * Dumper
     *
     * @param array $value
     */
    private static function dump($value, $toFile = false)
    {
        if (!$toFile) {
            echo '<pre>';
            var_dump($_SERVER['DOCUMENT_ROOT'], $value);
            echo '</pre>';
            return;
        }

        $handle = fopen($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dump.txt', 'a+');
        fwrite($handle, print_r($value, true));
        fclose($handle);
    }
}