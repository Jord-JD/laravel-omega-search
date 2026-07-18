<?php

namespace JordJD\LaravelOmegaSearch\Tests;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use JordJD\LaravelOmegaSearch\Traits\OmegaSearchTrait;
use PHPUnit\Framework\TestCase;

class LaravelOmegaSearchTest extends TestCase
{
    private function database()
    {
        $capsule = new Capsule();
        $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $capsule->getContainer()->instance('db', $capsule->getDatabaseManager());
        Facade::setFacadeApplication($capsule->getContainer());

        $capsule->schema()->create('products', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('live');
        });

        Product::insert([
            ['name' => 'Bicycle helmet', 'live' => 1],
            ['name' => 'Red bicycle', 'live' => 1],
            ['name' => 'Hidden bicycle', 'live' => 0],
        ]);
    }

    public function testNoMatchesDoesNotReturnEveryModel()
    {
        $this->database();

        $this->assertCount(0, Product::omegaSearch('zzzzzz')->get());
        $this->assertCount(0, Product::omegaSearch('   ')->get());
    }

    public function testResultsAreReturnedInRelevanceOrderOnSqlite()
    {
        $this->database();

        $this->assertSame(['Red bicycle', 'Bicycle helmet'], Product::omegaSearch('red bicycle')->pluck('name')->all());
    }
}

class Product extends Model
{
    use OmegaSearchTrait;

    protected $table = 'products';
    public $timestamps = false;
    protected $guarded = [];

    public function getOmegaSearchFieldsToSearch()
    {
        return ['name'];
    }

    public function getOmegaSearchConditions()
    {
        return ['live' => 1];
    }
}
