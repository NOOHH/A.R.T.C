<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodField;

class PaymentMethodFieldController extends Controller
{
    /**
     * Display a listing of fields for a payment method.
     */
    public function index($methodId)
    {
        $method = PaymentMethod::findOrFail($methodId);
        $fields = PaymentMethodField::where('payment_method_id', $methodId)
            ->orderBy('sort_order')
            ->get();
        return view('admin.payment-methods.fields.index', compact('method', 'fields'));
    }

    /**
     * API: return JSON list of fields for a payment method.
     */
    public function apiIndex($methodId)
    {
        $fields = PaymentMethodField::where('payment_method_id', $methodId)
            ->orderBy('sort_order')
            ->get(['field_name','field_label','field_type','field_options','is_required']);
        return response()->json($fields);
    }

    /**
     * Store a newly created field.
     */
    public function store(Request $request, $methodId)
    {
        $request->validate([
            'field_name' => 'required|string',
            'field_label' => 'required|string',
            'field_type' => 'required|in:text,number,date,file,textarea,select',
            'field_options' => 'nullable|array',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ]);
        $data = $request->only(['field_name','field_label','field_type','field_options','is_required','sort_order']);
        $data['payment_method_id'] = $methodId;
        PaymentMethodField::create($data);
        return response()->json(['success' => true, 'message' => 'Field added successfully']);
    }

    /**
     * Update the specified field.
     */
    public function update(Request $request, $fieldId)
    {
        $field = PaymentMethodField::findOrFail($fieldId);
        $request->validate([
            'field_label' => 'required|string',
            'field_options' => 'nullable|array',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ]);
        $field->update($request->only(['field_label','field_options','is_required','sort_order']));
        return response()->json(['success' => true, 'message' => 'Field updated successfully']);
    }

    /**
     * Remove the specified field.
     */
    public function destroy($fieldId)
    {
        $field = PaymentMethodField::findOrFail($fieldId);
        $field->delete();
        return response()->json(['success' => true, 'message' => 'Field deleted successfully']);
    }
    
    /**
     * Toggle payment method enabled/disabled status
     */
    public function toggleMethod(Request $request, $methodId)
    {
        $method = PaymentMethod::findOrFail($methodId);
        $method->update(['is_enabled' => $request->is_enabled]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Payment method status updated successfully'
        ]);
    }
}
