# RUNET-ID API Client Bundle

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
Центральный для бандла класс - `RunetId\ApiClientBundle\ApiClientContainer`. Он позволяет работать с несколькими ключами одновременно и поддерживает кеширование (по умолчанию файловое в стандартную папку симфони `%kernel.cache_dir%/runet_id_api_client`).

Через конфигурацию в разделе `container:credentials` можно указать несколько профилей. В `default_credentials` указывается имя профиля по умолчанию.

Также в контейнере можно задать "текущий" профиль (например, через `RequestListener`, если выбор профиля зависит от парметров запроса к приложению). По умолчанию метод `RunetId\ApiClientBundle\ApiClientContainer::getCurrent()` возвращает профиль по умолчанию.

Рекомендуется всегда использовать метод `RunetId\ApiClientBundle\ApiClientContainer::getCurrent()`.

## Алиасы для быстрого доступа к сервисам (рекомендуется)

```yaml
services:
    api_container:  "@runet_id.api_client.container"

    api:
        class: RunetId\ApiClientBundle\ApiCacheableClient
        factory: [ "@api_container", getCurrent ]

# вставляем глобальную переменную в твиг
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

2. Создаем ссылку на авторизацию:

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

        // здесь, например, авторизуем пользователя средствами Symfony
        $apiUser; // содержит все данные о пользователе, полученные с RunetId

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
