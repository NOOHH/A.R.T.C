<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTerms extends Model
{
    use HasFactory;

    protected $table = 'payment_terms';

    protected $fillable = [
        'terms_html',
    ];
} 