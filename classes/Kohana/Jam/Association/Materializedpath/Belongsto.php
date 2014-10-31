<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Jam_Association_Materializedpath_Belongsto extends Kohana_Jam_Association_Belongsto
{
    public function set(Jam_Validated $model, $value, $is_changed)
    {
        $value = parent::set($model, $value, $is_changed);

        if ($item = $model->{$this->name})
        {
            $model->path = $item->children_path();
        }

        return $value;
    }

    public function model_after_save(Jam_Model $model, Jam_Event_Data $data, $changed)
    {
        if ($value = Arr::get($changed, $this->name))
        {
            if (Jam_Association::is_changed($value) AND ($item = $model->{$this->name}))
            {
                Jam::update($model->meta()->name())
                    ->where('path', 'LIKE', $model->original('path').'%')
                    ->set(
                        'path',
                        DB::expr(
                            'TRIM(BOTH "/" FROM REPLACE(path, :old_path, :new_path)',
                            array(
                                ':old_path' => $model->original('path'),
                                ':new_path' => $item->children_path(),
                            )
                        )
                    )->execute();
            }
        }
    }
}
