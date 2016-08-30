<?php
/**
 * Created by PhpStorm.
 * User: warezgibzzz
 * Date: 30.08.16
 * Time: 1:17
 */
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class CIBlockPropertyWarezgibzzzTzMultiField
 */
class CIBlockPropertyWarezgibzzzTzMultiField
{
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
        /**
         * Fields width, show order setting and initial field count in property settings.
         */
        $showOrder = boolval($arFields['USER_TYPE_SETTINGS']['SHOW_ORDER']);
        $initialFieldCount = intval($arFields['USER_TYPE_SETTINGS']['INITIAL_FIELD_COUNT']);

        if ($initialFieldCount <= 0) {
            $initialFieldCount = 3;
        }

        return array(
            "SHOW_ORDER" => $showOrder,
            "INITIAL_FIELD_COUNT" => $initialFieldCount
        );
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
        if (strlen($value['VALUE']) > 0) {
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

        return $value;
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
                "FILTRABLE" => "N",
                $strHTMLControlName["NAME"] => $arProperty["USER_TYPE_SETTINGS"]
            ),
            "USER_TYPE_SETTINGS_TITLE" => "Sortable MultiField settings"
        );

        if ($arProperty["USER_TYPE_SETTINGS"]["SHOW_ORDERING"] == 'on') {
            $checkBoxValue = 'checked';
        } else {
            $checkBoxValue = '';
        }

        return '
        <tr>
        <td>Initial field count:</td>
        <td><input type="text" size="50" value="' . $arProperty["USER_TYPE_SETTINGS"]["INITIAL_FIELD_COUNT"] . '"  name="' . $strHTMLControlName["NAME"] . '[INITIAL_FIELD_COUNT]"></td>
        </tr>
        <tr>
        <td>Show ordering:</td>
        <td><input type="checkbox" name="' . $strHTMLControlName["NAME"] . '[SHOW_ORDERING]" ' . $checkBoxValue . ' ></td>
        </tr>
        ';
    }

    public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        if ($strHTMLControlName['MODE'] === 'FORM_FILL') {
            echo __METHOD__;
            self::dump([$arProperty, $value, $strHTMLControlName]);
        }
        return '123';
        // @TODO: implement GetPropertyFieldHtml()
    }

    public function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if (is_null($value['VALUE'])) {
            return '&nbsp;';
        }
        if (!is_null($value['VALUE']) && count($value['VALUE']) <= 3) {
            $textValueArray = new ArrayObject($value['VALUE']);
        } else {
            $textValueArray = array(
                $value['VALUE'][0],
                $value['VALUE'][1],
                $value['VALUE'][2],
            );
        }

        // Sort by ordering
        self::sortByCallable(
            $textValueArray,
            function ($item) {
                return $item['ordering'];
            }
        );
        // Get text from item
        self::each(
            $textValueArray,
            function ($item) {
                return $item['text'];
            }
        );
        return implode(', ', $textValueArray);
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
     * @param Closure $closure
     * @return array
     */
    public static function each($array, Closure $closure)
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
    private static function dump($value)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }
}