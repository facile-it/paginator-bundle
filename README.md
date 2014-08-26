A **lightweight** symfony2 pagination system

[![Build Status](https://travis-ci.org/facile-it/paginator-bundle.svg?branch=master)](https://travis-ci.org/facile-it/paginator-bundle)
[![Code Climate](https://codeclimate.com/github/facile-it/paginator-bundle/badges/gpa.svg)](https://codeclimate.com/github/facile-it/paginator-bundle)

## Requirements:

- Twig`>=1.5` version is required if you plan to include the twig template.

## Features:

- Inizialization can be made via request or via setters
- Handle route as well as route parameters 

## Installation and configuration:

Quite easy [Composer](http://packagist.org), add:

```json
{
    "require": {
        "facile-it/paginator-bundle": "~2.4"
    }
}
```

Or if you want to clone the repos:

    # Install knp paginator bundle
    git clone git://github.com/facile-it/paginator-bundle.git vendor/facile-it/PaginatorBundle

<a name="configuration"></a>

### Add PaginatorBundle to your application kernel

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Facile\PaginatorBundle\FacilePaginatorBundle(),
        // ...
    );
}
```

## Usage examples:

### Controller

Currently paginator can paginate:

- `Doctrine\ORM\QueryBuilder`

```php
// Acme\MainBundle\Controller\ProductController.php


    $queryBuilder = $this
        ->get('doctrine.orm.entity_manager')
        ->getRepository('AcmeMainBundle:Product')
        ->createQueryBuilder('product')

    $paginator = $this->get('facile.paginator')->parseRequest($this->getRequest());

    return $this->render('AcmeMainBundle:Product:list.html.twig', array(
        'results' => $pagination,
        'paginationInfo' => $paginator->getPaginationInfo($filterBuilder
        )
    );

```

### View

```jinja

{# display results #}
<table>
    {% for product in results %}
        <tr >
            <td>{{ product.id }}</td>
            <td>{{ product.title }}</td>
        </tr>
    {% endfor %}
</table>

{# display navigation #}
<div class="navigation">
    {% include 'FacilePaginatorBundle:Pagination:template.html.twig' %}
</div>

```
