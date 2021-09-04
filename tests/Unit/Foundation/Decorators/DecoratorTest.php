<?php

namespace Tests\Unit\Foundation\Decorators;

use Illuminate\Support\Collection;
use Laravelayers\Auth\Services\UserService;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Decorators\Decorator;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;
use Tests\TestCase;

class DecoratorTest extends TestCase
{
    /**
     * Test of the "make" method.
     */
    public function testMake()
    {
        $this->assertTrue(Decorator::make($this->initData()) instanceof DataDecorator);
        $this->assertTrue(Decorator::make('') instanceof DataDecorator);
        $this->assertTrue(Decorator::make(0)->count() == 1);
        $this->assertTrue(Decorator::make()->count() == 0);
        $this->assertTrue(Decorator::make(collect()) instanceof CollectionDecorator);

        $userService = app(UserService::class);

        $this->assertTrue($userService->first() instanceof DataDecorator);
        $this->assertTrue($userService->get() instanceof CollectionDecorator);
        $this->assertTrue($userService->paginate() instanceof PaginatorDecorator);
    }

    /**
     * Test of the "get" method.
     */
    public function testGet()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue(is_string($data->get('name')));
        $this->assertTrue($data->get('name') == 'Toto');
        $this->assertTrue( $data['name'] == $data->get('name'));

        $this->assertTrue(is_array($data->get()));
        $this->assertTrue(is_array($data->get()['book']));
        $this->assertTrue($data->get()['friends'] instanceof Collection);

        $this->assertTrue($data->get('book') instanceof DataDecorator);
        $this->assertTrue($data->get('friends') instanceof CollectionDecorator);

        $this->assertTrue(!is_array($data->get()['book']));
        $this->assertTrue(!$data->get()['friends'] instanceof Collection);

        $this->assertTrue(is_null($data->get('surname')));
        $this->assertTrue($data->get('surname', 'Gale') == 'Gale');

        $this->assertTrue(is_int($data->id));
        $this->assertTrue($data->id === 1);
    }

    /**
     * Test of the "all" method.
     */
    public function testAll()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue(is_array($data->all()));
        $this->assertTrue($data->all()['book'] instanceof DataDecorator);
        $this->assertTrue($data->all()['friends'] instanceof CollectionDecorator);
    }

    /**
     * Test of the "put" method.
     */
    public function testPut()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue($data->put('surname', 'Gale') instanceof DataDecorator);
        $this->assertTrue($data->get('surname') == 'Gale');

        $data['surname'] = 'Woodman';
        $this->assertTrue($data->surname == 'Woodman');

        $data->test = 1;
        $this->assertTrue(!$data->has('test'));
        $this->assertTrue(property_exists($data, 'test'));
        $this->assertTrue($data->test == 1);

        $data->test = [];
        $this->assertTrue(is_array($data->test));

        unset($data->test);
        $this->assertTrue(!property_exists($data, 'test'));

        $data->test = [];
        $this->assertTrue($data->test instanceof DataDecorator);

        $data->test = [];
        $this->assertTrue(is_array($data->test));

        unset($data->test);

        $data->test = collect();
        $this->assertTrue($data->test instanceof CollectionDecorator);

        $data->test = collect();
        $this->assertTrue($data->test instanceof Collection);
    }

    /**
     * Test of the "has" method.
     */
    public function testHas()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue($data->has('name'));
        $this->assertTrue(empty($data->get('surname')));

        $data['surname'] = null;

        $this->assertTrue(isset($data['surname']));

    }

    /**
     * Test of the "forget" method.
     */
    public function testForget()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue($data->has('name'));
        $data->forget('name');
        $this->assertTrue(!$data->has('name'));
    }

    /**
     * Test of the "count" method.
     */
    public function testCount()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue(count($data) === 4);

        $this->assertTrue($data->forget('name')->count() === 3);

        $this->assertTrue($data->put('test1', 4)->put('test2', 5)->count() === 5);
    }

    /**
     * Test of the "isEmpty" method.
     */
    public function testEmpty()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue($data->isEmpty() === false);
    }

    /**
     * Test of the "isNotEmpty" method.
     */
    public function testNotEmpty()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue($data->isNotEmpty() === true);
    }

    /**
     * Test of the "toArray" method.
     */
    public function testToArray()
    {
        $data = Decorator::make($this->initData());

        $this->assertTrue(is_array($data->toArray()));
        $this->assertTrue(is_array($data->toArray()['book']));
        $this->assertTrue(is_array($data->toArray()['friends']));
    }


    /**
     * Test of the "toJson" method.
     */
    public function testToJson()
    {
        $data = Decorator::make($this->initData());

        json_decode($data->toJson());
        $this->assertTrue(json_last_error() === JSON_ERROR_NONE);

    }

    /**
     * Test of the "getIterator" method.
     */
    public function testGetIterator()
    {
        $data = Decorator::make($this->initData());

        $array = $data->get();

        $this->assertTrue($data->count() == count($array));

        foreach($data as $key => $name) {
            $this->assertTrue(isset($array[$key]));
        }
    }

    /**
     * Test of the "__clone" method.
     */
    public function test__Clone()
    {
        $data = Decorator::make($this->initData());

        $cloned = clone $data;

        $this->assertTrue(spl_object_id($data) != spl_object_id($cloned));
        $this->assertTrue(spl_object_id($data->book) != spl_object_id($cloned->book));
        $this->assertTrue(spl_object_id($data->friends) != spl_object_id($cloned->friends));
    }

    /**
     * Initialize data.
     *
     * @return array
     */
    protected function initData()
    {
        return [
            'id' => 1,
            'name' => 'Toto',
            'book' => [
                'title' => 'The Wonderful Wizard of Oz',
                'author' => 'L. Frank Baum',
                'year' => '1900'
            ],
            'friends' => collect([
                'Dorothy Gale',
                'Scarecrow',
                'Tin Woodman',
                'Cowardly Lion'
            ])
        ];
    }
}
