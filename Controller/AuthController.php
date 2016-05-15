<?php

namespace RunetId\ApiClientBundle\Controller;

use Doctrine\ORM\EntityManager;
use RunetId\ApiClientBundle\ApiCacheableClient;
use RunetId\ApiClientBundle\Entity\AbstractUser;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $userClass;

    /**
     * @param EngineInterface    $templating
     * @param ApiCacheableClient $apiClient
     * @param EntityManager      $em
     * @param string             $userClass
     */
    public function __construct(
        EngineInterface $templating,
        ApiCacheableClient $apiClient,
        EntityManager $em,
        $userClass
    ) {
        $this->templating = $templating;
        $this->apiClient = $apiClient;
        $this->em = $em;
        $this->userClass = $userClass;
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

        $user = $this->em
            ->getRepository($this->userClass)
            ->findOneByRunetId($apiUser->RunetId);

        if (!$user) {
            $userClass = $this->userClass;

            /** @var AbstractUser $user */
            $user = new $userClass($apiUser->RunetId);
        }

        dump($user);
        return;

        /* $this->get('runet_id.api_client.container')->getDefault()
             ->event()
             ->register($user->getRunetId());
 
         $this->authUser($user);*/

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
