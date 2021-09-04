<?php

namespace Tests\Unit\Pagination\Decorators;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravelayers\Auth\Decorators\UserDecorator;
use Laravelayers\Auth\Models\User as UserModel;
use Laravelayers\Auth\Services\UserService;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;
use Laravelayers\Pagination\Paginator;
use Tests\TestCase;

class PaginatorDecoratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
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
        $items = $this->getData();

        $this->assertTrue($items instanceof PaginatorDecorator);
        $this->assertTrue($items->getData() instanceof Paginator);
        $this->assertTrue($items->getData() instanceof Paginator);
        $this->assertTrue($items->first() instanceof UserDecorator);
    }

    /**
     * Test of the paginator methods.
     */
    public function testPaginator()
    {
        $items = $this->getData();

        $this->assertTrue($items->count() == 10);
        $this->assertTrue($items->currentPage() == 1);
        $this->assertTrue($items->firstItem() == 1);
        $this->assertTrue(!empty($items->getOptions()['path']));
        $this->assertTrue(is_array($items->getOptions()['query']));
        $this->assertNotEmpty(preg_match('/=5$/', last($items->getUrlRange(1,5))));
        $this->assertTrue($items->lastItem() == 10);
        $this->assertTrue($items->lastPage() == 2);
        $this->assertNotEmpty(preg_match('/=2$/', $items->nextPageUrl()));
        $this->assertTrue((bool) $items->onFirstPage());
        $this->assertTrue($items->perPage() == 10);
        $this->assertNotEmpty(preg_match('/=2$/', $items->url(2)));
        $this->assertTrue($items->total() == 15);

        $this->assertTrue(strpos(e($items->render()), 'page=2') !== false);

        $items = $this->getData(false, 10, 2);

        $this->assertTrue($items->count() == 5);
        $this->assertTrue($items->currentPage() == 2);
        $this->assertTrue($items->firstItem() == 11);
        $this->assertTrue($items->lastItem() == 15);
        $this->assertTrue(!$items->nextPageUrl());
        $this->assertTrue(!$items->onFirstPage());

        $items = $this->getData(true);

        $this->assertNotEmpty(preg_match('/=2$/', $items->nextPageUrl()));

        $this->assertTrue(strpos(e($items->render()), 'page=2') !== false);
    }

    /**
     * Test of the "summary" method.
     */
    public function testSummary()
    {
        $items = $this->getData(false, 9, 2, 20);

        $this->assertTrue(strpos(e($items->summary()), '20') !== false);
        $this->assertTrue(strpos(e($items->summary()), '10') !== false);
        $this->assertTrue(strpos(e($items->summary()), '18') !== false);
    }
    
    /**
     * Get the data.
     *
     * @param bool $simplePaginate
     * @param int|null $perPage
     * @param int|null $page
     * @param int $count
     * @param string $pageName
     * @return CollectionDecorator
     */
    protected function getData($simplePaginate = false, $perPage = 10, $page = null, $count = 15, $pageName = 'page')
    {
        $service = app(UserService::class);

        $items = $simplePaginate
            ? $service->simplePaginate($perPage, $pageName, $page)
            : $service->paginate($perPage, $pageName, $page);

        if ($items->isEmpty()) {
            factory(UserModel::class, $count)->create();
            
            $items = $simplePaginate
                ? $service->simplePaginate($perPage, $pageName, $page)
                : $service->paginate($perPage, $pageName, $page);
        }

        return $items;
    }
}
