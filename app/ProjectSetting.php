<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSetting extends Model
{
    use HasFactory;

    protected $visible = [
        'name',
        'value',
    ];
}
