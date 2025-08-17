<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthFormField;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        $rules = [];
        $fields = AuthFormField::forForm('register')->orderBy('sort_order')->get();
        if ($fields->isEmpty()) {
            $fields = collect([
                (object)['field_key' => 'name', 'type'=>'text', 'is_required'=>true],
                (object)['field_key' => 'email', 'type'=>'email', 'is_required'=>true],
                (object)['field_key' => 'password', 'type'=>'password', 'is_required'=>true],
                (object)['field_key' => 'password_confirmation', 'type'=>'password', 'is_required'=>true],
            ]);
        }
        foreach ($fields as $f) {
            // Check if is_enabled property exists before using it
            if (property_exists($f, 'is_enabled') && !$f->is_enabled) continue;
            $key = $f->field_key;
            $rule = [];
            if ($f->is_required) $rule[] = 'required'; else $rule[] = 'nullable';
            switch ($f->type) {
                case 'email': $rule[] = 'email'; if ($key==='email') { $rule[] = 'unique:users'; } break;
                case 'password': $rule[] = 'min:8'; if ($key==='password') { $rule[] = 'confirmed'; } break;
                default: $rule[] = 'string'; break;
            }
            $rules[$key] = implode('|', $rule);
        }
        return Validator::make($data, $rules);
    }

    protected function create(array $data)
    {
        $user = new User();
        $user->name = $data['name'] ?? ($data['username'] ?? 'User');
        $user->email = $data['email'] ?? null;
        $user->role = 'client'; // Set default role as client
        if (array_key_exists('username', $data)) {
            $user->username = $data['username'];
        }
        $user->password = isset($data['password']) ? Hash::make($data['password']) : Hash::make('password');
        $user->save();

        // save meta for any additional fields
        foreach ($data as $key => $value) {
            if (in_array($key, ['name','email','username','password','password_confirmation','_token'])) continue;
            UserMeta::create([
                'user_id' => $user->id,
                'meta_key' => $key,
                'meta_value' => is_array($value) ? json_encode($value) : $value,
            ]);
        }

        return $user;
    }
}
