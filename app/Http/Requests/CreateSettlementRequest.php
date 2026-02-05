<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;

class CreateSettlementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $groupId = $this->input('group_id');
        if (!$groupId) return true; // Let rules handle it

        $group = Group::find($groupId);
        if (!$group) return false;
        
        $user = $this->user() ?? auth()->user();
        if (!$user) return false;

        // User must be group member
        if (!$group->users()->where('users.id', $user->id)->exists()) {
            return false;
        }

        return $user->can('create-settlement', $group);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'group_id' => 'required|integer|exists:groups,id',
            'paid_from' => 'required|integer|exists:users,id',
            'paid_to' => 'required|integer|exists:users,id|different:paid_from',
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'payment_mode' => 'required|string|in:cash,bank_transfer,upi,other',
            'note' => 'nullable|string|max:255',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $groupId = $this->input('group_id');
            if (!$groupId) return;
            
            $group = Group::find($groupId);
            if (!$group) return;
            
            $paidFrom = $this->input('paid_from');
            $paidTo = $this->input('paid_to');
            
            // Check implicit group membership for both parties
            $users = $group->users()->whereIn('users.id', [$paidFrom, $paidTo])->pluck('users.id');
            if ($users->count() !== 2) {
                $validator->errors()->add('group_id', 'Both users must be members of the group.');
            }
        });
    }
}
