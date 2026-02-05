<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;

class AddMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $groupId = $this->input('group_id') ?? $this->route('group');
        if (!$groupId) return true;

        $group = Group::find($groupId);
        if (!$group) return false;
        
        $user = $this->user() ?? auth()->user();
        
        // Only admin (or existing member depending on policy) can add members
        return $user && $user->can('update', $group);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'group_id' => 'required|integer|exists:groups,id',
            'email' => 'required|email|exists:users,email',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $groupId = $this->input('group_id');
            $email = $this->input('email');
            
            if ($groupId && $email) {
                $group = Group::find($groupId);
                if ($group && $group->users()->where('email', $email)->exists()) {
                    $validator->errors()->add('email', 'User is already a member of this group.');
                }
            }
        });
    }
}
