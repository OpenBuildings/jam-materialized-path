<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Jam_Association_Materializedpath_Belongsto extends Kohana_Jam_Association_Belongsto
{
    public function model_after_save(Jam_Model $model, Jam_Event_Data $data, $changed)
    {
        if ($value = Arr::get($changed, $this->name))
        {
            if ($item = $model->{$this->name})
            {
                $old_path = $model->children_path();
                $model->update_fields('path', $item->children_path());
                $new_path = $model->children_path();

                Jam::update($model)
                    ->where('path', 'LIKE', $old_path.'%')
                    ->update_children($old_path, $new_path)
                    ->execute();
            }
        }
    }
}
