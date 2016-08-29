<?php
/**
 * Created by PhpStorm.
 * User: warezgibzzz
 * Date: 30.08.16
 * Time: 1:17
 */

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
            "CheckFields" => array(__CLASS__, "CheckFields"),
            "GetLength" => array(__CLASS__, "GetLength"),
            "ConvertToDB" => array(__CLASS__, "ConvertToDB"),
            "ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
//            "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
//            "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
//            "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
//            "GetPublicEditHTML" => array(__CLASS__, "GetPublicEditHTML"),
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

    public function GetPropertyFieldHtml()
    {

    }

    public function GetAdminListViewHTML()
    {

    }

    public function GetPublicViewHTML()
    {

    }

    public function GetPublicEditHTML()
    {

    }
}