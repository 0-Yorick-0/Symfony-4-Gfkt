<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use App\Repository\PropertyRepository;

/**
 * 
 */
class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */
	public function index(PropertyRepository $repository): Response
	{
		$properties = $repository->findLatest();

		return $this->render('pages/home.html.twig', [
			'properties' => $properties
		]);
	}
}