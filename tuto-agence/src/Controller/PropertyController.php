<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;

/**
 * 
 */
class PropertyController extends AbstractController
{
	/**
	 * @var PropertyRepository
	 */
	private $repository;

	/**
	 * @var ObjectManager
	 */
	private $em;

	public function __construct(
		PropertyRepository $repository,
		ObjectManager $em
	)
	{
		$this->repository = $repository;
		$this->em = $em;
	}

	/**
	 * @see https://github.com/KnpLabs/KnpPaginatorBundle
	 * @Route("/biens", name="property.index")
	 * @return Response
	 */
	public function index(
		PaginatorInterface $paginator,
		Request $request
	)
	{
		$search = new PropertySearch();
		$form = $this->createForm(PropertySearchType::class, $search);
		$form->handleRequest($request);

		$properties = $paginator->paginate(
			$this->repository->findAllVisibleQuery($search),
			//définit 1 par défaut si aucune page n'est définie
			$request->query->getInt('page',1),
			12

		);
		return $this->render(
			'property/index.html.twig',
			[
				'current_menu' => 'properties',
				'properties'   => $properties,
				'form'         => $form->createView(),
			]
		);
	}

	/**
	 * @Route(
	 * 	"/biens/{slug}-{id}", 
	 * 	name="property.show", 
	 * 	requirements={"slug": "[a-z0-9\-]*"}
	 * )
	 * @param Property $property
	 * @return Response
	 */
	public function show(Property $property, string $slug): Response
	{
		if ($property->getSlug() !== $slug) {
			return $this->redirectToRoute('property.show', [
				'id'   => $property->getId(),
				'slug' => $property->getSlug(),
			], Response::HTTP_MOVED_PERMANENTLY);			
		}
		return $this->render('property/show.html.twig', [
			'property' => $property,
			'current_menu' => 'properties'
		]);
	}
}