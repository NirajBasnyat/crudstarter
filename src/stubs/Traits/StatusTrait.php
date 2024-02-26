<?php

namespace App\Traits;

trait StatusTrait
{
    public function changeItemStatus($modelName, $id, $status): void
    {
        $model = "App\\Models\\" . $modelName;
        $value = $model::findOrFail($id);
        $value->status = $status;
        $value->save();
    }
}
