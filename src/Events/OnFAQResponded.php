<?php
/**
 * Created by PhpStorm.
 * User: gibz
 * Date: 02.09.16
 * Time: 8:56
 */

namespace Warezgibzzz\Events;


use \Bitrix\Main\Mail\Event as MailEvent;

class OnFAQResponded
{
    public function sendMessageToEmailProvided(&$fields)
    {
        AddMessage2Log(print_r($fields, true));
        $item = \CIBlockElement::GetByID($fields['ID'])->Fetch();

        $itemEmail = \CIBlockElement::GetProperty($item['IBLOCK_ID'], $item['ID'], array("sort" => "asc"), array("CODE" => "EMAIL"))->Fetch();
        AddMessage2Log(print_r([$item, $itemEmail], true));
        if (strlen($item['DETAIL_TEXT']) > 0) {
            $res = MailEvent::send([
                'EVENT_NAME' => 'FAQ_REPLIED',

                'C_FIELDS' => [
                    'EMAIL_TO' => $itemEmail['VALUE'],
                ],
                'LID' => is_array(SITE_ID) ? implode(",", SITE_ID) : SITE_ID
            ]);
            if ($res->isSuccess()) {
                AddMessage2Log('Mail sent');
            } else {
                AddMessage2Log(print_r($res->getErrors(), true));
            }
        }


    }
}