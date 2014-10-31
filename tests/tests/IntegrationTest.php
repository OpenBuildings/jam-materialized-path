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
    public function test_deep()
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
        $this->assertEquals(array(2, 3, 4, 5, 6, 7), $one->decendents()->ids());
    }
}
