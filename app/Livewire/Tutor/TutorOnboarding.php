<?php

namespace App\Livewire\Tutor;

use App\Models\TutorProfile;
use App\Models\Subject;
use App\Models\Wallet;
use Livewire\Component;
use Livewire\WithFileUploads;

class TutorOnboarding extends Component
{
    use WithFileUploads;

    public int    $step         = 1;
    public string $bio          = '';
    public string $hourlyRate   = '';
    public int    $experienceYears = 0;
    public array  $languages    = ['English'];
    public string $country      = 'Kenya';
    public string $city         = '';
    public array  $selectedSubjects = [];

    // Availability
    public array $availability = [
        0 => ['active' => false, 'start' => '09:00', 'end' => '17:00'],
        1 => ['active' => true,  'start' => '09:00', 'end' => '17:00'],
        2 => ['active' => true,  'start' => '09:00', 'end' => '17:00'],
        3 => ['active' => true,  'start' => '09:00', 'end' => '17:00'],
        4 => ['active' => true,  'start' => '09:00', 'end' => '17:00'],
        5 => ['active' => true,  'start' => '09:00', 'end' => '13:00'],
        6 => ['active' => false, 'start' => '09:00', 'end' => '17:00'],
    ];

    // Documents
    public $nationalId         = null;
    public $tscCertificate     = null;
    public $academicCertificate = null;

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'bio'            => 'required|min:50',
                'hourlyRate'     => 'required|numeric|min:500',
                'experienceYears'=> 'required|integer|min:0',
                'city'           => 'required',
            ]);
        }
        if ($this->step === 2) {
            $this->validate(['selectedSubjects' => 'required|array|min:1']);
        }
        $this->step++;
    }

    public function prevStep() { $this->step = max(1, $this->step - 1); }

    public function submit()
    {
        $this->validate([
            'nationalId'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'tscCertificate'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'academicCertificate' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = auth()->user();

        // Create tutor profile
        $profile = TutorProfile::create([
            'user_id'          => $user->id,
            'bio'              => $this->bio,
            'hourly_rate'      => $this->hourlyRate,
            'experience_years' => $this->experienceYears,
            'languages'        => $this->languages,
            'country'          => $this->country,
            'city'             => $this->city,
            'is_approved'      => false,
        ]);

        // Attach subjects
        $profile->subjects()->sync($this->selectedSubjects);

        // Save availability
        foreach ($this->availability as $day => $slot) {
            if ($slot['active']) {
                $profile->availabilities()->create([
                    'day_of_week' => $day,
                    'start_time'  => $slot['start'],
                    'end_time'    => $slot['end'],
                    'is_active'   => true,
                ]);
            }
        }

        // Store documents
        $docTypes = [
            'nationalId'         => 'national_id',
            'tscCertificate'     => 'tsc_certificate',
            'academicCertificate' => 'academic_certificate'
        ];
        foreach ($docTypes as $prop => $type) {
            if ($this->{$prop}) {
                $path = $this->{$prop}->store("documents/{$user->id}", 'public');
                $profile->documents()->create([
                    'type'          => $type,
                    'file_path'     => $path,
                    'original_name' => $this->{$prop}->getClientOriginalName(),
                    'status'        => 'pending',
                ]);
            }
        }

        // Create wallet
        Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'currency' => 'KES']);

        $this->step = 5; // Done step
    }

    public function render()
    {
        return view('livewire.tutor.tutor-onboarding', [
            'subjects' => Subject::active()->with('category')->get()->groupBy('category.name'),
        ]);
    }
}
