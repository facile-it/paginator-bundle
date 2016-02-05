<?php

namespace Facile\PaginatorBundle\Pagination;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Paginator
 *
 * Paginate results and provide pagination info.
 * @author Francesco Panina <francesco.panina@facile.it>
 */
class Paginator extends AbstractSettablePaginator
{
    /** @var bool */
    private $useCache = false;

    /** @var array */
    private $hints = array();

    /** @var int */
    private $cacheLifetime = 60;

    /**
     * @param EntityManagerInterface $entityManager
     * @param array $options
     */
    public function __construct(EntityManagerInterface $entityManager, $options = array())
    {
        $this->entityManager = $entityManager;

        $this->queryBuilder = new QueryBuilder($entityManager);

        $this->init($options);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return array
     */
    public function paginate(QueryBuilder $queryBuilder)
    {
        $this->setQueryBuilder($queryBuilder);

        $query = $this
            ->getQueryBuilder()
            ->setFirstResult(abs($this->getCurrentPage() - 1) * $this->getNumberOfElementsPerPage())
            ->setMaxResults($this->getNumberOfElementsPerPage())
            ->getQuery();

        $this->applyHints($query);

        return $query
            ->useResultCache($this->useCache, $this->cacheLifetime)
            ->getResult();
    }

    /**
     * @param Request $request
     * @throws \Exception
     * @return $this
     *
     * Parse the request looking for path current page and number of pages.
     */
    public function parseRequest(Request $request)
    {
        try {
            $this->setPath($request->get('_route'));

            $this->setQuery($request->query->all());

            $this->setRouteParams($request->attributes->get('_route_params'));

            if ($request->get('page')) {
                $this->setCurrentPage($request->get('page'));
            }

            if ($request->get('maxItems')) {
                $this->setNumberOfElementsPerPage($request->get('maxItems'));
            }

        } catch (\Exception $e) {
            throw new \Exception ('The request provided is not a valid Request Object');
        }

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @throws \Exception
     * @return array
     */
    public function getPaginationInfo(QueryBuilder $queryBuilder)
    {
        if ( ! ($this->getPath())) {
            throw new \Exception('The path is empty, either set it with a parse request or with a getter method');
        }

        $this->setQueryBuilder($queryBuilder);

        return array(
            'pages' => $this->getPagesCount(),
            'page' => $this->getCurrentPage(),
            'path' => $this->getPath(),
            'query' => $this->getQuery(),
            'routeParams' => $this->getRouteParams(),
            'recordsCount' => $this->getRecordsCount(),
        );
    }

    /**
     * Enables the use of Doctrine's ResultCache on the paginated query
     *
     * @param $useCache
     * @param $lifetime
     */
    public function useResultCache($useCache, $lifetime)
    {
        $this->useCache = $useCache;
        $this->cacheLifetime = $lifetime;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addQueryHint($name, $value)
    {
        $this->hints[$name] = $value;
    }

// ------------------------- INTERNAL  -------------------------

    /**
     * @internal param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return mixed
     *
     * return the number of pages with the current NumberOfElementsPerPage
     */
    protected function getPagesCount()
    {
        $query = $this->getQueryBuilder()->getQuery();
        $this->applyHints($query);
        $query->useResultCache($this->useCache, $this->cacheLifetime);

        $hasGroupBy = 0 < count($this->getQueryBuilder()->getDQLPart('groupBy'));

        $paginator = new DoctrinePaginator($query);
        // see https://github.com/doctrine/doctrine2/issues/4073
        $paginator->setUseOutputWalkers($hasGroupBy);
        $this->setRecordsCount($paginator->count());

        return ceil($this->getRecordsCount() / $this->getNumberOfElementsPerPage());
    }

    /**
     * @param $options
     * @throws \Exception
     *
     * Inizialize the paginator with parameters from the constructor
     */
    protected function init($options)
    {
        foreach ($options as $parameterName => $parameterValue) {
            if ( ! isset ($this->{$parameterName})) {
                throw new \Exception ("The parameter $parameterName is not a valid parameter name");
            }

            if (gettype($this->{$parameterName}) != gettype($parameterValue)) {
                throw new \Exception ("The parameter value type passed for $parameterName is not an accepted type, only " . gettype($this->{$parameterName}));
            }

            $this->{'set' . ucfirst($parameterName)}($parameterValue);
        }
    }

    /**
     * @param $query
     */
    private function applyHints(Query $query)
    {
        foreach ($this->hints as $hintKey => $hintValue) {
            $query->setHint($hintKey, $hintValue);
        }
    }
}
