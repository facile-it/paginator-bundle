<?php

namespace Facile\PaginatorBundle\Tests\Unit\Pagination;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Facile\PaginatorBundle\Pagination\Paginator as FacilePaginator;

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
        $queryBuilder = $this->getQueryBuilderMock(20, self::ELEMENTS_PER_PAGE);

        $paginator = new FacilePaginator($entityManager);

        $paginator->setNumberOfElementsPerPage(self::ELEMENTS_PER_PAGE);
        $paginator->setCurrentPage(self::CURRENT_PAGE);

        $results = $paginator->paginate($queryBuilder);

        $this->assertNotNull($results);
    }

    public function testGettersAndSetters()
    {
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();

        $paginator = new FacilePaginator($entityManager);

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

        $this->setExpectedException('Exception');

        new FacilePaginator($entityManager, array("Totally_random_parameter_name" => 'any value'));
    }

    public function testInizializationWithParameterWrongTypeException()
    {
        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();

        $this->setExpectedException('Exception');

        new FacilePaginator($entityManager, array("numberOfElementsPerPage" => 'this is not an integer'));
    }

    /**
     * Test with no Exceptions
     */
    public function testInizializationWithQueryBuilder()
    {
        $queryBuilder = $this->prophesize('Doctrine\ORM\QueryBuilder')->reveal();

        $entityManager = $this->prophesize('Doctrine\ORM\EntityManager')->reveal();

        $paginator = new FacilePaginator($entityManager, array("queryBuilder" => $queryBuilder));

        $this->assertSame($queryBuilder, $paginator->getQueryBuilder());
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return QueryBuilder
     */
    protected function getQueryBuilderMock($offset, $limit)
    {
        $prophQueryBuilder = $this->prophesize('Doctrine\ORM\QueryBuilder');

        $queryBuilder = $prophQueryBuilder->reveal();

        $prophQueryBuilder->setMaxResults($limit)->shouldBeCalledTimes(1)->willReturn($queryBuilder);
        $prophQueryBuilder->setFirstResult($offset)->shouldBeCalledTimes(1)->willReturn($queryBuilder);
        $prophQueryBuilder->getQuery()->shouldBeCalledTimes(1)->willReturn($this->getQueryMock());

        return $queryBuilder;
    }

    /**
     * @return AbstractQuery
     */
    protected function getQueryMock()
    {
        $prophQuery = $this->prophesize('Doctrine\ORM\AbstractQuery');
        $prophQuery->getResult()->shouldBeCalledTimes(1)->willReturn(array());

        return $prophQuery->reveal();
    }
}
