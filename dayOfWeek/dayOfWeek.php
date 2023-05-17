<?php

function call($domain, $method, $params){
    $url = $domain.$method;
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

//writeToLog($_REQUEST, 'request');

if($_REQUEST["properties"]["date_now"])
{
    $date_now = $_REQUEST["properties"]["date_now"];
}else {
    $date_now = false;
        }

        $res = call($_REQUEST["auth"]["client_endpoint"], 'bizproc.activity.log', array(
            "auth" => $_REQUEST["auth"]["access_token"],
            "EVENT_TOKEN" => $_REQUEST["event_token"],
            "LOG_MESSAGE"=> 'получение текущей даты: ' . $date_now
        ));

        $days = [
            'Воскресенье', 'Понедельник', 'Вторник', 'Среда',
            'Четверг', 'Пятница', 'Суббота'
        ];
        $resultDay = $days[ date("w", strtotime($date_now))];
//writeToLog(date("w", strtotime($date_now)), 'result');

        //$resultDay =  $days[date("N", $date_now)];
//writeToLog($resultDay, 'resultDay');

        $res = call($_REQUEST["auth"]["client_endpoint"], 'bizproc.activity.log', array(
            "auth" => $_REQUEST["auth"]["access_token"],
            "EVENT_TOKEN" => $_REQUEST["event_token"],
            "LOG_MESSAGE"=> $resultDay
        ));
         $arParams = array(
            "auth" => $_REQUEST["auth"]["access_token"],
            "event_token" => $_REQUEST["event_token"],
            "RETURN_VALUES" => [
                'day' => $resultDay
            ]// наши возвращаемые значения
        );

        call($_REQUEST["auth"]["client_endpoint"], "bizproc.event.send", $arParams);
