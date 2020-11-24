<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     *
     * @Route("", name="app_security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_idea_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Registration
        $registerForm = $this->createForm(RegisterType::class, $user = new User(), [
            'action' => '/register'
        ]);

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'register_form' => $registerForm->createView()
        ]);
    }

    /**
     * @param Request                      $request
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoginFormAuthenticator       $formAuthenticator
     * @param UserAuthenticatorInterface   $authenticator
     *
     * @return Response
     *
     * @Route("/register", name="app_security_register")
     */
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginFormAuthenticator $formAuthenticator,
        UserAuthenticatorInterface $authenticator): Response
    {
        // Registration
        $registerForm = $this->createForm(RegisterType::class, $user = new User());
        $registerForm->submit($request->request->get($registerForm->getName()));

        if ($registerForm->isValid()) {
            // Hash the password
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $user->getPlainPassword())
            );

            // Persist the user
            $entityManager->persist($user);
            $entityManager->flush();

            // Authenticate & redirect the user
            return $authenticator->authenticateUser($user, $formAuthenticator, $request);
        }

        return $this->redirectToRoute('app_security_login');
    }

    /**
     * @Route("/logout", name="app_security_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}