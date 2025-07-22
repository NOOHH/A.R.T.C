<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentMethodFieldController extends Controller
{
    /**
     * List fields for a payment method
     */
    public function apiIndex($methodId)
    {
        // TODO: return actual fields for the method
        return response()->json([]);
    }

    /**
     * Store a new payment method field
     */
    public function store(Request $request, $methodId)
    {
        // TODO: implement storing logic
        return response()->json(['success' => false, 'message' => 'Not implemented'], 501);
    }

    /**
     * Delete a payment method field
     */
    public function destroy($fieldId)
    {
        // TODO: implement delete logic
        return response()->json(['success' => false, 'message' => 'Not implemented'], 501);
    }

    /**
     * Toggle a payment method's enabled status
     */
    public function toggleMethod(Request $request, $methodId)
    {
        // TODO: implement toggle logic
        return response()->json(['success' => false, 'message' => 'Not implemented'], 501);
    }
}
