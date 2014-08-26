<?php

namespace Facile\PaginatorBundle\Tests\Unit\Pagination;

use Doctrine\ORM\Query;
use Facile\PaginatorBundle\Pagination\Paginator as FacilePaginator;


use Mockery as m;

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

    protected $NumberOfElemetsPerPage = 5;

    protected $currentPage = 5;

    public function testPaginate()
    {

        $entityManager = $this->getEntityManagerForPaginatorTest($this->NumberOfElemetsPerPage, $this->currentPage);
        $queryBuilder = $this->getQueryBuilderMock($this->NumberOfElemetsPerPage, $this->currentPage);

        $paginator = new FacilePaginator($entityManager);

        $paginator->setNumberOfElementsPerPage($this->NumberOfElemetsPerPage);

        $results = $paginator->paginate($queryBuilder);

        $this->assertNotNull($results);

    }

    public function testGettersAndSetters()
    {
        $entityManager = $this->getEntityManagerForPaginatorTest($this->NumberOfElemetsPerPage, $this->currentPage);

        $paginator = new FacilePaginator($entityManager);

        $paginator->setNumberOfElementsPerPage($this->NumberOfElemetsPerPage);
        $paginator->setCurrentPage($this->currentPage);
        $paginator->setPath('a_random_path');

        $this->assertEquals($this->NumberOfElemetsPerPage,$paginator->getNumberOfElementsPerPage() );
        $this->assertEquals($this->currentPage,$paginator->getCurrentPage());
        $this->assertEquals('a_random_path',$paginator->getPath());

    }

    public function testInizializationWithParameterNotFoundException()
    {

        $entityManager = $this->getEntityManagerForPaginatorTest($this->NumberOfElemetsPerPage, $this->currentPage);

        $this->setExpectedException('Exception');

        new FacilePaginator($entityManager, array("Totally_random_parameter_name" => 'any value'));


    }

    public function testInizializationWithParameterWrongTypeException()
    {

        $entityManager = $this->getEntityManagerForPaginatorTest($this->NumberOfElemetsPerPage, $this->currentPage);

        $this->setExpectedException('Exception');

        new FacilePaginator($entityManager, array("numberOfElementsPerPage" => 'this is not an integer'));


    }

    /**
     * Test with no Exceptions
     */
    public function testInizializationWithQueryBuilder()
    {

        $queryBuilder = m::mock('\Doctrine\ORM\QueryBuilder[]');

        $entityManager = $this->getEntityManagerForPaginatorTest($this->NumberOfElemetsPerPage, $this->currentPage);

        new FacilePaginator($entityManager, array("queryBuilder" => $queryBuilder));

    }

    protected function getEntityManagerForPaginatorTest($offset, $limit)
    {
        return m::mock('Doctrine\ORM\EntityManager[persist, remove, flush]')
            ->shouldReceive('persist')->andReturn(null)->getMock()
            ->shouldReceive('remove')->andReturn(null)->getMock()
            ->shouldReceive('flush')->andReturn(null)->getMock();
    }

    protected function getQueryBuilderMock($offset, $limit)
    {
        $queryBuilder = m::mock('\Doctrine\ORM\QueryBuilder[setMaxResults, setFirstResult, getQuery]')->shouldDeferMissing();

        $queryBuilder->shouldReceive('setMaxResults')->times(1)->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('setFirstResult')->times(1)->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('getQuery')->times(1)->withNoArgs()->andReturn($this->getQueryMock());

        return $queryBuilder;

    }

    protected function getQueryMock()
    {
        return m::mock('FAKE_MOCK_OF_\Doctrine\ORM\Query')
            ->shouldReceive('getResult')->andReturn(array())
            ->getMock();
    }


}
