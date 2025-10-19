<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientConfidenceScore extends Model
{
    protected $fillable = [
      'user_id',
      'score',
      'band',
      'breakdown',
      'computed_at'
    ];
}
