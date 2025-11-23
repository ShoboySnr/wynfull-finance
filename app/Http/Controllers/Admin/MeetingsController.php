<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\AttachBroadcastMeetingAttendees;
use App\Jobs\BroadcastMeetingInvites;
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

        $end = (clone $start)->addMinutes((int)$data['duration']);

        // Create meeting row fast
        $meeting = Meeting::create([
            'organizer_id' => $admin->id,
            'starts_at'    => $start->utc(),
            'ends_at'      => $end->utc(),
            'status'       => 'scheduled',
            'mode'         => $data['mode'] ?? null,
            'meeting_link' => $data['meeting_link'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'scheduled_by' => $admin->id,
            'audience'     => $data['audience'],
        ]);

        // Single attendee = attach immediately
        if ($data['audience'] === 'single') {
            $attendee = User::findOrFail($data['attendee_id']);

            $meeting->attendees()->attach($attendee->id, [
                'status' => 'invited',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            BroadcastMeetingInvites::dispatch($meeting->id);
        } else {
            // Broadcast = attach in background
            AttachBroadcastMeetingAttendees::dispatch($meeting->id, $data['audience']);
        }

        activity()->useLog('admin')
            ->causedBy($admin)
            ->performedOn($meeting)
            ->event('admin.meeting.created')
            ->withProperties([
                'audience' => $data['audience'],
                'attendee_id' => $data['attendee_id'] ?? null,
            ])
            ->log('Admin scheduled a meeting');

        return back()->with('success',
            $data['audience'] === 'single'
                ? 'Meeting scheduled successfully.'
                : 'Broadcast meeting scheduled. Attendees are being attached in the background.'
        );
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
