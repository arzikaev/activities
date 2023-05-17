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
$isTrue = false;
if ($_REQUEST["properties"]["dates"]) {
    for($_REQUEST["properties"]["dates"] as $dateData) {
        if(!empty($dateData)){
           $isTrue = true;
        }
    }
    if($isTrue){
        $dates = $_REQUEST["properties"]["dates"];
        writeToLog($dates, 'dates');
        $date = date('d.m.Y G:i:s', max(array_map('strtotime', $dates)));
    }else{
        $date = 'Поля с датами еще не заполнены!';
    }
} else {
    $date = 'Не предоставлены даты!';
}

$res = call($_REQUEST["auth"]["client_endpoint"], 'bizproc.activity.log', array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    "LOG_MESSAGE" => 'Возвращаю: ' . $date
));

$arParams = array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "event_token" => $_REQUEST["event_token"],
    "RETURN_VALUES" => [
        'date' => $date
    ]
);

call($_REQUEST["auth"]["client_endpoint"], "bizproc.event.send", $arParams);
