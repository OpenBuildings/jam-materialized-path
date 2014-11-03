<?php

/**
 * @coversDefaultClass OpenBuildings\JamMaterializedPath\Init
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class IntegrationTest extends Testcase
{
    /**
     * @covers Kohana_Jam_Association_Materializedpath_Hasmany::save
     * @covers Kohana_Jam_Association_Materializedpath_Hasmany::model_after_delete
     */
    public function test_shallow()
    {
        $nine = Jam::create('category', array('name' => 'nine'));
        $four = Jam::find('category', 4);
        $four->children->add($nine);
        $four->save();

        $one = Jam::find('category', 1);
        $two = Jam::find('category', 2);
        $four = Jam::find('category', 4);
        $this->assertTrue($nine->is_decendent_of($one));
        $this->assertTrue($nine->is_decendent_of($two));
        $this->assertTrue($nine->is_decendent_of($four));

        $seven = Jam::find('category', 7);
        $seven->parent = $nine;
        $seven->save();
        $this->assertTrue($seven->is_decendent_of($nine));
        $this->assertTrue($seven->is_decendent_of($two));
        $this->assertTrue($seven->is_decendent_of($one));
        $this->assertTrue($seven->is_decendent_of($four));
        $this->assertTrue($nine->children->has($seven));
        $nine->delete();
        $this->assertEquals(array(2, 3, 4, 5, 6, 8), $one->decendents()->ids());
    }

    /**
     * @covers Kohana_Jam_Association_Materializedpath_Hasmany::save
     * @covers Kohana_Jam_Association_Materializedpath_Hasmany::add_items_query
     */
    public function test_add_child()
    {
        $two = Jam::find('category', 2);
        $seven = Jam::find('category', 7);

        $seven->children->add($two);

        $seven->save();

        $result = Jam::all('category')->as_array('id', 'path');

        $expected = array(
            1 => NULL,
            2 => '1/3/6/7',
            3 => '1',
            4 => '1/3/6/7/2',
            5 => '1/3/6/7/2',
            6 => '1/3',
            7 => '1/3/6',
            8 => '1/3/6',
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers Kohana_Jam_Association_Materializedpath_Hasmany::save
     * @covers Kohana_Jam_Association_Materializedpath_Hasmany::remove_items_query
     */
    public function test_remove_child()
    {
        $one = Jam::find('category', 1);
        $three = Jam::find('category', 3);

        $one->children->remove($three);

        $one->save();

        $result = Jam::all('category')->as_array('id', 'path');

        $expected = array(
            1 => NULL,
            2 => '1',
            3 => NULL,
            4 => '1/2',
            5 => '1/2',
            6 => '3',
            7 => '3/6',
            8 => '3/6',
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers Kohana_Jam_Association_Materializedpath_Belongsto::model_after_save
     */
    public function test_belongsto()
    {
        $two = Jam::find('category', 2);
        $seven = Jam::find('category', 7);

        $two->parent = $seven;

        $two->save();

        $result = Jam::all('category')->as_array('id', 'path');

        $expected = array(
            1 => NULL,
            2 => '1/3/6/7',
            3 => '1',
            4 => '1/3/6/7/2',
            5 => '1/3/6/7/2',
            6 => '1/3',
            7 => '1/3/6',
            8 => '1/3/6',
        );

        $this->assertEquals($expected, $result);
    }
}
