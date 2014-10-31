<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Jam_Association_Materializedpath_Hasmany extends Kohana_Jam_Association_Hasmany {

    public function add_items_query(Jam_Model $model, array $ids)
    {
        return parent::add_items_query($model, $ids)
            ->value('path', $model->children_path());
    }

    public function remove_items_query(Jam_Model $model, array $ids)
    {
        $query = parent::remove_items_query($model, $ids);

        if ($query instanceof Jam_Query_Builder_Update) {
            $query
                ->value('path', $model->children_path());
        }

        return $query;
    }

    /**
     * Set the foreign and polymorphic keys on an item when its set to the associated collection
     *
     * @param  Jam_Model $model
     * @param  Jam_Model $item
     */
    public function item_set(Jam_Model $model, Jam_Model $item)
    {
        parent::item_set($model, $item);

        $item->path = $model->children_path();
    }

    public function item_unset(Jam_Model $model, Jam_Model $item)
    {
        parent::item_unset($model, $item);

        $item->path = '';
    }

    public function save(Jam_Model $model, Jam_Array_Association $collection)
    {
        parent::save($model, $collection);

        foreach ($collection->original() as $item)
        {
            if ( ! $collection->has($item)) {
                Jam::update($model->meta()->name())
                    ->where('path', 'LIKE', $item->original('path').'%')
                    ->set(
                        'path',
                        DB::expr(
                            'TRIM(BOTH "/" FROM REPLACE(path, :old_path, :new_path))',
                            array(
                                ':old_path' => $model->original('path'),
                                ':new_path' => '',
                            )
                        )
                    );
            }
        }

        foreach ($collection as $item)
        {
            if (FALSE === in_array($item->id(), $collection->original_ids()))
            {
                Jam::update($model)
                    ->where('path', 'LIKE', $item->original('path').'%')
                    ->value(
                        'path',
                        DB::expr(
                            'TRIM(BOTH "/" FROM REPLACE(path, :old_path, :new_path))',
                            array(
                                ':old_path' => $item->original('path'),
                                ':new_path' => $item->path,
                            )
                        )
                    );
            }
        }
    }

    /**
     * Persist this collection in the database
     * @param  Jam_Model $model
     */
    public function model_after_delete(Jam_Model $model)
    {
        Jam::update($model)
            ->where('path', 'LIKE', $model->path.'%')
            ->value(
                'path',
                DB::expr(
                    'TRIM(BOTH "/" FROM REPLACE(path, :old_path, :new_path))',
                    array(
                        ':old_path' => $model->path,
                        ':new_path' => '',
                    )
                )
            )
            ->execute();
    }
}
