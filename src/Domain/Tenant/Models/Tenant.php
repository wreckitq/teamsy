<?php

namespace Domain\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
