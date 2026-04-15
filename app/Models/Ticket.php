<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TicketRemark;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = date('mdHis');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'subject',
        'message',
        'status',
        'ticket_number',
        'mobile_number',
    ];

    /**
     * Get the customer that owns the ticket.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the remarks (now ticketRemarks) for the ticket.
     */
    public function ticketRemarks(): HasMany
    {
        return $this->hasMany(TicketRemark::class, 'ticket_number', 'ticket_number')->latest();
    }

    public function getRouteKeyName()
    {
        return 'ticket_number';
    }
}
