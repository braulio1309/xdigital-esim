<?php


namespace App\Services\Core;


use App\Helpers\Core\Traits\HasAttrs;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class BaseService
{
    use HasAttrs;
    protected $model;

    public function setModel(Model $model): BaseService
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function save($options = [])
    {
        if ($options instanceof Request) {
            $attributes = $options->all();
        } elseif (is_array($options)) {
            $attributes = $options;
        } else {
            $attributes = [];
        }

        if (empty($attributes)) {
            $attributes = request()->all();
        }

        $this->model
            ->fill($this->getFillAble($attributes))
            ->save();

        return $this->model;
    }

    public function find($id)
    {
        return $this->model =  $this->model::query()->find($id);
    }

    public function __call($method, $arguments)
    {
        return $this->model->{$method}(...$arguments);
    }

}
