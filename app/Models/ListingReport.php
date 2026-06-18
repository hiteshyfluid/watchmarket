<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingReport extends Model
{
    protected $fillable = [
        'listing_url',
        'issue_type',
        'description',
        'serial_number',
        'reporter_name',
        'reporter_email',
        'status',
    ];

    const ISSUE_LABELS = [
        'counterfeit'   => 'Counterfeit / Fake Watch',
        'stolen'        => 'Stolen Watch',
        'fraudulent'    => 'Fraudulent Listing',
        'misrepresented'=> 'Misrepresented Condition',
        'scam_seller'   => 'Suspected Scam Seller',
        'other'         => 'Other Issue',
    ];

    public function issueLabel(): string
    {
        return self::ISSUE_LABELS[$this->issue_type] ?? ucfirst($this->issue_type);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'new'      => 'bg-red-100 text-red-700',
            'reviewed' => 'bg-yellow-100 text-yellow-700',
            'resolved' => 'bg-green-100 text-green-700',
            default    => 'bg-gray-100 text-gray-700',
        };
    }
}
