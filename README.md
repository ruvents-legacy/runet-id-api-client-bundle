# RUNET-ID API Client Bundle

## Установка:

`$ composer require runet-id/api-client-bundle:^1.0.0@alpha`

## Минимальная конфигурация

```yaml
runet_id_api_client:
    container:
        default_credentials: default
        credentials:
            default:
                key:    demokey
                secret: demosecret
```

## Описание
Центральный для бандла класс - `RunetId\ApiClientBundle\ApiClientContainer`. Он позволяет работать с несколькими ключами одновременно и поддерживает кеширование (по умолчанию включено файловое кеширование в стандартную папку симфони `%kernel.cache_dir%/runet_id_api_client`).

Через конфигурацию в разделе `container:credentials` можно указать несколько профилей. В `default_credentials` указывается имя профиля по умолчанию (обязательный параметр).

Также в контейнере через `RunetId\ApiClientBundle\ApiClientContainer::setCurrentName($name)` можно задать "текущий" профиль (например, при помощи `RequestListener`, если выбор профиля зависит от параметров запроса к приложению). Если текущий профиль не был задан, метод `RunetId\ApiClientBundle\ApiClientContainer::getCurrent()` возвращает профиль по умолчанию.

Рекомендуется всегда использовать метод `RunetId\ApiClientBundle\ApiClientContainer::getCurrent()`, так как это обеспечивает максимальную гибкость.

## Алиасы для быстрого доступа к сервисам (рекомендуется)

```yaml
services:
    api_container:  "@runet_id.api_client.container"

    api:
        class: RunetId\ApiClientBundle\ApiCacheableClient
        factory: [ "@api_container", getCurrent ]

# создаем глобальную переменную в twig
# для быстрого доступа к апи из шаблонов
twig:
    globals:
        api: "@api"
```

## Пример настройки авторизации

1. Подключаем `js`:

```html
<script src="{{ asset('bundles/runetidapiclient/js/runet_id_api_client.js') }}"></script>
<script>
    var runetIdApiClient = new RunetId;

    runetIdApiClient.init({
        apiKey: '{{ api.options.key }}',
        backUrl: '{{ url('auth.token') }}'
    });
</script>
```

2. Код кнопки для авторизации

```html
<button onclick="runetIdApiClient.login(); return false;">
    Войти через &ndash;RUNET&mdash;&mdash;ID&ndash;
</button>
```

3. Пример контроллера

```php
<?php

namespace AppBundle\Controller;

use RunetId\ApiClient\Exception\ApiException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/auth")
 */
class AuthController extends Controller
{
    /**
     * @Route("/token", name="auth.token")
     * @param Request $request
     * @return Response
     * @throws HttpException
     */
    public function tokenAction(Request $request)
    {
        $token = $request->query->get('token');

        try {
            $apiUser = $this->get('api')->user()->auth($token);
        } catch (ApiException $e) {
            throw new HttpException(403, $e->getMessage());
        }

        $apiUser; // содержит все данные о пользователе, полученные с RunetId

        // здесь авторизуем пользователя средствами Symfony

        return new Response('
            <script>
                window.onunload = function () {
                    window.opener.location.reload();
                };
                setTimeout(window.close, 400);
            </script>
        ');
    }
}

```
