<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class StaffManagement extends Component
{
    use WithPagination;

    // List filters
    public string $search = '';

    // Create form
    public string $name     = '';
    public string $email    = '';
    public string $password = '';

    // Edit state
    public ?int $editingId    = null;
    public string $editName   = '';
    public string $editEmail  = '';
    public bool $editIsActive = true;

    protected function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    protected function editRules(): array
    {
        return [
            'editName'     => 'required|string|max:255',
            'editEmail'    => "required|email|unique:users,email,{$this->editingId}",
            'editIsActive' => 'boolean',
        ];
    }

    public function createStaff(): void
    {
        $this->validate($this->rules());

        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role'     => 'staff',
            'is_active' => true,
        ]);

        $this->reset(['name', 'email', 'password']);
        session()->flash('message', 'Staff member created successfully.');
    }

    public function editStaff(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId   = $id;
        $this->editName    = $user->name;
        $this->editEmail   = $user->email;
        $this->editIsActive = $user->is_active;
    }

    public function updateStaff(): void
    {
        $this->validate($this->editRules());

        User::where('id', $this->editingId)->update([
            'name'      => $this->editName,
            'email'     => $this->editEmail,
            'is_active' => $this->editIsActive,
        ]);

        $this->reset(['editingId', 'editName', 'editEmail', 'editIsActive']);
        session()->flash('message', 'Staff member updated successfully.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'editName', 'editEmail', 'editIsActive']);
    }

    public function deleteStaff(int $id): void
    {
        // Prevent accidental self-deletion
        if (auth()->id() === $id) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        User::where('id', $id)->where('role', 'staff')->delete();
        session()->flash('message', 'Staff member removed.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $staff = User::where('role', 'staff')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.staff-management', compact('staff'))
            ->layout('components.layouts.admin', ['title' => 'Staff Management']);
    }
}
