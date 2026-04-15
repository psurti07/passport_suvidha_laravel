<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationStatuses extends Model
{
    use HasFactory;
    protected $table = 'application_statuses';
    protected $fillable = [
        "id",
        "status_name",
        "slug",
        "priority_no",
        "created_at",
        "updated_at",
        "deleted_at"    
    ];
}
