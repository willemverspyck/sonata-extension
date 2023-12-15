<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsController]
abstract class AbstractController extends CRUDController
{
    /**
     * @throws AccessDeniedException
     */
    public function cloneAction(Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        $this->assertObjectExists($request);

        $className = get_class($object);
        $classNames = array_flip($this->admin->getSubClasses());

        if (array_key_exists($className, $classNames)) {
            $request->query->set('subclass', $classNames[$className]);
        }

        $this->addFlash('sonata_flash_success', 'This entity has been cloned.');

        return parent::createAction($request);
    }
}
