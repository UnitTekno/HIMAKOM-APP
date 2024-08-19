<?php

// app/View/Components/LevelsGrid.php
namespace App\View\Components;

use Illuminate\View\Component;

class LevelsGrid extends Component
{
    public $levels;
    public $puzzles;
    public $userLevel;

    public function __construct($levels, $puzzles, $userLevel)
    {
        $this->levels = $levels;
        $this->puzzles = $puzzles;
        $this->userLevel = $userLevel;
    }

    public function render()
    {
        return view('components.levels-grid');
    }
}
