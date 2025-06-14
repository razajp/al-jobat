<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabric extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'supplier_id',
        'fabric_id',
        'color_id',
        'unit',
        'quantity',
        'reff_no',
        'remarks',
        'tag'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function fabric()
    {
        return $this->belongsTo(Setup::class, 'fabric_id');
    }
    public function color()
    {
        return $this->belongsTo(Setup::class, 'color_id');
    }
}
