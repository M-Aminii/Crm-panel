<?php

namespace App\Policies;

use App\Models\GlassLayer;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;

class GlassLayerPolicy
{
    use HandlesAuthorization;

    public function delete(User $user,GlassLayer $question)
    {
        if (!$question->isCreatedBy($user)) {
            throw new AuthorizationException;
        }else{
            return true;
        }
    }
    public function update(User $user,GlassLayer $Layer)
    {
        if (!$Layer->isCreatedBy($user)) {
            throw new AuthorizationException;
        }else{
            return true;
        }
    }
}
