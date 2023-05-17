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

$usersID = $_REQUEST["properties"]["users"];
$docIDs = $_REQUEST["properties"]["docID"];
$processID = $_REQUEST["properties"]["processID"];

$res = call(
    $_REQUEST["auth"]["client_endpoint"],
    'bizproc.activity.log',
    array(
        "auth" => $_REQUEST["auth"]["access_token"],
        "EVENT_TOKEN" => $_REQUEST["event_token"],
        "LOG_MESSAGE" => 'id документа: ' . $docID . '; id пользователей: ' . $userID . '; id процесса: ' . $processID
    )
);

if (!empty($usersID) && !empty($docIDs) && !empty($processID)) {
    //формируем массив с правами
    $rightsUsers = [];
    foreach ($usersID as $user) {

        if (substr($user, 0, 4) === 'user') {
            $accessCode = 'U' . substr($user, 5);
            $rightsUsers[] = [
                'TASK_ID' => 79,
                'ACCESS_CODE' => $accessCode
            ];
        } else {
            $accessCode = 'D' . substr($user, 7);
        }
       
    }
    // writeToLog($rightsUsers);
    //получаем файл
    $newFilesID = [];
    foreach ($docIDs as $docID) {
        $file = call(
            $_REQUEST["auth"]["client_endpoint"],
            'disk.file.get',
            array(
                "auth" => $_REQUEST["auth"]["access_token"],
                "EVENT_TOKEN" => $_REQUEST["event_token"],
                'id' => $docID
            )
        );
        writeToLog($file, 'file');
        $content = file_get_contents($file['result']['DOWNLOAD_URL']);
        $base64 = base64_encode($content);
        $params = array(
            "auth" => $_REQUEST["auth"]["access_token"],
            "EVENT_TOKEN" => $_REQUEST["event_token"],
            'id' => $file['result']['PARENT_ID'],
            'fileContent' => [$file['result']['NAME'], $base64],
            'data' => ['NAME' => $file['result']['NAME']],
            'generateUniqueName' => 'Y',
            'rights' => $rightsUsers
        );
        writeToLog($params, 'params');

        $newFile = call(
            $_REQUEST["auth"]["client_endpoint"],
            'disk.folder.uploadfile',
            $params
        );
        writeToLog($newFile, 'newFile');
        call(
        $_REQUEST["auth"]["client_endpoint"],
        'disk.file.delete',
        array(
        "auth" => $_REQUEST["auth"]["access_token"],
        "EVENT_TOKEN" => $_REQUEST["event_token"],
        'id' => $docID
        )
        );
        $newFilesID[] = 'n'.$newFile['result']['ID'];
    }

    //получаем все поля процесса
    $proccess = call(
        $_REQUEST["auth"]["client_endpoint"],
        'lists.element.get',
        array(
            "auth" => $_REQUEST["auth"]["access_token"],
            "EVENT_TOKEN" => $_REQUEST["event_token"],
            'IBLOCK_TYPE_ID' => 'bitrix_processes',
            'IBLOCK_ID' => 35,
            'ELEMENT_ID' => $processID
        )
    );
    writeToLog($proccess, 'proccess');
    
    $paramsUpdateProccess = [
        "auth" => $_REQUEST["auth"]["access_token"],
        "EVENT_TOKEN" => $_REQUEST["event_token"],
        'IBLOCK_TYPE_ID' => 'bitrix_processes',
        'IBLOCK_ID' => 35,
        'ELEMENT_ID' => $processID,
        'FIELDS' => [
            'NAME' => $proccess['result'][0]['NAME'],
            'PROPERTY_137' => ['n0'=> $newFilesID],//Документы
            'PROPERTY_1305' => $proccess['result'][0]['PROPERTY_1305'],//порядковый номер,
            'PROPERTY_667' => $proccess['result'][0]['PROPERTY_667'],//Подписант,
            'PROPERTY_1271' => $proccess['result'][0]['PROPERTY_1271'],//Тип документа,
            'PROPERTY_215' => $proccess['result'][0]['PROPERTY_215'],//Дата входящего документа,
            'PROPERTY_213' => $proccess['result'][0]['PROPERTY_213'],//Номер входящего документа,
            'PROPERTY_1167' => $proccess['result'][0]['PROPERTY_1167'],//Дополнительное согласование эксплуатация/строительство,
            'PROPERTY_205' => $proccess['result'][0]['PROPERTY_205'],//Дополнительный список согласующих,
            'PROPERTY_207' => $proccess['result'][0]['PROPERTY_207'],//Согласование руководителем,
            'PROPERTY_141' => $proccess['result'][0]['PROPERTY_141'],//Контрагент,
            'PROPERTY_143' => $proccess['result'][0]['PROPERTY_143'],//Номер документа (Дог., ДС, Счет, Акты),
            'PROPERTY_1269' => $proccess['result'][0]['PROPERTY_1269'],//Дата документа(Дог., ДС, Счет, Акты),
            'PROPERTY_1255' => $proccess['result'][0]['PROPERTY_1255'],//ID основного документа(id согласованного процесса договора и т.п.),
            'PREVIEW_TEXT' => $proccess['result'][0]['PREVIEW_TEXT'],//Комментарий(разъяснение),
            'PROPERTY_1181' => $proccess['result'][0]['PROPERTY_1181'],//Предмет договора,
            'PROPERTY_133' => $proccess['result'][0]['PROPERTY_133'],//Согласование с зам. директора,
            'PROPERTY_151' => $proccess['result'][0]['PROPERTY_151'],//Дата согласования зам.директора,
            'PROPERTY_149' => $proccess['result'][0]['PROPERTY_149'],//Финальная дата согласования,
            'PROPERTY_1183' => $proccess['result'][0]['PROPERTY_1183'],//Дата заключения,
            'PROPERTY_155' => $proccess['result'][0]['PROPERTY_155'],//Дата согласования Бухгалтерия,
            'PROPERTY_1307' => $proccess['result'][0]['PROPERTY_1307'],//Срок действия договора(мес),
            'PROPERTY_1185' => $proccess['result'][0]['PROPERTY_1185'],//Старое поле. Срок действия договора(мес),
            'PROPERTY_157' => $proccess['result'][0]['PROPERTY_157'],//согласовал Бухгалтерия,
            'PROPERTY_159' => $proccess['result'][0]['PROPERTY_159'],//Дата согласования Юр отдел,
            'PROPERTY_161' => $proccess['result'][0]['PROPERTY_161'],//согласовал Юр отдел,
            'PROPERTY_163' => $proccess['result'][0]['PROPERTY_163'],//Согласовал Ген. директор,
            'PROPERTY_165' => $proccess['result'][0]['PROPERTY_165'],//Лист согласования,
            'PROPERTY_169' => $proccess['result'][0]['PROPERTY_169'],//Дата согласования руководителем,
            'PROPERTY_171' => $proccess['result'][0]['PROPERTY_171'],//Согласовал руководитель,
            'PROPERTY_209' => $proccess['result'][0]['PROPERTY_209'],//Согласовали доп.согласующие,
            'PROPERTY_211' => $proccess['result'][0]['PROPERTY_211'],//Дата согласования доп.согласующими,
            'PROPERTY_127' => $proccess['result'][0]['PROPERTY_127'],//Плательщик/Получатель,
            'PROPERTY_153' => $proccess['result'][0]['PROPERTY_153'],//согласовал зам.директора,
            'PROPERTY_1169' => $proccess['result'][0]['PROPERTY_1169'],//Дата согласования Отдел эксплуатации,
            'PROPERTY_1171' => $proccess['result'][0]['PROPERTY_1171'],//Согласовал отдел эксплуатации,
            'PROPERTY_1173' => $proccess['result'][0]['PROPERTY_1173'],//Дата согласования строительный департамент,
            'PROPERTY_1175' => $proccess['result'][0]['PROPERTY_1175'],//Согласовал строительный департамент,
            'PROPERTY_1177' => $proccess['result'][0]['PROPERTY_1177'],//Скан подписанного документа,
            'PROPERTY_1189' => $proccess['result'][0]['PROPERTY_1189'],//согласовал фин. директор,
            'PROPERTY_1191' => $proccess['result'][0]['PROPERTY_1191'],//Дата согласования фин. директором,
            'PROPERTY_1263' => $proccess['result'][0]['PROPERTY_1263'],//Согласован руководителем РЦ,
            'PROPERTY_1265' => $proccess['result'][0]['PROPERTY_1265'],//Дата согласования руководителем РЦ,
            'PROPERTY_1303' => $proccess['result'][0]['PROPERTY_1303'],//Скан подписанного документа(для xls),
        ]
    ];

   $updareResult = call(
        $_REQUEST["auth"]["client_endpoint"],
        'lists.element.update',
        $paramsUpdateProccess
    );
    writeToLog($updareResult, 'updareResult');

    //запись резульатат
    $result = true;
    $arParams = array(
        "auth" => $_REQUEST["auth"]["access_token"],
        "event_token" => $_REQUEST["event_token"],
        "RETURN_VALUES" => [
            'isTrue' => $result
        ] // наши возвращаемые значения
    );

    call($_REQUEST["auth"]["client_endpoint"], "bizproc.event.send", $arParams);
}