<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schools extends Model
{
    protected $table = 'schools';
    public $timestamps = false;

    public function token() {
        return $this->hasOne('App\Model\Token', 'id', 'school_id');
    }
}
