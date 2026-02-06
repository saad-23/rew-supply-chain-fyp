<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class GeneralSettings extends Component
{
    public $settings = [];

    public function mount()
    {
        $this->settings = Setting::all()->pluck('value', 'key')->toArray();
    }

    public function save()
    {
        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        session()->flash('message', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.settings.general-settings');
    }
}
