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
            'CODE': 'getLastNumDoc',
            'HANDLER': 'https://spa-bitrix.ru/webhooks/activities/getLastNumDoc/getLastNumDoc.php',
            'AUTH_USER_ID': 1,
            'USE_SUBSCRIPTION': '',
            'NAME': {
                'ru': 'Получение № документа'
            },
            'DESCRIPTION': {
                'ru': 'Получение № документа'
            },
            'PROPERTIES': {
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
                        'Компания',
                        'Сделка',
                        'Лид'
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
                'Date': {
                    'Name': {
                        'ru': 'Дата создания',
                        'en': 'Date create'
                    },
                    'Type': 'string',
                    'Multiple': 'N',
                    'Default': null
                },
                'Number': {
                    'Name': {
                        'ru': 'Номер документа',
                        'en': 'Document number'
                    },
                    'Type': 'string',
                    'Multiple': 'N',
                    'Default': null
                },
                'FileID': {
                    'Name': {
                        'ru': 'ID файла',
                        'en': 'Document id'
                    },
                    'Type': 'string',
                    'Multiple': 'N',
                    'Default': null
                },
                'DownloadURLdoc': {
                    'Name': {
                        'ru': 'URL Doc',
                        'en': 'Document url doc'
                    },
                    'Type': 'string',
                    'Multiple': 'N',
                    'Default': null
                },
                'PdfUrl': {
                    'Name': {
                        'ru': 'URL PDF',
                        'en': 'Document url pdf'
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