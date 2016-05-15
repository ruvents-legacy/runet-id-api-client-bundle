<?php

namespace RunetId\ApiClientBundle\Controller;

use RunetId\ApiClientBundle\ApiCacheableClient;
use RunetId\ApiClientBundle\AuthService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 */
class AuthController
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var ApiCacheableClient
     */
    protected $apiClient;

    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * @param EngineInterface    $templating
     * @param ApiCacheableClient $apiClient
     * @param AuthService        $authService
     */
    public function __construct(
        EngineInterface $templating,
        ApiCacheableClient $apiClient,
        AuthService $authService
    ) {
        $this->templating = $templating;
        $this->apiClient = $apiClient;
        $this->authService = $authService;
    }

    /**
     *
     */
    public function registerAction()
    {
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function tokenAction(Request $request)
    {
        $token = $request->query->get('token');

        $apiUser = $this->apiClient
            ->noCacheOnce()
            ->user()
            ->getByToken($token);

        $user = $this->authService->findOrCreateUser($apiUser->RunetId);
        $this->authService->authUser($user);

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
