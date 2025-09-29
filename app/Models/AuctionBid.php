<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuctionHandler;
use App\Models\Userauth;

class AuctionBid extends Model
{
    use HasFactory;

    protected $table = 'auction_bids';

    // Table only has created_at (no updated_at), so disable default timestamps
    public $timestamps = false;

    protected $fillable = [
        'auction_handler_id',
        'user_id',
        'bid_amount'
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2'
    ];

    /**
     * Get the auction that owns the bid
     */
    public function auction()
    {
        return $this->belongsTo(AuctionHandler::class, 'auction_handler_id');
    }

    /**
     * Get the user who placed the bid
     */
    public function user()
    {
        return $this->belongsTo(Userauth::class);
    }

    /**
     * Scope for winning bids
     */
    public function scopeWinning($query)
    {
        return $query->where('is_winning', true);
    }

    /**
     * Scope for bids by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for bids on specific auction
     */
    public function scopeForAuction($query, $auctionId)
    {
        return $query->where('auction_handler_id', $auctionId);
    }

    /**
     * Scope for highest bid on auction
     */
    public function scopeHighestForAuction($query, $auctionId)
    {
        return $query->where('auction_handler_id', $auctionId)
                    ->orderByDesc('bid_amount')
                    ->limit(1);
    }
}