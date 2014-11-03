<?php

/**
 * @package    Openbuildings\Jam
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Model_Category2 extends Jam_Model {

    public static function initialize(Jam_Meta $meta)
    {
        $meta
            ->behaviors(array(
                'materializedpath' => Jam::behavior('materializedpath')
            ))

            ->fields(array(
                'id' => Jam::field('primary'),
                'name' => Jam::field('string'),
            ));
    }
}
