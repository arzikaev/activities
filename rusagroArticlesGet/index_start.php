<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="//api.bitrix24.com/api/v1/"></script>
</head>
<body>
Привет, начинаем установку приложения getQueue - очередь...
</body>
</html>
<script type="text/javascript">
    BX24.init(function(){
// описание массива параметров нашего собственного действия
        var params = {
            'CODE': 'getStatOfObj', // символьный код нашего действия
            'HANDLER': 'https://spa-bitrix.ru/webhooks/activities/rusagro/script.php',// скрипт-обработчик действия
            'AUTH_USER_ID': 193, // ID пользователя, токен которого будет передан приложению.
            'USE_SUBSCRIPTION': '',// Использование подписки. Допустимые значения - Y или N.
            //Можно указать, должно ли ожидать действие ответа от приложения.
            //Если параметр пустой или не указан - пользователь может сам настроить этот параметр в настройках действия в дизайнере бизнес-процессов.
            'NAME': {
                'ru': 'Проверка статьи' // название действия в редакторе БП
            },
            'DESCRIPTION': {
                'ru': 'Проверка статьи' // описание действия в редакторе БП
            },
            'PROPERTIES': {// массив входных параметров

                'numStat': {
                    'Name': {
                        'ru': 'Номер Статьи'
                    },
                    'Description': {
                        'ru': 'Номер Статьи'
                    },
                    'Type': 'string',
                    'Required': 'Y',
                    'Multiple': 'N',
                    'Default': '',
                },
            },
            'RETURN_PROPERTIES': {
                'statText': {
                    'Name': {
                        'ru': 'Текст статьи',
                        'en': 'result'
                    },
                    'Type': 'string',
                    'Multiple': 'N',
                    'Default': null
                },
                'statID': {
                    'Name': {
                        'ru': 'ID статьи',
                        'en': 'ID stat'
                    },
                    'Type': 'string',
                    'Multiple': 'N',
                    'Default': null
                }
            }
        };
        // регистрируем действие БП
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
                    // важно вызвать, чтобы инсталлятор больше не вызывался
                    BX24.installFinish();
                }
            }
        );
    });
</script>