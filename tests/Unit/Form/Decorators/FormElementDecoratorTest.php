<?php

namespace Tests\Unit\Form\Decorators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravelayers\Form\Decorators\FormElementDecorator;
use Tests\TestCase;

class FormElementDecoratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test of the "make" method.
     */
    public function testMake()
    {
        $element = $this->getData();

        $this->assertEmpty((string) trim($element->render()));

        $this->assertTrue($element->getName() == 'name');
    }

    /**
     * Test of the "getType" method.
     */
    public function testGetType()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue($element->getType() == 'text');

        $element->addType('text.group');

        $this->assertTrue($element->get('type') == 'text.group');

        $element->addType('');

        $this->assertTrue($element->getType() == 'hidden');

        $this->assertNotEmpty(preg_match('/<input.*type="\s*hidden\s*"[^>]*>/s', $element->render()));
    }

    /**
     * Test of the "getView" method.
     */
    public function testGetView()
    {
        $element = $this->getData()->getElement();

        $this->assertNotEmpty($element->getView());

        $element->addView('form::layouts.hidden.element');

        $this->assertTrue($element->getView() == 'form::layouts.hidden.element');
    }

    /**
     * Test of the "getName" method.
     */
    public function testGetName()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue($element->getName() == 'name');

        $element->addName('test');

        $this->assertTrue($element->getName() == 'test');

        $this->assertNotEmpty(preg_match('/<input.*name="\s*test\s*"[^>]*>/s', $element->render()));

        $element = $this->getData(false)->getElement();

        $checkbox = $element->getValue()->first();

        $this->assertTrue($element->getName($checkbox) == 'option[1]');

        $this->assertTrue($element->getName() == 'option[]');

        $this->assertTrue($checkbox->getName() == 1);
    }

    /**
     * Test of the "getId" method.
     */
    public function testGetId()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue($element->getId() == 'name');

        $element->addId('test');

        $this->assertTrue($element->getId() == 'test');

        $this->assertNotEmpty(preg_match('/<input.*id="\s*test\s*"[^>]*/s', $element->render()));

        $element = $this->getData(false)->getElement();

        $checkbox = $element->getValue()->first();

        $this->assertTrue($element->getId($checkbox) == 'option_1');

        $this->assertTrue($element->getId() == 'option');

        $this->assertTrue($checkbox->getId() == 1);
    }

    /**
     * Test of the "getValue" method.
     */
    public function testGetValue()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getValue());

        $element->addValue('test');

        $this->assertTrue($element->getValue() == 'test');

        $this->assertNotEmpty(preg_match('/<input.*value="\s*test\s*"[^>]*>/s', $element->render()));
    }

    /**
     * Test of the "getValue" method.
     */
    public function testGetText()
    {
        $element = FormElementDecorator::make('button')
            ->addType('button')
            ->getElement();

        $submit = $element->getValue()->first()->submit;

        $this->assertTrue($submit->getText() == 'Submit');

        $submit->addText('Test');

        $this->assertTrue($submit->getText() == 'Test');

        $this->assertNotEmpty(preg_match(
            '/<button.*name="\s?button\s?"[^>]*>.*Test.*<\/button>/s', $element->render()
        ));
    }

    /**
     * Test of the "getIsSelected" method.
     */
    public function testGetIsSelected()
    {
        $element = $this->getData(false)->getElement();

        $this->assertTrue($element->getValue()->getSelectedItems()->isEmpty());

        $checkbox = $element->getValue()->first();

        $checkbox->setIsSelected(true);

        $this->assertTrue($checkbox->isSelected);
        $this->assertTrue(!$element->getValue()->last()->isSelected);
        $this->assertTrue($element->getValue()->getSelectedItems()->isNotEmpty());

        $this->assertTrue($element->getIsSelected($checkbox));;

        $this->assertNotEmpty(preg_match(
            '/<input.*type="\s?checkbox\s?".*name="\s?option\[1\]\s?".*checked[^>]*>/s', $element->render()
        ));
    }

    /**
     * Test of the "getIsSelected" method.
     */
    public function testGetMultiple()
    {
        $element = $this->getData(false)->getElement();

        $this->assertNotEmpty($element->getMultiple());
        $this->assertTrue($element->getName() == 'option[]');

        $element->addMultiple(false);

        $this->assertEmpty($element->getMultiple());
        $this->assertTrue($element->getName() == 'option');

        $element = $this->getData(false);
        $element->addType('select')->addMultiple(true);

        $this->assertNotEmpty(preg_match('/<select.*name="\s?option\[\]\s?".*multiple[^>]*>/s', $element->render()));

        $element = $this->getData(false);

        $this->assertEmpty($element->addType('radio')->getMultiple());
    }

    /**
     * Test of the "getLabel" method.
     */
    public function testGetLabel()
    {
        $element = $this->getData();

        $element->put('label', 'Label')->getElement();

        $this->assertTrue($element->getLabel() == 'Label');

        $element->addLabel('Test');

        $this->assertTrue($element->getLabel() == 'Test');

        $this->assertNotEmpty(preg_match('/<label[^>]*>.*Test.*<\/label>/s', $element->render()));
    }

    /**
     * Test of the "getLabel" method.
     */
    public function testGetClass()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getClass());

        $element->addClass('test');

        $this->assertTrue($element->getClass() == 'test');

        $this->assertNotEmpty(preg_match(
            '/<input.*name="\s?name\s?".*class="\s*test\s*"[^>]*>/s', $element->render()
        ));
    }

    /**
     * Test of the "getHelp" method.
     */
    public function testGetHelp()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getHelp());

        $element->addHelp('Test');

        $this->assertTrue($element->getHelp() == 'Test');

        $this->assertNotEmpty(preg_match('/<label[^>]*>.*<\/label>/s', $element->render()));

        $this->assertNotEmpty(preg_match(
            '/<input.*name="\s?name\s?".*aria-describedby="\s*name_help\s*"[^>]*>/s', $element->render()
        ));

        $this->assertNotEmpty(preg_match(
            '/<p.*id="\s?name_help\s?"[^>]*>.*Test.*<\/p>/s', $element->render())
        );
    }

    /**
     * Test of the "getIcon" method.
     */
    public function testGetIcon()
    {
        $element = $this->getData();

        $element->put('type', 'text.group')->getElement();

        $this->assertTrue(!$element->getIcon());

        $element->addIcon('icon-plus');

        $this->assertTrue($element->get('icon') == 'icon-plus');

        $pattern = '/<i.*class="\s?icon\s?icon-plus\s?"[^>]*>.*<\/i>/s';

        $this->assertNotEmpty(preg_match($pattern, $element->getIcon()));

        $this->assertNotEmpty(preg_match($pattern, $element->render()));
    }

    /**
     * Test of the "getGroup" method.
     *
     * @see FormDecoratorTest::testRenderWithGroup()
     */
    public function testGetGroup()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getGroup());

        $this->assertTrue($element->addGroup('test')->getGroup() == 'test');
    }

    /**
     * Test of the "getLine" method.
     *
     * @see FormDecoratorTest::testRenderWithLine()
     */
    public function testGetLine()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getLine());

        $this->assertTrue($element->addLine('test')->getLine() == 'test');
    }

    /**
     * Test of the "getHidden" method.
     *
     * @see FormDecoratorTest::testRenderWithHidden()
     */
    public function testGetHidden()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getHidden());

        $this->assertTrue($element->addHidden(true)->getHidden());
    }

    /**
     * Test of the "getAttributes" method.
     */
    public function testGetAttributes()
    {
        $element = $this->getData()->getElement();

        $element->addAttributes(['placeholder' => 'Test', 'required' => '', 'disabled' => null, 'readonly' => false]);

        $this->assertTrue($element->getAttributes('placeholder') == 'Test');

        $this->assertEmpty(preg_match('/disabled/', $element->getAttributes()));

        $pattern = '/placeholder="[^"]*"\s*required="\s?"\s*readonly="\s*false\s*"/';

        $this->assertNotEmpty(preg_match($pattern, $element->getAttributes()));

        $this->assertNotEmpty(preg_match($pattern, $element->render()));

        $pattern = '/required="\s?"/';

        $this->assertEmpty(preg_match($pattern, $element->getAttributesExcept('required')));

        $this->assertEmpty(preg_match($pattern, $element->getAttributesOnly('placeholder')));

        $this->assertNotEmpty(preg_match('/data-test=/', $element->addAttributes(['data-test' => 'true'])));
    }

    /**
     * Test of the "getError" method.
     */
    public function testGetError()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getError());

        $this->assertTrue($element->addError('Test')->getError() == 'Test');
    }

    /**
     * Test of the "getErrors" method.
     */
    public function testGetErrors()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getErrors());

        $errors = app(\Illuminate\Support\MessageBag::class, ['messages' => [['name' => 'Error']]]);

        session()->put('errors', $errors);

        $this->assertTrue($element->getError() == 'Error');
        $this->assertTrue(current($element->getErrors())['name'] == 'Error');

        session()->forget('errors');
    }

    /**
     * Test of the "getTooltip" method.
     */
    public function testGetTooltip()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getTooltip());

        $pattern = '/data-tooltip.*title="\s?Test\s?"/';

        $this->assertNotEmpty(preg_match($pattern, $element->addTooltip('Test')->getTooltip()));

        $this->assertNotEmpty(preg_match($pattern, $element->render()));
    }

    /**
     * Test of the "getError" method.
     *
     * @see FormDecoratorTest::testGetRules()
     */
    public function testGetRules()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue($element->getRules() == 'string|max:255');

        $this->assertTrue(!$element->addRules('')->getRules());
    }

    /**
     * Test of the "getElements" method.
     */
    public function testGetElement()
    {
        $element = $this->getData()->getElement();

        $this->assertNotEmpty((string) trim($element));

        $element = FormElementDecorator::make('name')
            ->addType('text')
            ->addValue([
                'value' => ''
            ]);

        $this->assertNotEmpty((string) trim($element));
    }

    /**
     * Test of the "getElementPrefix" method.
     *
     * @see FormDecoratorTest::testGetElementsPrefix()
     */
    public function testGetElementPrefix()
    {
        $element = $this->getData()->getElement();

        $this->assertTrue(!$element->getElementPrefix());
    }

    /**
     * Test of the "render" method.
     *
     * @see FormDecoratorTest::testGetElementsPrefix()
     */
    public function testRender()
    {
        $element = $this->getData()->getElement();

        $pattern = '/<input.*name="\s*name\s*".*id="\s*name\s*"[^>]*>/s';

        $this->assertNotEmpty(preg_match($pattern, $element->render()));

        $this->assertNotEmpty(preg_match($pattern, (string) $element));
    }

    /**
     * Get the data decorator data.
     *
     * @param bool $text
     * @return FormElementDecorator
     */
    protected function getData($text = true)
    {
        if ($text) {
            return FormElementDecorator::make([
                'name' => 'name',
                'type' => 'text',
                'value' => '',
                'rules' => 'string|max:255'
            ]);
        }

        return FormElementDecorator::make('checkbox')
            ->addType('checkbox')
            ->addName('option')
            ->addValue([
                1 => [
                    'name' => 1,
                    'text' => 'One'
                ],
                2 => 'Two'
            ]);
    }
}
