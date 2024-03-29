<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
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
	public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response
	{

        //$property est automatiquement instancié et hydraté par le framework
        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', [
                'id'   => $property->getId(),
                'slug' => $property->getSlug(),
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('property.show', [
                'id'   => $property->getId(),
                'slug' => $property->getSlug(),
            ]);
        }

        return $this->render('property/show.html.twig', [
            'property'     => $property,
            'current_menu' => 'properties',
            'form'         => $form->createView()
        ]);
	}
}