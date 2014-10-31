<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Behavior_Materializedpath extends Jam_Behavior
{
    public function initialize(Jam_Meta $meta, $name)
    {
        parent::initialize($meta, $name);

        $meta
            ->associations([
                'parent' => Jam::association('materializedpath_belongsto', array(
                    'foreign_model' => $meta->model(),
                    'inverse_of' => 'children',
                )),
                'children' => Jam::association('materializedpath_hasmany', [
                    'foreign_key' => 'parent_id',
                    'foreign_model' => $meta->model(),
                    'inverse_of' => 'parent',
                ]),
            ])
            ->fields([
                'path' => Jam::field('string'),
            ]);
    }

    public function model_call_children_path(Jam_Model $model, Jam_event_data $data)
    {
        $id = $model->id();

        $data->return = $model->path ? $model->path.'/'.$id : $id;
    }

    public function model_call_path_ids(Jam_Model $model, Jam_event_data $data)
    {
        $data->return = $model->path ? explode('/', $model->path) : array();
    }

    public function model_call_is_root(Jam_Model $model, Jam_event_data $data)
    {
        $data->return = empty($model->parent_id);
    }

    public function model_call_decendents(Jam_Model $model, Jam_event_data $data)
    {
        $path = $model->children_path();

        $data->return = Jam::all($model)->where('path', 'LIKE', "{$path}%");
    }

    public function model_call_is_decendent_of(Jam_Model $model, Jam_event_data $data, Jam_Model $ancestor)
    {
        $data->return = in_array($ancestor->id(), $model->path_ids());
    }

    public function model_call_is_ansestor_of(Jam_Model $model, Jam_event_data $data, Jam_Model $decendent)
    {
        $data->return = $decendent->is_decendent_of($model);
    }

    public function model_call_depth(Jam_Model $model, Jam_event_data $data)
    {
        $data->return = count($model->path_ids());
    }

    public function model_call_ansestors(Jam_Model $model, Jam_event_data $data)
    {
        $path_ids = $model->path_ids();

        if ($path_ids)
        {
            $data->return = Jam::all($model)->where($model->meta()->primary_key(), 'IN', $path_ids);
        }
        else
        {
            $data->return = Jam_Query_Builder_Collection::factory($model)->load_fields(array());
        }
    }
}
