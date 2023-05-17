<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="//api.bitrix24.com/api/v1/"></script>
</head>
<body>
Привет, начинаем установку активити getLastNumDoc!
</body>
</html>
<script type="text/javascript">
    BX24.init(function () {
        var params = {
            'CODE': 'requisiteAdd',
            'HANDLER': 'https://spa-bitrix.ru/webhooks/activities/requisiteAdd/requisiteAdd.php',
            'AUTH_USER_ID': 1,
            'USE_SUBSCRIPTION': '',
            'NAME': {
                'ru': 'Создать Реквизиты'
            },
            'DESCRIPTION': {
                'ru': 'Создать Реквизиты'
            },
            'PROPERTIES': {
                'requisiteType':{
                    'Name': {
                        'ru': 'Тип реквизита'
                    },
                    'Description': {
                        'ru': 'Тип реквизита'
                    },
                    'Type': 'select',
                    'Options':[
                        'Физ. лицо',
                        'Юр. лицо',
                        'ИП'
                    ],
                    'Required': 'Y',
                    'Multiple': 'N',
                    'Default': '',
                },
                'elementType':{
                    'Name': {
                        'ru': 'Тип элемента'
                    },
                    'Description': {
                        'ru': 'Тип элемента'
                    },
                    'Type': 'select',
                    'Options':[
                        'Контакт',
                        'Компания'
                    ],
                    'Required': 'Y',
                    'Multiple': 'N',
                    'Default': '',
                },
                'id': {
                    'Name': {
                        'ru': 'ID элемента'
                    },
                    'Description': {
                        'ru': 'ID элемента'
                    },
                    'Type': 'string',
                    'Required': 'Y',
                    'Multiple': 'N',
                    'Default': '',
                }

            },
            'RETURN_PROPERTIES': {
                'result': {
                    'Name': {
                        'ru': 'Результат',
                        'en': 'Result'
                    },
                    'Type': 'string',
                    'Multiple': 'N',
                    'Default': null
                }
            }
        };

        BX24.callMethod(
            'bizproc.activity.add',
            params,
            function (result) {
                if (result.error())
                    alert("Error: " + result.error());
                else {
                    alert("Дейстиве успешно установлено");
                    BX24.installFinish();
                }
            }
        );
    });
</script>