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

$elementType = $_REQUEST["properties"]['elementType'] == 0 ? 3 : 4;
$id = $_REQUEST["properties"]['id'];
switch ($_REQUEST["properties"]['requisiteType']) {
    case 0:
        $requisiteType = 5;
        break;
    case 1:
        $requisiteType = 1;
        break;
    case 2:
        $requisiteType = 3;
        break;
}
$requisiteAddParams = [
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    'fields' => [
        'ENTITY_TYPE_ID' => $elementType,
        'ENTITY_ID' => $id,
        'PRESET_ID' => $requisiteType,
        'NAME' => "Реквизит",
    ]
];
$res = call($_REQUEST["auth"]["client_endpoint"], "crm.requisite.add", $requisiteAddParams);
$result = 'Реквизит создан' . $res;
$res = call($_REQUEST["auth"]["client_endpoint"], 'bizproc.activity.log', array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    "LOG_MESSAGE" => 'Результат: ' . $result
));
$arParams = array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "event_token" => $_REQUEST["event_token"],
    "RETURN_VALUES" => [
        'result' => $result,
    ]
);
call($_REQUEST["auth"]["client_endpoint"], "bizproc.event.send", $arParams);
