<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="//api.bitrix24.com/api/v1/"></script>
</head>
<body>
Привет, начинаем установку приложения...
</body>
</html>
<script type="text/javascript">
    BX24.init(function(){
// описание массива параметров нашего собственного действия
        var params = {
            'CODE': 'getList', // символьный код нашего действия
            'HANDLER': 'https://spa-bitrix.ru/webhooks/activities/pause/pause.php',// скрипт-обработчик действия
            'AUTH_USER_ID': 4, // ID пользователя, токен которого будет передан приложению.
            'USE_SUBSCRIPTION': '',// Использование подписки. Допустимые значения - Y или N.
            //Можно указать, должно ли ожидать действие ответа от приложения.
            //Если параметр пустой или не указан - пользователь может сам настроить этот параметр в настройках действия в дизайнере бизнес-процессов.
            'NAME': {
                'ru': 'Пауза' // название действия в редакторе БП
            },
            'DESCRIPTION': {
                'ru': 'Пауза' // описание действия в редакторе БП
            },
            'PROPERTIES': {// массив входных параметров

                'PauseTime': {
                    'Name': {
                        'ru': 'Время паузы'
                    },
                    'Description': {
                        'ru': 'Время паузы'
                    },
                    'Type': 'string',
                    'Required': 'Y',
                    'Multiple': 'N',
                    'Default': '',
                }

            },
            'RETURN_PROPERTIES': {
                'PauseEnd': {
                    'Name': {
                        'ru': 'Время паузы',
                        'en': 'Pause time'
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