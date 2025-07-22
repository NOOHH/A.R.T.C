<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodField extends Model
{
    protected $table = 'payment_method_fields';
    protected $fillable = [
        'payment_method_id',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'is_required',
        'sort_order'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean'
    ];

    /**
     * Get the payment method that owns the fields.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
