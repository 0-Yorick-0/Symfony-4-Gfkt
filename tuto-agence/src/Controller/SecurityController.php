<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * 
 */
class SecurityController extends AbstractController
{
	
	/**
	 * @Route("/login", name="login")
	 */
	public function login(AuthenticationUtils $authenticationUtils)
	{
		//récupère le dernier nom d'utilisateur tapé par l'utilisateur
		$lastUsername = $authenticationUtils->getLastUsername();
		$error = $authenticationUtils->getLastAuthenticationError();
		return $this->render('security/login.html.twig', [
			'last_username' => $lastUsername,
			'error' => $error
		]);
	}
}