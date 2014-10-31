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
            $query->value('path', NULL);
        }

        return $query;
    }

    public function save(Jam_Model $model, Jam_Array_Association $collection)
    {
        parent::save($model, $collection);

        foreach ($collection->original() as $item)
        {
            $item = Jam::build($model)->load_fields($item);

            if ( ! $collection->has($item)) {
                $old_path = $item->children_path();
                $item->path = '';
                $new_path = $item->children_path();

                Jam::update($model)
                    ->where('path', 'LIKE', $old_path.'%')
                    ->value(
                        'path',
                        DB::expr(
                            'TRIM(BOTH "/" FROM REPLACE(path, :old_path, :new_path))',
                            array(
                                ':old_path' => $old_path,
                                ':new_path' => $new_path,
                            )
                        )
                    )->execute();
            }
        }

        foreach ($collection as $item)
        {
            if (FALSE === in_array($item->id(), $collection->original_ids()))
            {
                $old_path = $item->children_path();
                $item->path = $model->children_path();
                $new_path = $item->children_path();

                Jam::update($model)
                    ->where('path', 'LIKE', $old_path.'%')
                    ->value(
                        'path',
                        DB::expr(
                            'TRIM(BOTH "/" FROM REPLACE(path, :old_path, :new_path))',
                            array(
                                ':old_path' => $old_path,
                                ':new_path' => $new_path,
                            )
                        )
                    )->execute();
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
            ->where('path', 'LIKE', $model->children_path().'%')
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
