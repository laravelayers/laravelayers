<?php

namespace Tests\Unit\Navigation\Decorators;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Laravelayers\Admin\Controllers\Auth\UserController;
use Laravelayers\Auth\Decorators\LoginDecorator;
use Laravelayers\Auth\Services\UserService;
use Laravelayers\Form\Decorators\FormDecorator;
use Laravelayers\Form\Decorators\FormElementDecorator;
use Laravelayers\Navigation\Decorators\MenuDecorator;
use Laravelayers\Navigation\Decorators\MenuItemDecorator;
use Tests\TestCase;

class MenuItemDecoratorTest extends TestCase
{
    /**
     * Test of the "make" method.
     */
    public function testMake()
    {
        $data = $this->getData();

        $this->assertTrue($data->first() instanceof MenuItemDecorator);
    }

    /**
     * Test of the "getMenuName" method.
     */
    public function testGetMenuName()
    {
        $this->assertTrue($this->getData()->first()->getMenuName() == 'Item 1');
    }

    /**
     * Test of the "getMenuUrl" method.
     */
    public function testGetMenuUrl()
    {
        $this->assertTrue($this->getData()->first()->getMenuUrl() == '/menu/1');
    }

    /**
     * Test of the "getMenuIcon" method.
     */
    public function testGetMenuIcon()
    {
        $this->assertTrue($this->getData()->first()->getMenuIcon() == 'icon-plus');
    }

    /**
     * Test of the "getMenuSorting" method.
     */
    public function testGetMenuSorting()
    {
        $this->assertTrue($this->getData()->first()->getMenuSorting() == 1);
    }

    /**
     * Test of the "getMenuClass" method.
     */
    public function testGetMenuClass()
    {
        $this->assertTrue($this->getData()->first()->getMenuClass() == 'secondary');
    }

    /**
     * Test of the "getMenuParentId" method.
     */
    public function testGetMenuParentId()
    {
        $this->assertTrue($this->getData()->first()->getMenuParentId() == 0);
    }

    /**
     * Test of the "getMenuParentId" method.
     */
    public function testGetMenuLabel()
    {
        $node = $this->getData()->first();

        $this->assertEmpty($node->getMenuLabel());

        $node->setMenuLabel('test', 'alert');

        $this->assertTrue($node->getMenuLabel()['label'] == 'test');
        $this->assertTrue($node->getMenuLabel()['class'] == 'alert');

        $node = $this->getData()->getPath(2)->first();

        $node->setMenuLabel('test', 'alert');

        $this->assertEmpty($node->getMenuLabel());
    }

    /**
     * Test of the "getNodeId" method.
     */
    public function testGetNodeId()
    {
        $this->assertTrue($this->getData()->first()->getNodeId() == 1);
    }

    /**
     * Test of the "getNodeParentId" method.
     */
    public function testGetNodeParentId()
    {
        $data = $this->getData();

        $this->assertTrue($data->first()->getNodeParentId() == 0);

        $tree = $data->getNode(2);

        $nodes = $tree->getOriginal()->whereIn('id', [1, 3]);

        $tree = $tree->addNodes($nodes, 2);

        $this->assertTrue($data->first()->getNodeParentId() == 0);
        $this->assertTrue($data->first()->getNodeId() == 1);

        $this->assertTrue($tree->first()->getTree()->first()->getNodeParentId() == 2);
        $this->assertTrue($tree->first()->getTree()->first()->getNodeId() == '2_1');
    }

    /**
     * Test of the "getNodeSorting" method.
     */
    public function testGetNodeSorting()
    {
        $data = $this->getData();

        $tree = $data->getTree(2);

        $node = $tree->getOriginal()->whereIn('id', 1);

        $tree = $tree->addNodes($node, 2, 1)->reloadNodes();

        $this->assertTrue($data->first()->getNodeSorting() == 1);
        $this->assertTrue($data->first()->getKey() == 1);

        $this->assertTrue($tree->first()->getNodeSorting() > 1);
        $this->assertTrue($tree->first()->getKey() == 1);
    }

    /**
     * Test of the "getNodeLevel" method.
     */
    public function testGetNodeLevel()
    {
        $tree = $this->getData()->getTree();

        $this->assertTrue($tree->first()->getNodeLevel() == 0);
        $this->assertTrue($tree->first()->getTree()->first()->getNodeLevel() == 1);
    }

    /**
     * Test of the "getIsNodeSelected" method.
     */
    public function testGetIsNodeSelected()
    {
        $node = $this->getData()->getNode(1)->first();

        $this->assertFalse($node->getIsNodeSelected());
        $this->assertTrue($node->setIsNodeSelected(true)->getIsNodeSelected());
    }

    /**
     * Test of the "getTree" method.
     */
    public function testGetTree()
    {
        $node = $this->getData()->getNode(1)->first();

        $subnode = $node->getTree()->first();

        $this->assertTrue($subnode->getKey() == 2);

        $this->assertNotEmpty($subnode->getTree()->first());
    }

    /**
     * Test of the "getSiblings" method.
     */
    public function testGetSiblings()
    {
        $node = $this->getData()->getNode(1)->first();

        $subnode = $node->getSiblings()->first();

        $this->assertTrue($subnode->getKey() == 2);

        $this->assertEmpty($subnode->getTree()->first());
    }

    /**
     * Test of the "getPath" method.
     */
    public function testGetPath()
    {
        $node = $this->getData()->getNode(3)->first();

        $path = $node->getPath();

        $this->assertTrue($path->count() == 3);
        $this->assertTrue($path->first()->getKey() == 1);
        $this->assertTrue($path->last()->getKey() == 3);

        $path = $node->getPath(1);

        $this->assertTrue($path->count() == 2);
        $this->assertTrue($path->first()->getKey() == 2);
    }

    /**
     * Test of the "getParent" method.
     */
    public function testGetParent()
    {
        $parent = $this->getData()->getNode(2)->first()->getParent();

        $this->assertTrue($parent->first()->getKey() == 1);
        $this->assertTrue($parent->first()->getTree()->first()->getKey() == 2);
    }

    /**
     * Get the data.
     *
     * @return MenuDecorator
     */
    protected function getData()
    {
        return MenuDecorator::make([
            0 => [
                'id' => 1,
                'name' => 'Item 1',
                'url' => '/menu/1',
                'icon' => 'icon-plus',
                'sorting' => 1,
                'class' => 'secondary',
                'parent_id' => 0,
            ],
            1 => [
                'id' => 2,
                'name' => 'Item 2',
                'url' => '/menu/2',
                'sorting' => 2,
                'parent_id' => 1,
            ],
            2 => [
                'id' => 3,
                'name' => 'Item 3',
                'url' => '/menu/3',
                'sorting' => 3,
                'parent_id' => 2,
            ]
        ])->getMenu();
    }
}
