<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Ad;
use App\Models\Userauth;
use App\Models\AuctionBid;

class AuctionHandler extends Model
{
    use HasFactory;

    protected $table = 'auction_handlers';

    protected $fillable = [
        'ad_id',
        'lot_number',
        'starting_price',
        'mini_bid_increment',
        'auction_duration_hours',
        'current_highest_bid',
        'start_time',
        'end_time',
        'status',
        'winner_user_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'starting_price' => 'decimal:2',
        'mini_bid_increment' => 'decimal:2',
        'current_highest_bid' => 'decimal:2',
        'auction_duration_hours' => 'integer',
    ];

    /**
     * Get the ad that owns the auction
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the winner of the auction
     */
    public function winner()
    {
        return $this->belongsTo(Userauth::class, 'winner_user_id');
    }

    /**
     * Get all bids for this auction
     */
    public function bids()
    {
        return $this->hasMany(AuctionBid::class, 'auction_handler_id');
    }

    /**
     * Get the highest bid for this auction
     */
    public function highestBid()
    {
        return $this->hasOne(AuctionBid::class, 'auction_handler_id')->orderByDesc('bid_amount');
    }

    /**
     * Check if auction is currently active
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               Carbon::now()->between($this->start_time, $this->end_time);
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTime()
    {
        if (!$this->isActive()) {
            return 0;
        }
        
        return Carbon::now()->diffInSeconds($this->end_time);
    }

    /**
     * Get current highest bid amount
     */
    public function getCurrentHighestBid()
    {
        return $this->current_highest_bid ?: $this->starting_price;
    }

    /**
     * Scope for active auctions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_time', '<=', Carbon::now())
                    ->where('end_time', '>', Carbon::now());
    }

    /**
     * Scope for ended auctions
     */
    public function scopeEnded($query)
    {
        return $query->where('status', 'ended')
                    ->orWhere('end_time', '<=', Carbon::now());
    }
}