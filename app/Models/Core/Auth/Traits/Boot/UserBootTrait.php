<?php


namespace App\Models\Core\Auth\Traits\Boot;


use App\Hooks\User\AfterUserPivotAction;
use App\Hooks\User\AfterUserSaved;
use App\Hooks\User\WhileUserDeleting;
use App\Models\Core\Auth\User;
use App\Models\Core\Auth\UsuarioClaveHistorial;

trait UserBootTrait
{
    public static function boot() : void
    {
        parent::boot();
        if (!app()->runningInConsole()){
            static::creating(function($model){
                return $model->fill([
                    'created_by' => auth()->id()
                ]);
            });
        }

        static::saved(function ($user) {
            if (($user->wasChanged('password') || $user->wasRecentlyCreated) && $user->password) {
                UsuarioClaveHistorial::create([
                    'user_id'  => $user->id,
                    'password' => $user->password,
                ]);
            }

            AfterUserSaved::new(true)
                ->setModel($user)
                ->handle();
        });

        static::synced(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('synced')
                ->handle();
        });

        static::attached(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('attached')
                ->handle();
        });

        static::detached(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('detached')
                ->handle();
        });

        static::updatingExistingPivot(function ($user, $relation, $properties) {
            AfterUserPivotAction::new(true)
                ->setModel($user)
                ->setProperties($properties)
                ->setRelation($relation)
                ->setType('updatingExistingPivot')
                ->handle();
        });

        static::deleting(function (User $user) {
            WhileUserDeleting::new(true)
                ->setModel($user)
                ->handle();
        });

    }
}
