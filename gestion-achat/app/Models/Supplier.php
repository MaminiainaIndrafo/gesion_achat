<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    use HasFactory;

    protected $table = 'suppliers';

    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'name',
        'contact_email',
        'contact_phone',
        'address',
        'api_endpoint',
        'api_key'
    ];
    
}
