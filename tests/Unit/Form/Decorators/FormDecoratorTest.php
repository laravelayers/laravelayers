<?php

namespace Tests\Unit\Form\Decorators;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Laravelayers\Auth\Decorators\LoginDecorator;
use Laravelayers\Auth\Services\UserService;
use Laravelayers\Form\Decorators\FormDecorator;
use Laravelayers\Form\Decorators\FormElementDecorator;
use Tests\TestCase;

class FormDecoratorTest extends TestCase
{
    /**
     * Test of the "make" method.
     */
    public function testMake()
    {
        $service = app(UserService::class)->setDecorators(LoginDecorator::class);

        $decorator = $service->fill();

        $elements = $decorator->getElements();

        $this->assertTrue(!$elements->name->value);
        $this->assertTrue(!$decorator->name);

        $decorator = $service->fill();

        $this->get('/login');

        Request::setMethod('POST');
        Request::merge(['email' => 'admin@test.localhost']);

        $elements = $decorator->getElements();

        $this->assertTrue( $elements->email->value == 'admin@test.localhost');
        $this->assertTrue($decorator->email == 'admin@test.localhost');

        Request::setMethod('GET');
        Request::merge([]);
    }

    /**
     * Test of the "getElements" method.
     */
    public function testGetElements()
    {
        $elements = $this->getData();

        $this->assertTrue($elements->count() == 2);

        $this->assertTrue(!$elements->name instanceof FormElementDecorator);
        $this->assertTrue(!$elements->getForm() instanceof FormElementDecorator);
        $this->assertTrue($elements->getForm()['method'] == 'POST');

        $elements = $elements->getElements();

        $this->assertTrue($elements->name instanceof FormElementDecorator);
        $this->assertTrue($elements->getForm() instanceof FormElementDecorator);

        $this->assertTrue(spl_object_id($elements) == spl_object_id($elements->getElements()));

        $elements = $this->getData(false);

        $this->assertTrue($elements->count() == 2);

        $this->assertTrue(!$elements->getForm());

        $elements = $elements->getElements();

        $this->assertTrue($elements->name instanceof FormElementDecorator);
        $this->assertTrue($elements->getForm() instanceof FormElementDecorator);

        $this->assertTrue(spl_object_id($elements) == spl_object_id($elements->getElements()));
    }

    /**
     * Test of the "getForm" method.
     */
    public function testGetForm()
    {
        $elements = $this->getData()->getElements();

        $this->assertTrue($elements->getForm() instanceof FormElementDecorator);
        $this->assertTrue($elements->getForm()->getType() == 'form');
        $this->assertTrue($elements->getForm('method') == 'POST');
        $this->assertTrue($elements->getForm('methodField') == 'POST');
        $this->assertTrue($elements->getForm('action') == URL::current());

        $this->assertTrue($elements->getForm()->getView() == 'form::layouts.form.element');
    }

    /**
     * Test of the "setForm" method.
     */
    public function testSetForm()
    {
        $elements = FormDecorator::make([]);

        $elements->setForm([
            'type' => 'form',
            'method' => 'POST'
        ]);

        $this->assertNotEmpty($elements->getForm());
        $this->assertTrue(is_array($elements->getForm()));

        $elements->getElements();

        $this->assertTrue($elements->getForm() instanceof FormElementDecorator);
        $this->assertTrue($elements->getForm()->getType() == 'form');

        $elements = FormDecorator::make([
            0 => [
                'type' => 'form',
                'method' => 'POST',
                'action' => '/'
            ]
        ]);

        $this->assertTrue(!$elements->getForm());

        $elements->getElements();

        $this->assertTrue($elements->getForm() instanceof FormElementDecorator);
        $this->assertTrue($elements->getForm('action') == '/');

        $elements->setForm([
            'type' => 'form',
            'method' => 'POST',
            'action' => '/test'
        ]);

        $this->assertTrue(spl_object_id($elements) != spl_object_id($elements->getElements()));

        $this->assertTrue($elements->getForm() instanceof FormElementDecorator);
        $this->assertTrue($elements->getForm('action') == '/test');

        $this->assertTrue(spl_object_id($elements) == spl_object_id($elements->getElements()));
    }

    /**
     * Test of the "getElementsPrefix" method.
     */
    public function testGetElementsPrefix()
    {
        $elements = $this->getData();

        $this->assertTrue(!$elements->getElementsPrefix());
        $this->assertTrue($elements->getElementsPrefixName() == 'element');

        $elements->setElementsPrefixName('test');

        $this->assertTrue($elements->setElementsPrefix(1)->getElementsPrefix() == 'test[1]');

        $elements->setElementsPrefixName('element');

        $this->assertTrue($elements->getElementsPrefix() == 'test[1]');

        $elements = $elements->getElements();

        $this->assertTrue($elements->getElementsPrefix() == 'test[1]');

        $this->assertTrue($elements->first()->getElementPrefix() == 'test[1]');
    }

    /**
     * Test of the "getRules" method.
     */
    public function testGetRules()
    {
        $elements = $this->getData()->getElements();

        $this->assertTrue(count($elements->getRules()) == 0);

        Request::setMethod('POST');
        Request::merge(['name' => 'test']);

        $elements = $this->getData()->getElements();

        $this->assertTrue(count($elements->getRules()) == 2);
        $this->assertTrue($elements->getRules()['email'] == 'required|email');

        $elements->addRules('email', 'email');

        $this->assertTrue($elements->getRules()['email'] == 'email');

        $elements->addRules('email', '');

        $this->assertTrue(!isset($elements->getRules()['email']));

        $elements->addRules(['name' => 'string']);

        $this->assertTrue($elements->getRules()['name'] == 'string');

        $elements->addRules([]);

        $this->assertTrue(!$elements->getRules());
    }

    /**
     * Test of the "getErrors" method.
     */
    public function testGetErrors()
    {
        $elements = $this->getData()->getElements();

        $this->assertTrue(!$elements->getErrors());

        $elements->setErrors(['name' => 'Test']);

        $this->assertTrue($elements->getErrors()['name'] == 'Test');

        $elements->setError('name', '');

        $this->assertTrue(!$elements->getErrors());
    }

    /**
     * Test of the "getWarnings" method.
     */
    public function testGetWarnings()
    {
        $elements = $this->getData()->getElements();

        $elements->setWarning('Test');

        $this->assertTrue($elements->getWarnings()->first() == 'Test');

        $elements->setWarnings([]);

        $this->assertTrue($elements->getWarnings()->isEmpty());

    }

    /**
     * Test of the "getSuccess" method.
     */
    public function testGetSuccess()
    {
        $elements = $this->getData()->getElements();

        $this->assertTrue($elements->getSuccess() != 'Test');

        $elements->setSuccess('Test');

        $this->assertTrue($elements->getSuccess() == 'Test');
    }

    /**
     * Test of the "render" method.
     */
    public function testRender()
    {
        $elements = $this->getData()->getElements();

        $pattern = '/<form.*method="\s?'
            . $elements->getForm('method')
            . '\s?".*action="\s?'
            . preg_quote($elements->getForm('action'), '/')
            . '\s?".*id="\s?'
            . $elements->getForm()->getId()
            . '\s?"[^>]+>.*<\/form>/s';

        $this->assertNotEmpty(preg_match($pattern, $elements->render()));

        $this->assertNotEmpty(preg_match($pattern, (string) $elements));
    }

    /**
     * Test of the "render" method for elements combined into groups.
     */
    public function testRenderWithGroup()
    {
        $elements = $this->getData()->getElements();

        foreach($elements as $element) {
            $element->addGroup('test');
        }

        $this->assertNotEmpty(preg_match(
            '/<fieldset[^>]*>.*<legend[^>]*>.*test.*<\/legend>.*<\/fieldset>/s',
            $elements->render()
        ));
    }

    /**
     * Test of the "render" method for elements combined into line.
     */
    public function testRenderWithLine()
    {
        $elements = $this->getData()->getElements();

        foreach($elements as $element) {
            $element->addLine('test');
        }

        $this->assertNotEmpty(preg_match(
            '/<div.*class="[^"]*grid-x[^"]*"[^>]*>.*name="\s?name\s?".*name="\s?email\s?".*<\/div>/s',
            $elements->render()
        ));
    }

    /**
     * Test of the "render" method for hidden elements.
     */
    public function testRenderWithHidden()
    {
        $elements = $this->getData()->getElements();

        $pattern = '/name="\s*name\s*"/';

        $this->assertNotEmpty(preg_match($pattern, $elements->render()));

        $elements->first()->addHidden(true);

        $this->assertEmpty(preg_match($pattern, $elements->render()));
    }

    /**
     * Test of the "getRequest" method.
     */
    public function testGetRequest()
    {
        $elements = $this->getData()->getElements();

        $this->assertTrue($elements->getRequest() instanceof \Illuminate\Http\Request);

        $elements->setRequest(null);

        $this->assertTrue(!$elements->getRequest());
    }

    /**
     * Test of the "validate" method.
     */
    public function testValidate()
    {
        Request::setMethod('POST');
        Request::merge(['name' => 'test']);

        $elements = $this->getData()->getElements();

        try {
            $elements->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $validator = Validator::make($elements->getRequest()->all(), $elements->getRules());
        }

        $this->assertTrue($validator->errors()->count() > 0);

    }

    /**
     * Get the data.
     *
     * @param bool $array
     * @return FormDecorator
     */
    protected function getData($array = true)
    {
        if ($array) {
            return FormDecorator::make([
                'form' => [
                    'type' => 'form',
                    'method' => 'POST'
                ],
                'name' => [
                    'type' => 'text',
                    'value' => '',
                    'rules' => 'string|max:255'
                ],
                'email' => [
                    'type' => 'email',
                    'value' => '',
                    'rules' => 'required|email'
                ]
            ]);
        }

        return FormDecorator::make([
            FormElementDecorator::make('form')
                ->addType('form')
                ->addValue([
                    'method' => 'POST'
                ]),
            FormElementDecorator::make('name')
                ->addType('text')
                ->addValue([
                    'value' => ''
                ]),
        ]);
    }
}
