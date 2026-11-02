<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ApplyController extends Controller
{
    /** Options offered by the application form (components/sections/Apply.tsx). */
    public const YEARS = ['First year', 'Second year', 'Third year', 'Fourth year', 'Fifth year or above', 'Postgraduate'];

    public const INSTITUTIONS = [
        'Universities' => [
            'Abia State University (ABSU)',
            'Michael Okpara University of Agriculture (MOUAU)',
            'Gregory University',
            'Clifford University',
            'Spiritan University',
            'Amadeus University',
            'Nigerian British University',
            'Lux Mundi University',
        ],
        'Polytechnics' => [
            'Ogbonnaya Onu Polytechnic (formerly Abia State Polytechnic)',
            'Federal Polytechnic Ngodo Isuochi',
            'Covenant Polytechnic',
            'Temple Gate Polytechnic',
            'Uma Ukpai Polytechnic',
            'Ihechukwu Madubuike Institute of Technology',
        ],
        'Colleges of Education' => [
            'Abia State College of Education (Technical)',
            'Federal College of Education, Ofeme–Ohuhu',
            'Covenant College of Education',
            'Diamond College of Education',
            'Harvard Wilson College of Education',
            'National Institute for Nigerian Languages (NINLAN)',
        ],
        'Health and Nursing Colleges' => [
            'Abia State College of Health Sciences and Management Technology',
            'Abia State College of Nursing Sciences',
            'College of Nursing Sciences, Obohia-Ndoki',
            'St. Antony College of Nursing Science',
            'Janet Frank College of Nursing Sciences',
            'Archbishop Charles Heerey Memorial College of Nursing Sciences',
            'Marjorie Bash College of Nursing Sciences',
            'Madonna College of Health Technology',
        ],
        'Theological and Other Colleges' => [
            'Assemblies of God Divinity School',
            'Methodist Theological Institute',
            'Trinity Theological College',
            'Literacy College of Management and Technology',
        ],
    ];

    public const SECTORS = [
        'Agriculture and food', 'Education', 'Health', 'Finance and payments', 'Commerce and retail',
        'Manufacturing and trade', 'Logistics and mobility', 'Fashion and creative', 'Energy and climate',
        'Media and entertainment', 'Other',
    ];

    public const STAGES = ['Just an idea', 'Building a prototype', 'Early users or customers', 'Making revenue'];

    public const TEAM_SIZES = ['Just me', '2 people', '3 to 4 people', '5 or more'];

    public const MENTOR_AREAS = [
        'Validating the idea', 'Finding customers', 'Product and technology', 'Business model and pricing',
        'Branding and marketing', 'Pitching', 'Legal and registration', 'Building a team', 'Managing money',
    ];

    public const COMMITMENTS = ['Yes, for all 6 weeks', 'Would need some flexibility'];

    public const HEARD = ['A friend or classmate', 'My institution', 'Social media', 'An Abia Tech Hub event', 'Other'];

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'institution' => ['required', 'string', Rule::in(array_merge(...array_values(self::INSTITUTIONS)))],
            'course' => ['required', 'string', 'max:120'],
            'year_of_study' => ['required', Rule::in(self::YEARS)],
            'startup_name' => ['required', 'string', 'max:80'],
            'sector' => ['required', Rule::in(self::SECTORS)],
            'stage' => ['required', Rule::in(self::STAGES)],
            'problem' => ['required', 'string', 'min:20', 'max:600'],
            'solution' => ['required', 'string', 'min:20', 'max:600'],
            'team_size' => ['required', Rule::in(self::TEAM_SIZES)],
            'link' => ['nullable', 'string', 'max:200'],
            'mentor_areas' => ['required', 'array', 'min:1'],
            'mentor_areas.*' => ['string', Rule::in(self::MENTOR_AREAS)],
            'goal' => ['required', 'string', 'min:20', 'max:600'],
            'commitment' => ['required', Rule::in(self::COMMITMENTS)],
            'heard_about' => ['nullable', Rule::in(self::HEARD)],
            'consent' => ['accepted'],
        ]);

        $digits = preg_replace('/\D/', '', $data['phone']) ?? '';
        if (strlen($digits) < 7 || strlen($digits) > 15 || ! preg_match('/^[+\d\s()-]+$/', $data['phone'])) {
            throw ValidationException::withMessages(['phone' => 'Enter a valid phone number.']);
        }

        $application = Application::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'institution' => $data['institution'],
            'course' => $data['course'],
            'year_of_study' => $data['year_of_study'],
            'startup_name' => $data['startup_name'],
            'sector' => $data['sector'],
            'stage' => $data['stage'],
            'problem' => $data['problem'],
            'solution' => $data['solution'],
            'team_size' => $data['team_size'],
            'link' => $data['link'] ?? null,
            'mentor_areas' => $data['mentor_areas'],
            'goal' => $data['goal'],
            'commitment' => $data['commitment'],
            'heard_about' => $data['heard_about'] ?? null,
            'consent' => true,
            'status' => 'new',
        ]);

        $this->notifyOwner($application);

        return response()->json(['ok' => true], 201);
    }

    private function notifyOwner(Application $application): void
    {
        $to = config('mail.notify_address') ?: env('NOTIFY_EMAIL');
        if (! $to) {
            return;
        }

        $lines = [
            'New application: '.$application->startup_name,
            $application->full_name.' · '.$application->email.' · '.$application->phone,
            $application->institution.' · '.$application->course.' · '.$application->year_of_study,
            'Sector: '.$application->sector,
            'Stage: '.$application->stage,
            'Team: '.$application->team_size,
            'Mentorship: '.implode(', ', $application->mentor_areas ?? []),
            'Link: '.($application->link ?: 'Not provided'),
            '',
            'Problem: '.$application->problem,
            'Solution: '.$application->solution,
            'Goal: '.$application->goal,
        ];

        try {
            Mail::raw(implode("\n", $lines), function ($message) use ($to, $application) {
                $message->to($to)->subject('New application: '.$application->startup_name);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
