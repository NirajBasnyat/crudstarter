<?php

namespace App\Traits;

use Storage;

trait FileUploadTrait
{
    public function fileUpload($model, string $field, string $folder_name, bool $delete)
    {
        if ($delete === true) {
            Storage::delete('public/' . $folder_name . '/' . $model->$field);
        }

        $image = request()->file($field);
        $filename = time() . '.' . $image->GetClientOriginalExtension();
        request()->file($field)->storeAs('public/' . $folder_name, $filename);
        $model->$field = $filename;
        $model->update([$field => $filename]);
        return $model;
    }

    public function fileUploadApi($model, string $field, string $folder_name)
    {
        $image = request()->file($field); //this is changed from upper method
        $filename = time() . '.' . $image->GetClientOriginalExtension();
        request()->file($field)->storeAs('public/' . $folder_name, $filename);
        $model->$field = $filename;
        $model->update([$field => $filename]);
        return $model;
    }
}