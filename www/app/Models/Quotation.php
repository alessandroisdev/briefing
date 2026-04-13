<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';

    protected $fillable = [
        'client_id',
        'briefing_id',
        'title',
        'total_amount',
        'status',
        'valid_until',
        'pdf_url',
        'client_notes'
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function briefing()
    {
        return $this->belongsTo(ClientBriefing::class, 'briefing_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
}
