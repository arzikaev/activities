<?php

function call($domain, $method, $params)
{
    $url = $domain . $method;
    $url .= strpos($url, "?") > 0 ? "&" : "?";
    $url .= http_build_query($params);
    $res = file_get_contents($url);
    return $res;
}

function writeToLog($data, $title = '')
{
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(getcwd() . '/hook.log', $log, FILE_APPEND);
    return true;
}

writeToLog($_REQUEST, 'request');
$stat = $_REQUEST["properties"]["stat"];
$obj = $_REQUEST["properties"]["obj"];
$res = call($_REQUEST["auth"]["client_endpoint"], 'bizproc.activity.log', array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    "LOG_MESSAGE" => 'Запрашиваемая статья: ' . $stat
));
//поиск объекта
$objData = call($_REQUEST["auth"]["client_endpoint"], 'lists.element.get', array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    "IBLOCK_TYPE_ID" => 'bitrix_processes',
    'IBLOCK_ID' => 41,
    'ELEMENT_ID'=>$obj
));
//определение статей объекта
$objItem = json_decode($objData, true);
writeToLog($objItem);

//сравнение статей

//запись резульатат
$result = true;
$arParams = array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "event_token" => $_REQUEST["event_token"],
    "RETURN_VALUES" => [
        'isTrue' => $result
    ]// наши возвращаемые значения
);

call($_REQUEST["auth"]["client_endpoint"], "bizproc.event.send", $arParams);
