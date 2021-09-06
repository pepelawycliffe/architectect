<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePolicy
{
    use HandlesAuthorization;

    public function index(User $user)
    {
        return $user->hasPermission('templates.view');
    }

    public function show(User $user)
    {
        return $user->hasPermission('templates.view');
    }

    public function store(User $user)
    {
        return $user->hasPermission('templates.create');
    }

    public function update(User $user)
    {
        return $user->hasPermission('templates.update');
    }

    public function destroy(User $user)
    {
        return $user->hasPermission('templates.delete');
    }
}
