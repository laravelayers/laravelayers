<?php

namespace Tests\Unit\Foundation\Decorators;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravelayers\Auth\Decorators\UserDecorator;
use Laravelayers\Auth\Models\User as UserModel;
use Laravelayers\Auth\Services\UserService;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Tests\TestCase;

class CollectionDecoratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app
            ->make(Factory::class)
            ->load(dirname(__DIR__, 4) . '/database/factories');
    }
    
    /**
     * Test of the "make" method.
     */
    public function testMake()
    {
        $this->assertTrue(CollectionDecorator::make([]) instanceof CollectionDecorator);
        $this->assertTrue(CollectionDecorator::make(collect()) instanceof CollectionDecorator);
        $this->assertTrue(CollectionDecorator::make()->getData() instanceof Collection);

        $items = $this->getData();

        $this->assertTrue($items instanceof CollectionDecorator);
        $this->assertTrue($items->first() instanceof UserDecorator);
    }

    /**
     * Test of the collection object.
     */
    public function testCollection()
    {
        $items = $this->getData();

        $this->assertTrue($items instanceof CollectionDecorator);

        $this->assertTrue(!method_exists($items, 'implode'));
        $this->assertTrue(method_exists($items->getData(), 'implode'));
        $this->assertTrue(is_string($items->implode(',')));

        $only = $items->getOnly($items->first()->getKey());
        $this->assertTrue($items->count() > $only->count());
    }

    /**
     * Test of the "getKeys" method.
     */
    public function testGetKeys()
    {
        $keys = $this->getData()->getKeys();

        $original = UserModel::get();

        $this->assertTrue(count($keys) == count($original));

        foreach($keys as $key) {
            $this->assertNotEmpty($original->find($key)->getKey());
        }
    }

    /**
     * Test of the "getByKey" method.
     */
    public function testGetByKey()
    {
        $items = $this->getData();

        $this->assertTrue($items->getByKey(current($items->getKeys()))->isNotEmpty());
        $this->assertTrue($items->getByKey($items->getKeys())->count() == count($items->getKeys()));
        $this->assertTrue(is_array($items->getByKey(0, [])));
    }

    /**
     * Test of the "getExcept" method.
     */
    public function testGetExcept()
    {
        $items = $this->getData();

        $this->assertTrue(!$items->getExcept($key = current($items->getKeys()))->getByKey($key));
    }

    /**
     * Test of the "getOnly" method.
     */
    public function testGetOnly()
    {
        $items = $this->getData();

        $items = $items->getOnly($key = current($items->getKeys()));

        $this->assertTrue($items->getByKey($key)->isNotEmpty());
        $this->assertTrue($items->count() == 1);
    }

    /**
     * Test of the "getSelectedItems" method.
     */
    public function testGetSelectedItems()
    {
        $items = $this->getData();

        $this->assertTrue($items->getSelectedItems()->isEmpty());

        $items->setSelectedItems($items->first()->getKey());

        $this->assertTrue($items->getSelectedItems()->isNotEmpty());
    }

    /**
     * Get the data.
     *
     * @return CollectionDecorator
     */
    protected function getData()
    {
        $service = app(UserService::class);

        $items = $service->get();

        if ($items->isEmpty()) {
            factory(UserModel::class, 3)->create();

            $items = $service->get();
        }

        return $items;
    }
}
