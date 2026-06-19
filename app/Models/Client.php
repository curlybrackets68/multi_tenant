<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $connection = 'master';

    protected $fillable = [
        'name',
        'subdomain',
        'db_host',
        'db_port',
        'db_name',
        'db_username',
        'db_password',
        'status',
    ];
}
