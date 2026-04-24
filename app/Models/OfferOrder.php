<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class OfferOrder extends Model
{
    use HasFactory;
 
    protected $table = 'offer_order';
   
    protected $fillable = [
        'full_name',
        'mobile',
        'email',
        'card_number',
        'amount',
        'payment_id',
        'offer_type',
        'created_at',
        'updated_at',
    ];
 
}