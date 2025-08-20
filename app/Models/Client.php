<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // Force this model to always use the main database connection
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'db_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'status',
        'user_id',
        'archived'
    ];
    
    protected $casts = [
        'archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function websiteRequest()
    {
        return $this->hasOne(WebsiteRequest::class);
    }
}


