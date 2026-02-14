# Laravel Omega Search

Omega Search allows you to easily add an intelligent search engine to your Laravel powered website or web application. 
It can be configured to search any of your Eloquent models.

Under the hood, this uses the [Omega Search](https://github.com/Jord-JD/omega-search) package.

## Installation

You can install this package with Composer.

```
composer require jord-jd/laravel-omega-search
```

## Usage

To use Laravel Omega Search, first add the `OmegaSearchTrait` 
to the models you wish to search. You must then implement 
the following two abstract methods.

* `getOmegaSearchFieldsToSearch()` - Must return an array of the model's fields to search.
* `getOmegaSearchConditions()` - Must return an associative array of the search conditions. Example: `['active' => 1, 'discontinued' => 0]`

After this setup, a search can be performed by calling
the static `omegaSearch($searchText)` method on the model.
This method performs an intelligent fuzzy search, and returns 
a query builder filtered to the related records in descending 
order of relevance.

The related models can then be retrieved (`->get()`) or 
paginated (`->paginate()`) as required.

## Joining Tables

If you want to search the content of related tables, you can use joins. Simply override the `getOmegaSearchTablesToJoin` on your model and return an array of `OmegaSearchJoins`.

#### Defining Joins

##### Join by related keys

There are two ways to define joins. The first way is to call the `OmegaSearchJoin::joinTableByForeignKey` method. You have to pass the local table name and joined table name to this method.

By default the key on the joined table will be the joined table name singularised with `_id` appended and the key on the local table will `id`. These can be set manually using the 3rd and 4th parameter for this method.

See below for an example:
```php
    public function getOmegaSearchTablesToJoin()
    {
        return [
            OmegaSearchJoin::joinTableByForeignKey($this->getTable(), 'divisions'),
        ];
    }
```

or


```php
    public function getOmegaSearchTablesToJoin()
    {
        return [
            OmegaSearchJoin::joinTableByForeignKey($this->getTable(), 'divisions', 'id', 'division_id),
        ];
    }
```

You can chain joins as seen below:

```php
    public function getOmegaSearchTablesToJoin()
    {
        return [
            OmegaSearchJoin::joinTableByForeignKey($this->getTable(), 'divisions'),
            OmegaSearchJoin::joinTableByForeignKey('divisions', 'companies', 'id', 'company_id')
        ];
    }
```



##### Join Manually

If you want to join tables that are not just linked by related keys you can manually add conditions to a join. To do this create a new `OmegaSearchJoin` object and call the `addCondition` method to add your conditions. You must specify the local table name and the joined table name in the constructor. Optionally you can specify the join type, the default is `JOIN`

When adding a condition the parameter on the left will automatically have the local table name prepended and the joined table name will be prepended to the right condition.

See below for an example:

```php
    public function getOmegaSearchTablesToJoin()
    {
        $join = new OmegaSearchJoin('contacts', 'divisions', 'INNER JOIN');
        $join->addCondition('gross_income', '>', 'annual_income');
        
        return [
            $join,
        ];
    }
```
