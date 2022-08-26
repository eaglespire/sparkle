<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Transactions extends Component
{
    public $val;
    public $type;

    public function render()
    {
        return view('livewire.transactions');
    }
}
