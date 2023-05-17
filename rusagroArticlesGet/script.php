<?php
function call($domain, $method, $params)
{
    $queryUrl = $domain . $method;
    $queryData = http_build_query($params);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,
    ));
    $results = curl_exec($curl);
    $results = json_decode($results, true);
    curl_close($curl);
    return $results; 
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

//writeToLog($_REQUEST, 'request');
$numStat = $_REQUEST["properties"]["numStat"];
$res = call($_REQUEST["auth"]["client_endpoint"], 'bizproc.activity.log', array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    "LOG_MESSAGE" => 'Запрашиваемая статья: ' . $numStat
));
//поиск статьи
$stat = call(
    $_REQUEST["auth"]["client_endpoint"],
    'lists.element.get',
    array(
        "auth" => $_REQUEST["auth"]["access_token"],
        "EVENT_TOKEN" => $_REQUEST["event_token"],
        'IBLOCK_TYPE_ID' => 'lists',
        'IBLOCK_ID' => 119,
        'FILTER' => [
            'NAME' => $numStat
        ]
    )
);
//writeToLog($stat, 'stat');

//запись резульатат
$statText = array_values($stat['result'][0]['PROPERTY_663']);
$statID = $stat['result'][0]['ID'];
$arParams = array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "event_token" => $_REQUEST["event_token"],
    "RETURN_VALUES" => [
        'statText' => $statText,
        'statID' => $statID
    ]// наши возвращаемые значения
);

call($_REQUEST["auth"]["client_endpoint"], "bizproc.event.send", $arParams);
