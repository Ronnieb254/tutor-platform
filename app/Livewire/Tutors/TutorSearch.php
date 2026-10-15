<?php

namespace App\Livewire\Tutors;

use App\Models\TutorProfile;
use App\Models\Category;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;

class TutorSearch extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $categoryId   = '';
    public string $subjectId    = '';
    public string $minPrice     = '';
    public string $maxPrice     = '';
    public string $minRating    = '';
    public string $sortBy       = 'rating_avg';

    protected $queryString = [
        'search'    => ['except' => ''],
        'categoryId'=> ['except' => '', 'as' => 'category'],
        'subjectId' => ['except' => '', 'as' => 'subject'],
        'sortBy'    => ['except' => 'rating_avg'],
    ];

    public function updatedSearch()    { $this->resetPage(); }
    public function updatedCategoryId(){ $this->subjectId = ''; $this->resetPage(); }

    public function render()
    {
        $query = TutorProfile::approved()
            ->with(['user', 'subjects'])
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->subjectId, fn($q) => $q->whereHas('subjects', fn($s) => $s->where('subjects.id', $this->subjectId)))
            ->when($this->categoryId && !$this->subjectId, fn($q) => $q->whereHas('subjects', fn($s) => $s->where('category_id', $this->categoryId)))
            ->when($this->minPrice, fn($q) => $q->where('hourly_rate', '>=', $this->minPrice))
            ->when($this->maxPrice, fn($q) => $q->where('hourly_rate', '<=', $this->maxPrice))
            ->when($this->minRating, fn($q) => $q->where('rating_avg', '>=', $this->minRating))
            ->orderByDesc($this->sortBy);

        return view('livewire.tutors.tutor-search', [
            'tutors'     => $query->paginate(12),
            'categories' => Category::active()->with('subjects')->get(),
            'subjects'   => $this->categoryId
                ? Subject::where('category_id', $this->categoryId)->active()->get()
                : collect(),
        ]);
    }
}
