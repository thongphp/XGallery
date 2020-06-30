<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable=['actor_id', 'actor_table', 'action', 'object_id', 'object_table','text', 'extra'];
}
