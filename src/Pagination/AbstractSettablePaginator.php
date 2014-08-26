<?php

namespace Facile\PaginatorBundle\Pagination;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;

Abstract class AbstractSettablePaginator implements PaginatorInterface
{

    /**
     * @var
     */
    protected $entityManager;

    /**
     * @var int
     */
    protected $numberOfElementsPerPage = 15;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder;

    /**
     * @var
     */
    protected $path;


    /**
     * @var
     */
    protected $query;

    /**
     * @var
     */
    protected $routeParams;

    /**
     * @var int
     */
    protected $recordsCount;


    //------------------------- PROPERTY GETTERS -------------------------

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return int
     */
    public function getNumberOfElementsPerPage()
    {
        return $this->numberOfElementsPerPage;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

// ------------------------- PROPERTY SETTERS -------------------------

    /**
     * @param mixed $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param ObjectManager $entityManager
     */
    protected function setEntityManager(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @throws \Exception
     * @return $this
     */
    public function setQueryBuilder($queryBuilder)
    {
        if (!$queryBuilder instanceof QueryBuilder) {
            throw new \Exception('Must be an instance of query builder');
        }

        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    /**
     * @param int $numberOfElementsPerPage
     * @return $this
     */
    public function setNumberOfElementsPerPage($numberOfElementsPerPage)
    {
        $this->numberOfElementsPerPage = $numberOfElementsPerPage;
        return $this;
    }

    /**
     * @param int $currentPage
     * @throws \Exception
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        if ($currentPage < 0) {
            throw new \Exception('Current page must be greater than zero');
        }

        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @param mixed $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param array $routeParams
     * @return $this
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;
        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param int $recordsCount
     * @return $this
     */
    protected  function setRecordsCount($recordsCount)
    {
        $this->recordsCount = $recordsCount;
        return $this;
    }

    /**
     * @return int
     */
    protected function getRecordsCount()
    {
        return $this->recordsCount;
    }

}