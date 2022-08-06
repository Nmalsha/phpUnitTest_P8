<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        // $email = $request->get('login')['email'];
        // dd($request->request->get('_csrf_token'));
        $request->getSession()->set(Security::LAST_USERNAME, $email);
        // dd(new UserBadge($email), $email);
        // return new Passport(

        //     new UserBadge($email),

        //     new PasswordCredentials($request->request->get('password', '')),
        //     [
        //         new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
        //     ]
        // );
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        // dd($this->getTargetPath($request->getSession(), $firewallName));
        $getemail = $request->request->get('email');
        $userRole = $this->userRepository->findOneBy(['email' => $getemail])->getRoles()[0];

        if ($userRole = 'ROLE_ADMIN') {
            return new RedirectResponse($this->urlGenerator->generate('user_list'));
        }

        return new RedirectResponse($this->urlGenerator->generate('task_list'));

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
