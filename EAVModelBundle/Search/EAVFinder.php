<?php

namespace CleverAge\EAVManager\EAVModelBundle\Search;

use CleverAge\EAVManager\EAVModelBundle\Entity\DataRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Sidus\EAVModelBundle\Model\FamilyInterface;

/**
 * Use this service as a wrapper of the EAVQueryBuilder API to find data based on attributes values
 */
class EAVFinder
{
    /** @var Registry */
    protected $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param FamilyInterface $family
     * @param array           $filterBy
     * @param array           $orderBy
     *
     * @throws \UnexpectedValueException
     * @throws \LogicException
     *
     * @return array
     */
    public function findBy(FamilyInterface $family, array $filterBy, array $orderBy = [])
    {
        $qb = $this->getQb($family, $filterBy, $orderBy);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param FamilyInterface $family
     * @param array           $filterBy
     * @param array           $orderBy
     *
     * @throws \UnexpectedValueException
     * @throws \LogicException
     * @throws NonUniqueResultException
     *
     * @return array
     */
    public function findOneBy(FamilyInterface $family, array $filterBy, array $orderBy = [])
    {
        $qb = $this->getQb($family, $filterBy, $orderBy);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param FamilyInterface $family
     * @param array           $filterBy
     * @param array           $orderBy
     *
     * @throws \UnexpectedValueException
     * @throws \LogicException
     *
     * @return QueryBuilder
     */
    public function getQb(FamilyInterface $family, array $filterBy, array $orderBy = [])
    {
        /** @var DataRepository $dataRepo */
        $dataRepo = $this->doctrine->getRepository($family->getDataClass());
        $eavQb = $dataRepo->createEAVQueryBuilder($family);

        // Add order by
        foreach ($orderBy as $attributeCode => $direction) {
            $eavQb->addOrderBy($eavQb->a($attributeCode), $direction);
        }

        $dqlHandlers = [];
        foreach ($filterBy as $attributeCode => $value) {
            $attributeQb = $eavQb->a($attributeCode);
            if (is_array($value)) {
                $dqlHandlers[] = $attributeQb->in($value);
            } else {
                $dqlHandlers[] = $attributeQb->equals($value);
            }
        }

        return $eavQb->apply($eavQb->getAnd($dqlHandlers));
    }
}