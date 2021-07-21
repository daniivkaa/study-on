<?php

namespace App\Controller;

use App\Security\BillingAuthenticator;
use App\Service\BillingTransaction;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegisterType;
use App\Security\User;
use App\Service\BillingClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{

    private BillingClient $billingClient;
    private BillingTransaction $billingTransaction;

    public function __construct(BillingClient $billingClient, BillingTransaction $billingTransaction)
    {
        $this->billingClient = $billingClient;
        $this->billingTransaction = $billingTransaction;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('course_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile()
    {
        $user = $this->getUser();

        $balance = $this->billingClient->getBalance($user);
        $transactions = $this->billingTransaction->getTransactions($user->getApiToken());

        return $this->render('security/profile.html.twig', [
            'user' => $user,
            'balance' => $balance,
            'transactions' => $transactions,
        ]);

    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserAuthenticatorInterface $authenticator, BillingAuthenticator $formAuthenticator): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('app_profile');
        }

        $user = new User();
        $errors = [];

        $registrationForm = $this->createForm(RegisterType::class, null);

        $registrationForm->handleRequest($request);


        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $data = $registrationForm->getData();
            $credentials = [
                'email' => $data['email'],
                'password' => $data['password']
            ];

            $response = $this->billingClient->register($credentials, $user);
            if($response instanceof User){
                return $authenticator->authenticateUser(
                    $response,
                    $formAuthenticator,
                    $request);
            }

            $errors = $response;
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $registrationForm->createView(),
            'errors' => $errors,
        ]);
    }
}
