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

        return view('admin.schedule.index', compact('meetings'));
    }

    public function store(Request $request)
    {
        $admin = $request->user();

        $data = $request->validate([
            'audience'     => ['required', 'in:single,all_clients,all_coaches,all'],
            'attendee_id'  => ['nullable','required_if:audience,single','exists:users,id'],
            'date'         => ['required','date'],
            'start_time'   => ['required','date_format:H:i'],
            'duration'     => ['required','integer','min:15','max:240'],
            'mode'         => ['nullable','string','max:20'],
            'meeting_link' => ['nullable','url','max:500'],
            'notes'        => ['nullable','string'],
        ]);

        $start = now()
            ->setDateFrom($data['date'])
            ->setTimeFromTimeString($data['start_time'])
            ->setTimezone(config('app.timezone'));

        $audience = [
            'type' => $data['audience'],
            'user_id' => $data['attendee_id'] ?? null,
        ];

        $meeting = $this->service->scheduleBroadcast(
            $admin,
            $start,
            (int) $data['duration'],
            $audience,
            [
                'mode'         => $data['mode'] ?? null,
                'meeting_link' => $data['meeting_link'] ?? null,
                'notes'        => $data['notes'] ?? null,
            ]
        );

        activity()->useLog('admin')
            ->causedBy($admin)
            ->performedOn($meeting)
            ->event('admin.meeting.created')
            ->withProperties([
                'audience' => $data['audience'],
                'attendee_id' => $data['attendee_id'] ?? null,
            ])
            ->log('Admin scheduled a meeting');

        // Dispatch async notifications to attendees (recommended)
        // BroadcastMeetingInvites::dispatch($meeting->id);

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
