<?php


namespace App\Models\Core\File\Traits;


use App\Helpers\Core\Traits\FileHandler;
use Illuminate\Support\Facades\Storage;

trait FileAttribute
{
    use FileHandler;

    public function getFullUrlAttribute()
    {
        $system = config('filesystems.default');
        return Storage::disk($system)->url($this->path);
    }
}
