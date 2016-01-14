<?php

namespace Facile\PaginatorBundle\Pagination;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;

/**
 * Interface PaginatorInterface
 *
 * An interface for a request-inizialized paginator
 */
interface PaginatorInterface
{
    public function paginate(QueryBuilder $queryBuilder);

    public function getPaginationInfo(QueryBuilder $queryBuilder);

    public function parseRequest(Request $queryBuilder);
}
