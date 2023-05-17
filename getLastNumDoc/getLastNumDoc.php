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

writeToLog($_REQUEST, 'REQUEST');

$id = $_REQUEST["properties"]["id"];
switch ($_REQUEST["properties"]["elementType"]){
    case 0:
        $elementType = 3;
        break;
    case 1:
        $elementType = 4;
        break;
    case 2:
        $elementType = 2;
        break;
    case 3:
        $elementType = 1;
        break;
}

$getDocsParams = [
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    'order' => ['createTime' => 'desc'],
    "filter" => [
        "entityTypeId" => $elementType,
        "entityId" => $id
    ]
];

$docsFromDeal = call($_REQUEST["auth"]["client_endpoint"], "crm.documentgenerator.document.list", $getDocsParams);
$doc = json_decode($docsFromDeal, true)['result']['documents'][0];
if(!empty($doc)){
    $date = date('d.m.Y G:i:s', strtotime($doc['createTime']));
    $number = $doc['number'];
    $fileID = $doc['fileId'];
    $downloadURLdoc = $doc['downloadUrl'];
    $downloadURLpdf = $doc['pdfUrl'];
}else{
    $date = 'Документ не формировался';
    $number = 'Документ не формировался';
    $fileID = 'Документ не формировался';
    $downloadURLdoc = 'Документ не формировался';
    $downloadURLpdf = 'Документ не формировался';
}
$res = call($_REQUEST["auth"]["client_endpoint"], 'bizproc.activity.log', array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "EVENT_TOKEN" => $_REQUEST["event_token"],
    "LOG_MESSAGE" => 'Дата создания документа: ' . $date . ', номер документа: ' . $number
));

$arParams = array(
    "auth" => $_REQUEST["auth"]["access_token"],
    "event_token" => $_REQUEST["event_token"],
    "RETURN_VALUES" => [
        'Date' => $date,
        'Number' => $number,
        'FileID' => $fileID,
        'DownloadURLdoc' => $downloadURLdoc,
        'PdfUrl' => $downloadURLpdf,
    ]
);

call($_REQUEST["auth"]["client_endpoint"], "bizproc.event.send", $arParams);
