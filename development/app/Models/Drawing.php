<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drawing extends Model
{
    use HasFactory;
    protected $table='drawings';
    protected $fillable = ['image_name', 'label', 'user_id'];
}