<?php

namespace App\Listener;

use Doctrine\Common\EventSubscriber;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Property;


/**
 * Va écouter les évenement de modif des entités contenant des images afin de gérer ces images
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/events.html#events
 */
class ImageCacheSubscriber implements EventSubscriber
{
	

	/**
	 * @var CacheManager
	 */
	private $cacheManager;


	/**
	 * @var UploaderHelper
	 */
	private $uploaderHelper;


	function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
	{
		$this->cacheManager = $cacheManager;
		$this->uploaderHelper = $uploaderHelper;
	}

	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return string[]
	 */
	public function getSubscribedEvents()
	{
		return [
			'preRemove',
			'preUpdate'
		];
	}

	public function preRemove(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		//la logique écrite ici va s'appliquer à TOUS les events update sur les entités, il faut donc filtrer
		if (!$entity instanceof Property) {
			return;
		}

		$this->cacheManager->remove(
			$this->uploaderHelper->asset($entity, 'imageFile')
		);
	}

	/**
	 * Appelée avant que les modifications soient effectuées sur l'entité (idéal pour supprimer les images en cache)
	 */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity = $args->getEntity();
		//la logique écrite ici va s'appliquer à TOUS les events update sur les entités, il faut donc filtrer
		if (!$entity instanceof Property) {
			return;
		}
		//suppression des images en cache
		if ($entity->getImageFile() instanceof UploadedFile) {
			$this->cacheManager->remove(
				$this->uploaderHelper->asset($entity, 'imageFile')
			);
		}
	}
}