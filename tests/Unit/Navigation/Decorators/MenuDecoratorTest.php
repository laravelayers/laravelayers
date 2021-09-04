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

class MenuDecoratorTest extends TestCase
{
    /**
     * Test of the "make" method.
     */
    public function testMake()
    {
        $data = $this->getData();

        $this->assertTrue($data instanceof MenuDecorator);

        $this->assertTrue(!$data->first() instanceof MenuItemDecorator);

        $this->assertTrue($data->count() == 5);
    }

    /**
     * Test of the "getMenu" method.
     */
    public function testGetMenu()
    {
        $menu = $this->getData()->getMenu();

        $this->assertTrue($menu instanceof MenuDecorator);

        $this->assertTrue($menu->count() == 5);

        $this->assertTrue($menu->first() instanceof MenuItemDecorator);

        $pattern = '/'
            . '<ul[^>]*class="\s?menu[^"]*"[^>]*>'
                . '.*<li[^>]*>.*Item\s1.*<\/li>'
                . '.*<li[^>]*>.*Item\s[2-4]+.*<\/li>'
                . '.*<li[^>]*>.*Item\s5.*<\/li>'
            . '.*<\/ul>'
            .'/s';

        $this->assertNotEmpty(preg_match($pattern, e($menu->render())));
    }

    /**
     * Test of the "getTree" method.
     */
    public function testGetTree()
    {
        $menu = $this->getData()->getMenu();

        $tree = $menu->getMenu()->getTree();

        $this->assertTrue($tree instanceof MenuDecorator);

        $this->assertTrue($tree->count() == 2);

        $this->assertTrue($tree->first() instanceof MenuItemDecorator);

        $this->assertTrue($tree->first()->getKey()  == 1);

        $this->assertTrue($tree->first()->getTree()->isNotEmpty());

        $this->assertTrue($tree->first()->getTree()->first()->getTree()->isNotEmpty());

        $pattern = ''
            . '/<ul[^>]*class="\s?menu[^"]*"[^>]*>'
                . '.*<li[^>]*>.*Item\s1'
                    . '.*<ul[^>]*class="[^"]*nested[^"]*"[^>]*>'
                        . '.*<li[^>]*>.*Item\s2'
                            . '.*<ul[^>]*class="[^"]*nested[^"]*"[^>]*>'
                                . '.*<li[^>]*>.*Item\s3.*<\/li>'
                            . '.*<\/ul>'
                        . '.*<\/li>'
                        . '.*<li[^>]*>.*Item\s4.*<\/li>'
                    . '.*<\/ul>'
                . '.*<\/li>'
            . '.*<\/ul>/s';

        $this->assertNotEmpty(preg_match($pattern, e($tree->render())));

        $tree = $menu->getMenu()->getTree(1);

        $this->assertTrue($tree->count() == 2);

        $this->assertTrue($tree->first()->getKey() == 2);

        $this->assertTrue($tree->first()->getTree()->isNotEmpty());

        $this->assertTrue($tree->first()->getTree()->first()->getTree()->isEmpty());

        $tree = $menu->getMenu()->getTree(1, 0);

        $this->assertTrue($tree->count() == 2);

        $this->assertTrue($tree->first()->getKey()  == 2);

        $this->assertTrue($tree->first()->getTree()->isEmpty());

        $tree = $menu->getMenu()->getTree($menu->getMenu()->getNode(1)->first());

        $this->assertTrue($tree->count() == 2);

        $this->assertTrue($tree->first()->getKey()  == 2);
    }

    /**
     * Test of the "getSiblings" method.
     */
    public function testGetSiblings()
    {
        $siblings = $this->getData()->getMenu()->getSiblings(1);

        $this->assertTrue($siblings instanceof MenuDecorator);

        $this->assertTrue($siblings->count() == 2);

        $this->assertTrue($siblings->first() instanceof MenuItemDecorator);

        $this->assertTrue($siblings->first()->getKey()  == 2);

        $this->assertTrue($siblings->first()->getTree()->isEmpty());

        $pattern = '/'
            . '<ul[^>]*class="\s?menu[^"]*"[^>]*>'
                . '.*<li[^>]*>.*Item\s2.*<\/li>'
                . '.*<li[^>]*>.*Item\s4.*<\/li>'
            . '.*<\/ul>'
            .'/s';

        $this->assertNotEmpty(preg_match($pattern, e($siblings->render())));
    }

    /**
     * Test of the "getPath" method.
     */
    public function testGetPath()
    {
        $menu = $this->getData()->getMenu();

        $path = $menu->getPath(2);

        $this->assertTrue($path instanceof MenuDecorator);

        $this->assertTrue($path->count() == 2);

        $this->assertTrue($path->first() instanceof MenuItemDecorator);

        $this->assertTrue($path->first()->getKey() == 1);

        $this->assertTrue($path->last()->getKey() == 2);

        $this->assertTrue($path->last()->getTree()->isNotEmpty());

        $pattern = '/'
            .'<ul[^>]*class="[^"]*breadcrumbs[^"]*"[^>]*>'
                . '.*<li[^>]*>.*Item\s1.*<\/li>'
                . '.*<li[^>]*>.*Item\s2.*<\/li>'
            . '.*<\/ul>'
            . '/s';

        $this->assertNotEmpty(preg_match($pattern, e($path->render())));

        $path = $menu->getPath($menu->getNode(2)->first());

        $this->assertTrue($path->count() == 2);

        $this->assertTrue($path->first()->getKey() == 1);

    }

    /**
     * Test of the "getParent" method.
     */
    public function testGetParent()
    {
        $parent = $this->getData()->getMenu()->getParent(3);

        $this->assertTrue($parent instanceof MenuDecorator);

        $this->assertTrue($parent->count() == 1);

        $this->assertTrue($parent->first() instanceof MenuItemDecorator);

        $this->assertTrue($parent->first()->getKey() == 2);

        $this->assertTrue($parent->first()->getTree()->first()->getKey() == 3);

        $pattern = '/'
            . '<ul[^>]*class="\s?menu[^"]*"[^>]*>'
                . '.*<li[^>]*>.*Item\s2.*'
                    . '<ul[^>]*class="[^"]*nested[^"]*"[^>]*>'
                        . '.*<li[^>]*>.*Item\s3.*<\/li>'
                    . '.*<\/ul>'
                . '.*<\/li>'
            . '.*<\/ul>'
            . '/s';

        $this->assertNotEmpty(preg_match($pattern, e($parent->render())));
    }

    /**
     * Test of the "getNode" method.
     */
    public function testGetNode()
    {
        $menu = $this->getData()->getMenu();

        $node = $menu->getMenu()->getNode(1);

        $this->assertTrue($node instanceof MenuDecorator);

        $this->assertTrue($node->count() == 1);

        $this->assertTrue($node->first() instanceof MenuItemDecorator);

        $this->assertTrue($node->first()->getKey() == 1);

        $this->assertTrue($node->first()->getTree()->isNotEmpty());

        $pattern = '/'
            . '<ul[^>]*class="\s?menu[^"]*"[^>]*>'
                . '.*<li[^>]*>.*Item\s1.*'
                    . '<ul[^>]*class="[^"]*nested[^"]*"[^>]*>'
                        . '.*<li[^>]*>.*Item\s2.*<\/li>'
                    . '.*<\/ul>'
                . '.*<\/li>'
            . '.*<\/ul>'
            . '/s';

        $this->assertNotEmpty(preg_match($pattern, e($node->render())));

        $node = $menu->getMenu()->getNode(1, 0);

        $this->assertTrue($node->first()->getKey() == 1);

        $this->assertTrue($node->first()->getTree()->isEmpty());

        $node = $menu->getPath($menu->getNode(1)->first());

        $this->assertTrue($node->first()->getKey() == 1);
    }

    /**
     * Test of the "getTitle" method.
     */
    public function testGetTitle()
    {
        $menu = $this->getData()->getMenu();

        $title = $menu->getTitle(3);

        $pattern = 'Item\s3\s\/\sItem\s2\s\/\sItem\s1';

        $this->assertNotEmpty(preg_match('/' . $pattern . '/', $title));

        $title = $menu->getTitle(3, null, 'Test');

        $pattern = '/' . $pattern . '\s\/\sTest/';

        $this->assertNotEmpty(preg_match($pattern, $title));

        $title = $menu->getPath(3)->getTitle('Test');

        $this->assertNotEmpty(preg_match($pattern, $title));

        $title = $menu->getPath(3)->getTitle('Test', ' | ');

        $pattern = str_replace('\/', '|', $pattern);

        $this->assertNotEmpty(preg_match($pattern, $title));
    }

    /**
     * Test of the "getOriginal" method.
     */
    public function testGetOriginal()
    {
        $tree = $this->getData()->getMenu()->getTree();

        $this->assertTrue($tree->count() == 2);

        $this->assertTrue($tree->getOriginal()->count() == 5);
    }

    /**
     * Test of the "addNodes" method.
     */
    public function testAddNodes()
    {
        $menu = $this->getData()->getMenu();

        $tree = $menu->getNode(5);

        $nodes = $tree->getOriginal()->whereIn('id', [2, 4]);

        $tree = $tree->addNodes($nodes, 5);

        $this->assertTrue($tree->first()->getTree()->whereIn('id', [2, 4])->isNotEmpty());

        $tree = $menu->getTree(null, 0);

        $this->assertTrue($tree->count() == 2);

        $tree = $tree->addNodes($nodes, 0, 0);

        $this->assertTrue($tree->count() == 4);

        $this->assertTrue($tree->first()->getKey() == 2);
    }

    /**
     * Test of the "reloadNodes" method.
     */
    public function testReloadNodes()
    {
        $menu = $this->getData()->getMenu();

        $tree = $menu->getTree(1);

        $this->assertTrue($tree->count() == 2);

        $node = $tree->getOriginal()->getByKey(5);

        $tree = $tree->addNode($node, 1, 0)->reloadNodes();

        $this->assertTrue($tree->count() == 3);

        $this->assertTrue($tree->first()->getKey() == 5);
    }

    /**
     * Test of the "getTreeMethod" method.
     */
    public function testGetTreeMethod()
    {
        $menu = $this->getData()->getMenu()->getPath(2);

        $this->assertTrue((bool) $menu->getTreeMethod('getPath'));

        $this->assertTrue($menu->getTreeMethod()[0] == 'getPath');
        $this->assertTrue($menu->getTreeMethod()[1][0] == 2);
    }

    /**
     * Test of the "render" method.
     */
    public function testRender()
    {
        $menu = $this->getData()->getMenu();

        preg_match('/<ul[^>]*>/s', $menu->render(), $matches);

        $this->assertNotEmpty(preg_match('/class="[^"]*menu[^"]*"/', current($matches)));
        $this->assertEmpty(preg_match('/class="[^"]*vertical[^"]*"/', current($matches)));

        preg_match('/<ul[^>]*>/s', $menu->render('vertical'), $matches);

        $this->assertNotEmpty(preg_match('/class="[^"]*menu[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/class="[^"]*vertical[^"]*"/', current($matches)));

        $this->assertNotEmpty(preg_match(
            '/<nav[^>]*>.*<ul.*class="[^"]*breadcrumbs[^"]*"[^>]*>.*<\/ul>/s', $menu->getPath(2)->render()
        ));
    }

    /**
     * Test of the "render" method for the accordion menu.
     */
    public function testRenderAccordion()
    {
        $tree = $this->getData()->getMenu()->getTree();

        preg_match('/<ul[^>]*>/s', $tree->render('accordion'), $matches);

        $this->assertNotEmpty(preg_match('/class="[^-]*menu[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/class="[^"]*accordion-menu[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/data-accordion-menu/', current($matches)));

        preg_match('/<ul[^>]*>/s', $tree->render('accordion.drilldown'), $matches);

        $this->assertNotEmpty(preg_match('/data-responsive-menu="[^"]*drilldown[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/data-responsive-menu="[^"]*accordion[^"]*"/', current($matches)));
    }

    /**
     * Test of the "render" method for the drilldown menu.
     */
    public function testRenderDrilldown()
    {
        $tree = $this->getData()->getMenu()->getTree();

        preg_match('/<ul[^>]*>/s', $tree->render('drilldown'), $matches);

        $this->assertNotEmpty(preg_match('/class="[^"]*drilldown[^"]*"/s', current($matches)));
        $this->assertNotEmpty(preg_match('/data-drilldown/', current($matches)));
    }

    /**
     * Test of the "render" method for the dropdown menu.
     */
    public function testRenderDropdown()
    {
        $tree= $this->getData()->getMenu()->getTree();

        preg_match('/<ul[^>]*>/s', $tree->render('dropdown'), $matches);

        $this->assertNotEmpty(preg_match('/class="[^"]*dropdown[^"]*"/s', current($matches)));
        $this->assertEmpty(preg_match('/class="[^"]*vertical[^"]*"/s', current($matches)));
        $this->assertNotEmpty(preg_match('/data-dropdown-menu/', current($matches)));

        preg_match('/<ul[^>]*>/s', $tree->render('dropdown.accordion'), $matches);

        $this->assertNotEmpty(preg_match('/class="\s?menu[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/data-responsive-menu="[^"]*accordion[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/data-responsive-menu="[^"]*dropdown[^"]*"/', current($matches)));

        preg_match('/<ul[^>]*>/s', $tree->render('dropdown.drilldown'), $matches);

        $this->assertNotEmpty(preg_match('/class="\s?menu[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/data-responsive-menu="[^"]*drilldown[^"]*"/', current($matches)));
        $this->assertNotEmpty(preg_match('/data-responsive-menu="[^"]*dropdown[^"]*"/', current($matches)));

        preg_match('/<ul[^>]*>/s', $tree->render('dropdown.vertical'), $matches);

        $this->assertNotEmpty(preg_match('/class="[^"]*dropdown[^"]*"/s', current($matches)));
        $this->assertNotEmpty(preg_match('/class="[^"]*vertical[^"]*"/s', current($matches)));
        $this->assertNotEmpty(preg_match('/data-dropdown-menu/', current($matches)));
    }

    /**
     * Test of the "render" method for the breadcrumbs.
     */
    public function testRenderBreadcrumbs()
    {
        $path = $this->getData()->getMenu()->getPath(2);

        $this->assertNotEmpty(preg_match(
            '/<ul[^>]*class="[^"]*breadcrumbs[^"]*"/s', $path->render('breadcrumbs')
        ));

        $this->assertNotEmpty(preg_match(
            '/<nav[^>]*>.*<ul.*class="[^"]*breadcrumbs[^"]*"[^>]*>.*<\/ul>/s', $path->render('breadcrumbs.nav')
        ));
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
            ],
            3 => [
                'id' => 4,
                'name' => 'Item 4',
                'url' => '/menu/4',
                'sorting' => 4,
                'parent_id' => 1,
            ],
            4 => [
                'id' => 5,
                'name' => 'Item 5',
                'url' => '/menu/5',
                'sorting' => 5,
                'parent_id' => 0,
            ]
        ]);
    }
}
