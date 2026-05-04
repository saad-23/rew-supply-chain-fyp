<div class="p-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Settings</h1>
            <p class="page-subtitle">Configure global application preferences</p>
        </div>
    </div>

    <div class="card card-body">
        @if (session()->has('message'))
            <div class="alert-success mb-5">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <!-- Site Identity -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-4">Identity</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Company Name</label>
                            <input type="text" wire:model="settings.site_name" class="input-enhanced" aria-label="Company name" placeholder="e.g. My Company">
                        </div>
                        <div>
                            <label class="form-label">Factory Address</label>
                            <input type="text" wire:model="settings.factory_address" class="input-enhanced" aria-label="Factory address" placeholder="e.g. 123 Industrial Area, Lahore">
                        </div>
                        <div>
                            <label class="form-label">Contact Email</label>
                            <input type="email" wire:model="settings.contact_email" class="input-enhanced" aria-label="Contact email" placeholder="info@company.com">
                        </div>
                    </div>
                </div>
                
                <hr class="border-slate-200 dark:border-slate-700">

                <!-- Preferences -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-blue-600 mb-4">System Preferences</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Currency Symbol</label>
                            <select wire:model="settings.currency" class="select-enhanced" aria-label="Currency">
                                    <option value="PKR">PKR (Pakistani Rupee)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                </select>
                        </div>
                        <div>
                            <label class="form-label" data-tooltip="Products at or below this quantity will trigger low stock alerts">Low Stock Alert Threshold <svg class="inline w-3.5 h-3.5 text-slate-400 cursor-help ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></label>
                            <input type="number" wire:model="settings.low_stock_threshold" class="input-enhanced" aria-label="Low stock threshold" placeholder="e.g. 10">
                             <p class="text-xs text-gray-500 mt-1">Products below this quantity will trigger alerts.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary btn-lg" aria-label="Save settings">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Config
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <span class="btn-spinner"></span> Saving…
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
