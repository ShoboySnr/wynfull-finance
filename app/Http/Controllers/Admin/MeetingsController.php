<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\User;
use App\Services\Schedule\AdminMeetingService;
use Illuminate\Http\Request;

class MeetingsController extends Controller
{
    public function __construct(private readonly AdminMeetingService $service) {}

    public function index(Request $request)
    {
        $admin = $request->user();

        $meetings = Meeting::query()
            ->with(['attendee.profile:id,user_id,avatar_path,first_name,last_name', 'organizer:id,name'])
            ->where('organizer_id', $admin->id)
            ->latest('starts_at')
            ->paginate(20);

        return view('admin.meetings.index', compact('meetings'));
    }

    public function store(Request $request)
    {
        $admin = $request->user();

        $data = $request->validate([
            'attendee_id' => ['required','exists:users,id'],
            'date'        => ['required','date'],
            'start_time'  => ['required','date_format:H:i'],
            'duration'    => ['required','integer','min:15','max:240'],
            'mode'        => ['nullable','string','max:20'],
            'notes'       => ['nullable','string'],
        ]);

        $attendee = User::findOrFail($data['attendee_id']);

        $start = now()
            ->setDateFrom($data['date'])
            ->setTimeFromTimeString($data['start_time'])
            ->setTimezone(config('app.timezone'));

        $meeting = $this->service->schedule(
            $admin,
            $attendee,
            $start,
            $data['duration'],
            ['mode' => $data['mode'] ?? null, 'notes' => $data['notes'] ?? null]
        );

        activity()->useLog('admin')
            ->causedBy($admin)
            ->performedOn($meeting)
            ->event('admin.meeting.created')
            ->withProperties(['attendee_id' => $attendee->id])
            ->log('Admin scheduled a meeting');

        // TODO: send mail + custom notification

        return back()->with('success', 'Meeting scheduled successfully.');
    }

    public function reschedule(Request $request, Meeting $meeting)
    {
        $admin = $request->user();
        abort_unless($meeting->organizer_id === $admin->id, 403);

        $data = $request->validate([
            'date'       => ['required','date'],
            'start_time' => ['required','date_format:H:i'],
            'duration'   => ['required','integer','min:15','max:240'],
        ]);

        $attendee = $meeting->attendee;

        $start = now()
            ->setDateFrom($data['date'])
            ->setTimeFromTimeString($data['start_time'])
            ->setTimezone(config('app.timezone'));

        $meeting->delete();
        $new = $this->service->schedule($admin, $attendee, $start, $data['duration']);

        return back()->with('success', 'Meeting rescheduled.');
    }

    public function cancel(Request $request, Meeting $meeting)
    {
        $admin = $request->user();
        abort_unless($meeting->organizer_id === $admin->id, 403);

        $data = $request->validate([
            'reason' => ['nullable','string','max:1000']
        ]);

        $meeting->update([
            'status'        => 'cancelled',
            'cancelled_at'  => now(),
            'cancel_reason' => $data['reason'] ?? null,
        ]);

        activity()->useLog('admin')
            ->causedBy($admin)
            ->performedOn($meeting)
            ->event('admin.meeting.cancelled')
            ->log('Admin cancelled meeting');

        return back()->with('success', 'Meeting cancelled.');
    }
}
