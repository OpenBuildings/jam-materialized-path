<?php

/**
 * @coversDefaultClass Kohana_Jam_Behavior_Materializedpath
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Jam_Behavior_MaterializedpathTest extends Testcase
{
    public function data_children_path()
    {
        return array(
            array(array('id' => 10), '10'),
            array(array('id' => 10, 'path' => '3'), '3/10'),
            array(array('id' => 23, 'path' => '3/12'), '3/12/23'),
        );
    }

    /**
     * @covers ::model_call_children_path
     * @dataProvider data_children_path
     */
    public function test_children_path($attributes, $expected)
    {
        $category = Jam::build('category', $attributes);

        $this->assertEquals($expected, $category->children_path());
    }

    public function data_path_ids()
    {
        return array(
            array(array('path' => '3/12'), array(3, 12)),
            array(array('path' => '3/12/23'), array(3, 12, 23)),
            array(array('path' => ''), array()),
        );
    }

    /**
     * @covers ::model_call_path_ids
     * @dataProvider data_path_ids
     */
    public function test_path_ids($attributes, $expected)
    {
        $category = Jam::build('category', $attributes);

        $this->assertEquals($expected, $category->path_ids());
    }

    public function data_is_decendent_of()
    {
        return array(
            array(
                array('path' => '3/12'),
                array('id' => 3),
                TRUE,
            ),
            array(
                array('path' => '3/12'),
                array('id' => 12, 'parent_id' => 3, 'path' => '3'),
                TRUE,
            ),
            array(
                array('id' => 23, 'parent_id' => 4, 'path' => '1/2/4'),
                array('id' => 4, 'parent_id' => 2, 'path' => '1/2'),
                TRUE,
            ),
            array(
                array('path' => '3/12'),
                array('id' => 20),
                FALSE,
            ),
        );
    }

    /**
     * @covers ::model_call_is_decendent_of
     * @dataProvider data_is_decendent_of
     */
    public function test_is_decendent_of($attributes, $ansestor_attributes, $expected)
    {
        $category = Jam::build('category', $attributes);
        $ansestor = Jam::build('category', $ansestor_attributes);

        $this->assertEquals($expected, $category->is_decendent_of($ansestor));
    }

    public function data_is_ansestor_of()
    {
        return array(
            array(
                array('id' => 3),
                array('path' => '3/12'),
                TRUE,
            ),
            array(
                array('id' => 12, 'parent_id' => 3, 'path' => '3'),
                array('path' => '3/12'),
                TRUE,
            ),
            array(
                array('id' => 20),
                array('path' => '3/12'),
                FALSE,
            ),
        );
    }

    /**
     * @covers ::model_call_is_ansestor_of
     * @dataProvider data_is_ansestor_of
     */
    public function test_is_ansestor_of($attributes, $decendent_attributes, $expected)
    {
        $category = Jam::build('category', $attributes);
        $decendent = Jam::build('category', $decendent_attributes);

        $this->assertEquals($expected, $category->is_ansestor_of($decendent));
    }

    /**
     * @covers ::model_call_is_root
     */
    public function test_is_root()
    {
        $category = Jam::build('category', array('parent_id' => 10));

        $this->assertFalse($category->is_root());

        $category = Jam::build('category', array());

        $this->assertTrue($category->is_root());
    }

    /**
     * @covers ::model_call_decendents
     */
    public function test_decendents()
    {
        $category = Jam::build('category', array(
            'id' => 12,
            'path' => '3/5'
        ));

        $this->assertEquals("SELECT `categories`.* FROM `categories` WHERE `categories`.`path` LIKE '3/5/12%'", (string) $category->decendents());
    }

    /**
     * @covers ::model_call_ansestors
     */
    public function test_ansestors()
    {
        $category = Jam::build('category', array(
            'id' => 12,
            'path' => '3/5'
        ));

        $this->assertEquals("SELECT `categories`.* FROM `categories` WHERE `categories`.`id` IN ('3', '5')", (string) $category->ansestors());

        $category = Jam::build('category');

        $this->assertEquals("SELECT `categories`.* FROM `categories`", (string) $category->ansestors());
        $this->assertCount(0, $category->ansestors());
    }
}
