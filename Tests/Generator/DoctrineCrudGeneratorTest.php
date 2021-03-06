<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sensio\Bundle\GeneratorBundle\Tests\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;

class DoctrineCrudGeneratorTest extends GeneratorTest
{
    public function testGenerateYamlFull()
    {
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'yml', '/post', true, true);

        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/config/routing/post.yml',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $files = array(
            'Resources/config/routing/post.xml',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir.'/'.$file), sprintf('%s has not been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
            'public function newAction',
            'public function editAction',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    public function testGenerateXml()
    {
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'xml', '/post', false, true);

        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/config/routing/post.xml',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $files = array(
            'Resources/config/routing/post.yml',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir.'/'.$file), sprintf('%s has not been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }

        $content = file_get_contents($this->tmpDir.'/Controller/PostController.php');
        $strings = array(
            'public function newAction',
            'public function editAction',
            '@Route',
        );
        foreach ($strings as $string) {
            $this->assertNotContains($string, $content);
        }
    }

    public function testGenerateAnnotationWrite()
    {
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'annotation', '/post', true, true);

        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $files = array(
            'Resources/config/routing/post.yml',
            'Resources/config/routing/post.xml',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir.'/'.$file), sprintf('%s has not been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
            'public function newAction',
            'public function editAction',
            '@Route',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    public function testGenerateAnnotation()
    {
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'annotation', '/post', false, true);

        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $files = array(
            'Resources/config/routing/post.yml',
            'Resources/config/routing/post.xml',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir.'/'.$file), sprintf('%s has not been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
            '@Route',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }

        $content = file_get_contents($this->tmpDir.'/Controller/PostController.php');
        $strings = array(
            'public function newAction',
            'public function editAction',
        );
        foreach ($strings as $string) {
            $this->assertNotContains($string, $content);
        }
    }

    /**
     * @dataProvider getRoutePrefixes
     */
    public function testGetRouteNamePrefix($original, $expected)
    {
        $prefix = DoctrineCrudGenerator::getRouteNamePrefix($original);

        $this->assertEquals($expected, $prefix);
    }

    public function getRoutePrefixes()
    {
        return array(
            array('', ''),
            array('/', ''),
            array('//', ''),
            array('/{foo}', ''),
            array('/{_foo}', ''),
            array('/{/foo}', ''),
            array('/{/foo/}', ''),
            array('/{_locale}', ''),
            array('/{_locale}/foo', 'foo'),
            array('/{_locale}/foo/', 'foo'),
            array('/{_locale}/foo/{_format}', 'foo'),
            array('/{_locale}/foo/{_format}/', 'foo'),
            array('/{_locale}/foo/{_format}/bar', 'foo_bar'),
            array('/{_locale}/foo/{_format}/bar/', 'foo_bar'),
            array('/{_locale}/foo/{_format}/bar//', 'foo_bar'),
            array('/{foo}/foo/{bar}/bar', 'foo_bar'),
            array('/{foo}/foo/{bar}/bar/', 'foo_bar'),
            array('/{foo}/foo/{bar}/bar//', 'foo_bar'),
        );
    }

    protected function getGenerator()
    {
        $generator = new DoctrineCrudGenerator($this->filesystem, $this->tmpDir);
        $generator->setSkeletonDirs(__DIR__.'/../../Resources/skeleton');

        return $generator;
    }

    protected function getBundle()
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue($this->tmpDir));
        $bundle->expects($this->any())->method('getName')->will($this->returnValue('FooBarBundle'));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue('Foo\BarBundle'));

        return $bundle;
    }

    public function getMetadata()
    {
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')->disableOriginalConstructor()->getMock();
        $metadata->identifier = array('id');
        $metadata->fieldMappings = array('title' => array('type' => 'string'));

        return $metadata;
    }
}
