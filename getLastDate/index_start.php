<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="//api.bitrix24.com/api/v1/"></script>
</head>
<body>
Привет, начинаем установку активити getLastDate!
</body>
</html>
<script type="text/javascript">
    BX24.init(function(){
        var params = {
            'CODE': 'getLastDate',
            'HANDLER': 'https://spa-bitrix.ru/webhooks/activities/getLastDate/getLastDate.php',
            'AUTH_USER_ID': 1,
            'USE_SUBSCRIPTION': '',
            'NAME': {
                'ru': 'Последняя дата коммуникации'
            },
            'DESCRIPTION': {
                'ru': 'Последняя дата коммуникации'
            },
            'PROPERTIES': {
                'dates': {
                    'Name': {
                        'ru': 'Дата'
                    },
                    'Description': {
                        'ru': 'Дата'
                    },
                    'Type': 'string',
                    'Required': 'Y',
                    'Multiple': 'Y',
                    'Default': '',
                }

            },
            'RETURN_PROPERTIES': {
                'date': {
                    'Name': {
                        'ru': 'Последняя дата коммуникации',
                        'en': 'last date'
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
            function(result)
            {
                if(result.error())
                    alert("Error: " + result.error());
                else
                {
                    alert("Дейстиве успешно установлено");
                    BX24.installFinish();
                }
            }
        );
    });
</script>