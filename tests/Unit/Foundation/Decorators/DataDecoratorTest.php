<?php

namespace Tests\Unit\Foundation\Decorators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravelayers\Auth\Decorators\UserActionDecorator;
use Laravelayers\Auth\Decorators\UserDecorator;
use Laravelayers\Auth\Models\User;
use Laravelayers\Auth\Models\UserAction;
use Laravelayers\Auth\Services\UserService;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Tests\TestCase;

class DataDecoratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test of the "make" method.
     */
    public function testMake()
    {
        $this->assertTrue(DataDecorator::make([]) instanceof DataDecorator);
        $this->assertTrue(DataDecorator::make(collect()) instanceof CollectionDecorator);

        $data = $this->getData();

        $this->assertTrue($data instanceof UserDecorator);

        $this->assertTrue(DataDecorator::make($this->getData()) instanceof UserDecorator);


        $data = UserActionDecorator::make($this->getData());

        $this->assertTrue($data instanceof UserActionDecorator);
        $this->assertTrue($data->getDataKey() instanceof UserDecorator);
        $this->assertNotEmpty($data->email);
    }

    /**
     * Test of the "getTable" method.
     */
    public function testGetTable()
    {
        $data = $this->getData();

        $this->assertTrue($data->getTable() == User::first()->getTable());
    }

    /**
     * Test of the "getKey" method.
     */
    public function testGetKey()
    {
        $data = $this->getData();

        $this->assertTrue($data->getKey() == User::first()->getKey());
    }

    /**
     * Test of the "getKeyName" method.
     */
    public function testGetKeyName()
    {
        $data = $this->getData();

        $this->assertTrue($data->getKeyName() == User::first()->getKeyName());

        $keyName = $data->getKeyName();

        $this->assertTrue(!$data->setKeyName(null)->getKeyName());

        $this->assertTrue($data->setKeyName($keyName)->getKeyName() == $keyName);
    }

    /**
     * Test of the "getOnlyOriginal" method.
     */
    public function testGetOnlyOriginal()
    {
        $data = $this->getData();

        $data->put('test', 1);

        $this->assertTrue(count($data->getOnlyOriginal()) + 1 == $data->count());
        $this->assertTrue(count($data->getOnlyOriginal()) == count(User::first()->getOriginal()));
    }

    /**
     * Test of the "getOriginalKeys" method.
     */
    public function testGetOriginalKeys()
    {
        $keys = $this->getData()->getOriginalKeys();

        $original = User::first()->getOriginal();

        $this->assertTrue(count($keys) == count($original));

        foreach($keys as $key) {
            $this->assertTrue(isset($original[$key]));
        }
    }

    /**
     * Test of the "syncOriginalKeys" method.
     */
    public function testSyncOriginalKeys()
    {
        $data = $this->getData();

        $data->put('test', 1);

        $this->assertTrue(empty($data->getOnlyOriginal()['test']));

        $data->syncOriginalKeys();

        $this->assertTrue(!empty($data->getOnlyOriginal()['test']));
    }


    /**
     * Test of the "getDateKeys" method.
     */
    public function testGetDateKeys()
    {
        $keys = $this->getData()->getDateKeys();

        $original = array_flip(User::first()->getDates());

        $this->assertTrue(count($keys) == count($original));

        foreach($keys as $key) {
            $this->assertTrue(isset($original[$key]));
        }
    }

    /**
     * Test of the "getTimestampKeys" method.
     */
    public function testGetTimestampKeys()
    {
        $this->assertTrue($this->getData()->getTimestampKeys()['created_at'] == User::CREATED_AT);
        $this->assertTrue($this->getData()->getTimestampKeys()['updated_at'] == User::UPDATED_AT);
    }

    /**
     * Test of the "getRelation" method.
     */
    public function testGetRelation()
    {
        $data = $this->getData();

        $userActions = $data->getRelation('userActions');

        $this->assertTrue($userActions instanceof CollectionDecorator);

        $userActionsModel = User::first()->userActions;

        $this->assertTrue($userActions->first()->action == $userActionsModel->first()->action);

        $this->assertTrue($data->setRelation('test', [])->getRelation('test') instanceof DataDecorator);

        $this->assertTrue($data->getRelation('tests', DataDecorator::make(collect())) instanceof CollectionDecorator);

        $this->assertTrue($data->userActions instanceof CollectionDecorator);

        $this->assertTrue($data->getUserActions()->first() instanceof UserActionDecorator);
        $this->assertTrue($data->userActions->first() instanceof UserActionDecorator);

        $userActions->first()->put('test', 1);

        $this->assertTrue($userActions->first()->test == 1);

        $userActions->first()->put('test', 2);

        $data->setRelation('userActions', $userActions);

        $this->assertTrue($userActions->first()->test == 2);
        $this->assertTrue($data->getRelation('userActions')->first()->test == 2);
    }

    /**
     * Test of the "getRelations" method.
     */
    public function testGetRelations()
    {
        $data = $this->getData();

        $data->setRelations(['userActions' => $data->userActions]);

        $this->assertTrue(count($data->getRelations()) == 1);

        $data->setRelation('test', []);

        $this->assertTrue(is_array($data->getRelations(false)['test']));

        $this->assertTrue(count($data->getRelations()) == 2);

        $this->assertTrue($data->getRelations()['test'] instanceof DataDecorator);

        $this->assertTrue($data->getRelations(false)['test'] instanceof DataDecorator);
    }

    /**
     * Test of the "isSelected" method.
     */
    public function testIsSelected()
    {
        $data = $this->getData();

        $this->assertTrue(!$data->getIsSelected());

        $data->setIsSelected(1);

        $this->assertTrue($data->getIsSelected() == true);
        $this->assertTrue($data->isSelected == true);
    }

    /**
     * Get the data.
     *
     * @return DataDecorator
     */
    protected function getData()
    {
        $service = app(UserService::class);

        User::factory()
            ->has(
                UserAction::factory()->count(2)
                    ->state(function (array $attributes, User $user) {
                        return ['user_id' => $user->id];
                    })
            )
            ->create();

        $user = $service->withActionsAndRoles()->get()->last();

        $this->assertTrue($user->isNotEmpty());

        return $user;
    }
}
