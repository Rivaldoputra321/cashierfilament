<?php

namespace App\Service;

use Livewire\Component;

class BarcodeScanner extends Component
{
    public $scanTarget = 'product'; // 'product' or 'member'
    public $code = '';
    
    public function mount($target = 'product')
    {
        $this->scanTarget = $target;
    }
    
    public function render()
    {
        return view('livewire.barcode-scanner');
    }
    
    public function scanComplete($code)
    {
        $this->code = $code;
        $this->emit('barcodeScanned', [
            'target' => $this->scanTarget,
            'code' => $code
        ]);
    }
}