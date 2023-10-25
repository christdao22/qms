<?php
namespace App\View\Components;

use Illuminate\View\Component;

class Displaymonitor extends Component
{
    public string $displayTitleKey;
    public string $displayMonitor;

    public function __construct($displayTitleKey='', $displayMonitor='') {
        $this->displayTitleKey = $displayTitleKey;
        $this->displayMonitor = $displayMonitor;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.displaymonitor');
    }
}
