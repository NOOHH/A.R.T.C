<?php

namespace App\Http\Controllers;

use App\Models\AuthFormField;
use Illuminate\Http\Request;

class AuthFormFieldController extends Controller
{
    public function index()
    {
        $loginFields = AuthFormField::where('form', 'login')->orderBy('sort_order')->get();
        $registerFields = AuthFormField::where('form', 'register')->orderBy('sort_order')->get();
        $loginIdentifier = \App\Models\AdminSetting::where('setting_key','login_identifier')->value('setting_value') ?? 'email';
        return view('admin.admin-settings.auth-fields', compact('loginFields','registerFields','loginIdentifier'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'form' => 'required|in:login,register',
            'field_key' => 'required|string|max:100',
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'is_required' => 'boolean',
            'is_enabled' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'options' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);
        if (isset($data['options'])) {
            $data['options'] = json_encode($data['options']);
        }
        AuthFormField::updateOrCreate(
            ['form' => $data['form'], 'field_key' => $data['field_key']],
            array_merge($data, [
                'is_required' => $request->boolean('is_required'),
                'is_enabled' => $request->boolean('is_enabled'),
                'sort_order' => $data['sort_order'] ?? 0,
            ])
        );
        return back()->with('status', 'Auth field saved');
    }

    public function update(Request $request, AuthFormField $authFormField)
    {
        $data = $request->validate([
            'label' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:50',
            'is_required' => 'boolean',
            'is_enabled' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'options' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);
        if (isset($data['options'])) {
            $data['options'] = json_encode($data['options']);
        }
        $authFormField->update(array_merge($data, [
            'is_required' => $request->boolean('is_required', $authFormField->is_required),
            'is_enabled' => $request->boolean('is_enabled', $authFormField->is_enabled),
        ]));
        return back()->with('status', 'Auth field updated');
    }

    public function destroy(AuthFormField $authFormField)
    {
        $authFormField->delete();
        return back()->with('status', 'Auth field deleted');
    }

    public function setLoginIdentifier(Request $request)
    {
        $request->validate(['login_identifier' => 'required|in:email,username']);
        \App\Models\AdminSetting::updateOrCreate(
            ['setting_key' => 'login_identifier'],
            ['setting_value' => $request->login_identifier]
        );
        return back()->with('status', 'Login identifier saved');
    }
}
