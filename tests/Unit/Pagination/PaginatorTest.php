<?php

namespace Facile\PaginatorBundle\Tests\Unit\Pagination;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Facile\PaginatorBundle\Pagination\Paginator as FacilePaginator;
use Prophecy\Argument;

/**
 * Class Paginator Test
 *
 * @category Test
 * @package Facile\Cbr\CoreBundle\Tests\Product
 * @author Francesco Panina <francesco.panina@facile.it>
 *
 */
class PaginatorTest extends \PHPUnit_Framework_TestCase
{
    const ELEMENTS_PER_PAGE = 5;

    const CURRENT_PAGE = 5;

    public function testPaginate()
    {
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();
        $router = $this->prophesize('Symfony\Bundle\FrameworkBundle\Routing\Router')->reveal();
        $queryBuilder = $this->getQueryBuilderMock(20, self::ELEMENTS_PER_PAGE);

        $paginator = new FacilePaginator($entityManager, $router);

        $paginator->setNumberOfElementsPerPage(self::ELEMENTS_PER_PAGE);
        $paginator->setCurrentPage(self::CURRENT_PAGE);

        $results = $paginator->paginate($queryBuilder);

        $this->assertNotNull($results);
    }

    public function testGettersAndSetters()
    {
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();
        $router = $this->prophesize('Symfony\Bundle\FrameworkBundle\Routing\Router')->reveal();

        $paginator = new FacilePaginator($entityManager, $router);

        $paginator->setNumberOfElementsPerPage(self::ELEMENTS_PER_PAGE);
        $paginator->setCurrentPage(self::CURRENT_PAGE);
        $paginator->setPath('a_random_path');

        $this->assertEquals(self::ELEMENTS_PER_PAGE, $paginator->getNumberOfElementsPerPage());
        $this->assertEquals(self::CURRENT_PAGE, $paginator->getCurrentPage());
        $this->assertEquals('a_random_path', $paginator->getPath());
    }

    public function testInizializationWithParameterNotFoundException()
    {
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();
        $router = $this->prophesize('Symfony\Bundle\FrameworkBundle\Routing\Router')->reveal();

        $this->setExpectedException('Exception');

        new FacilePaginator($entityManager, $router, array("Totally_random_parameter_name" => 'any value'));
    }

    public function testInizializationWithParameterWrongTypeException()
    {
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();
        $router = $this->prophesize('Symfony\Bundle\FrameworkBundle\Routing\Router')->reveal();

        $this->setExpectedException('Exception');

        new FacilePaginator($entityManager, $router, array("numberOfElementsPerPage" => 'this is not an integer'));
    }

    /**
     * Test with no Exceptions
     */
    public function testInizializationWithQueryBuilder()
    {
        $queryBuilder = $this->prophesize('Doctrine\ORM\QueryBuilder')->reveal();

        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();
        $router = $this->prophesize('Symfony\Bundle\FrameworkBundle\Routing\Router')->reveal();

        $paginator = new FacilePaginator($entityManager, $router,  array("queryBuilder" => $queryBuilder));

        $this->assertSame($queryBuilder, $paginator->getQueryBuilder());
    }

    public function testUseResultQuery()
    {
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();
        $router = $this->prophesize('Symfony\Bundle\FrameworkBundle\Routing\Router')->reveal();

        $queryBuilder = $this->getQueryBuilderMock(20, self::ELEMENTS_PER_PAGE, true, 60);

        $paginator = new FacilePaginator($entityManager, $router);

        $paginator->setNumberOfElementsPerPage(self::ELEMENTS_PER_PAGE);
        $paginator->setCurrentPage(self::CURRENT_PAGE);
        $paginator->useResultCache(true, 60);

        $results = $paginator->paginate($queryBuilder);

        $this->assertNotNull($results);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return QueryBuilder
     */
    protected function getQueryBuilderMock($offset, $limit, $useCache = false, $cacheLifetime = 60)
    {
        $prophQueryBuilder = $this->prophesize('Doctrine\ORM\QueryBuilder');

        $queryBuilder = $prophQueryBuilder->reveal();

        $prophQueryBuilder->setMaxResults($limit)->shouldBeCalledTimes(1)->willReturn($queryBuilder);
        $prophQueryBuilder->setFirstResult($offset)->shouldBeCalledTimes(1)->willReturn($queryBuilder);
        $prophQueryBuilder->getQuery()->shouldBeCalledTimes(1)->willReturn($this->getQueryMock($useCache, $cacheLifetime));

        return $queryBuilder;
    }

    /**
     * @return AbstractQuery
     */
    protected function getQueryMock($useCache, $cacheLifetime)
    {
        $query = $this->prophesize('Doctrine\ORM\AbstractQuery');

        if ( ! $useCache) {
            $cacheLifetime = Argument::any();
        }

        $query
            ->useResultCache($useCache, $cacheLifetime)
            ->shouldBeCalledTimes(1)
            ->willReturn($query->reveal());

        $query
            ->getResult()
            ->shouldBeCalledTimes(1)
            ->willReturn(array());

        return $query->reveal();
    }
}
