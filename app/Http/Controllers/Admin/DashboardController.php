<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Subscriber;
use App\Support\Audit;
use App\Support\Masking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role;

        $applications = Application::latest()->take(100)->get()
            ->map(fn (Application $application) => Masking::forRole($role, $application->toArray()));

        return view('admin.dashboard', [
            'user' => $user,
            'applications' => $applications,
            'messageCount' => Message::count(),
            'bookings' => Booking::latest()->take(100)->get(),
            'subscribers' => Subscriber::count(),
            'canEdit' => $user->atLeast('ADMIN'),
        ]);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,reviewing,accepted,declined'],
        ]);

        $application->forceFill(['status' => $data['status']])->save();
        Audit::log($request->user()->id, 'application.status', $application->id.' → '.$data['status']);

        return redirect()->route('admin.dashboard')->with('done', 'updated');
    }
}
