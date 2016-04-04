<?php

namespace RunetId\ApiClientBundle\Controller;

use AppBundle\Entity\User;
use RuventsCmf\CoreBundle\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class SecurityController
 */
class SecurityController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $token = $request->query->get('token');

        $apiUser = $this->get('runet_id.api_client')->user()->getByToken($token);

        $user = $this->getEntityManager()
            ->getRepository('AppBundle:User')->findOneByRunetId($apiUser->RunetId);

        if (!$user) {
            $user = (new User($apiUser->RunetId, null))
                ->setFirstname($apiUser->FirstName)
                ->setLastname($apiUser->LastName);
            $this->persistEntity($user)->flush();
        }

        $token = new UsernamePasswordToken($user, null, 'front', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);

        return new Response('
            <script>
                window.onunload = function () {
                    window.opener.location.reload();
                };
                window.close();
            </script>
        ');
    }
}
