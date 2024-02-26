<?php

namespace App\Traits;

trait UploadFileTrait
{
    public function storeImage(string $field, string $folder_name, $file): static
    {
        $filename = $file->hashName();
        $file->move(public_path('uploaded-images/' . $folder_name), $filename);
        $this->update([$field => $filename]);
        return $this;
    }

    public function updateImage(string $field, string $folder_name, $file): static
    {
        if (isset($this->{$field})) {
            @unlink('uploaded-images/' . $folder_name . '/' . $this->{$field});
        }

        $filename = $file->hashName();
        $file->move(public_path('uploaded-images/' . $folder_name), $filename);
        $this->update([$field => $filename]);
        return $this;
    }

    public function deleteImage(string $field, string $folder_name): static
    {
        if (isset($this->{$field})) {
            @unlink('uploaded-images/' . $folder_name . '/' . $this->{$field});
        }

        return $this;
    }

    public function storeMultiImage($file, string $folder_name): static
    {
        $filename = $file->hashName();
        $file->move(public_path('uploaded-images/' . $folder_name), $filename);
        $this->image = $filename;
        return $this;
    }
}
