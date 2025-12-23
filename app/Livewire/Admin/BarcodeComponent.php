<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Barcode;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class BarcodeComponent extends Component
{
    use InteractsWithBanner;

    public $deleteName = null;
    public $confirmingDeletion = false;
    public $selectedId = null;

    public function confirmDeletion($id, $name)
    {
        $this->deleteName = $name;
        $this->confirmingDeletion = true;
        $this->selectedId = $id;
    }

    public function delete()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $barcode = Barcode::find($this->selectedId);
        
        if ($barcode) {
            Attendance::where('barcode_id', $barcode->id)->update(['barcode_id' => null]);
            $barcode->delete();
        }
        
        $this->confirmingDeletion = false;
        $this->selectedId = null;
        $this->deleteName = null;
        $this->banner(__('Deleted successfully.'));
    }

    public function render()
    {
        $barcodes = Barcode::all();
        return view('livewire.admin.barcode', [
            'barcodes' => $barcodes
        ]);
    }
}
