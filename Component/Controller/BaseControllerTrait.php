<?php
/*
 * This file is part of the CleverAge/EAVManager package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\EAVManager\Component\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method string generateUrl($route, $parameters = [], $referenceType)
 * @method Registry getDoctrine
 *
 * @property ContainerInterface $container
 *
 * @author Vincent Chalnot <vchalnot@clever-age.com>
 */
trait BaseControllerTrait
{
    /**
     * @param Request $request
     * @param array   $parameters
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getCurrentUri(Request $request, array $parameters = []): string
    {
        $params = $request->attributes->get('_route_params');
        if ($request->get('target')) {
            $params['target'] = $request->get('target');
        }

        return $this->generateUrl($request->attributes->get('_route'), array_merge($params, $parameters));
    }

    /**
     * Alias to return the entity manager.
     *
     * @param string|null $persistentManagerName
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
     *
     * @return EntityManager|ObjectManager
     */
    protected function getManager($persistentManagerName = null): EntityManager
    {
        return $this->getDoctrine()->getManager($persistentManagerName);
    }
}
