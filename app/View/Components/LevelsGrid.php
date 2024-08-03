<?php

// app/View/Components/LevelsGrid.php
namespace App\View\Components;

use Illuminate\View\Component;

class LevelsGrid extends Component
{
    public $levels;
    public $width;
    public $height;
    public $unlocked;

    public function __construct($levels, $unlocked)
    {
        $this->levels = $levels;
        $this->unlocked = $unlocked;
    }

    public function render()
    {
        return view('components.levels-grid');
    }
}
