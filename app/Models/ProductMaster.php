<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMaster extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'products';
    protected $primarykey = 'id';
    protected $fillable = [
        'ClientName',
        'ProductName',
        'ProductPrice',
        'Store',
        'Status',
        'AddedBy',
        'UpdatedBy',
        'created_at',
        'updated_at',
    ];
}
