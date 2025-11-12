<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $notifications = $this->notificationService->getUnreadNotifications(
            $request->user(), 
            (int) $request->query('limit', 10)
        );

        $unreadCount = $this->notificationService->getUnreadCount($request->user());

        return response()->json([
            'ok' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Request $request, int $id)
    {
        $success = $this->notificationService->markAsRead($id, $request->user());

        return response()->json([
            'ok' => $success,
            'message' => $success ? 'Notification marked as read' : 'Notification not found'
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllAsRead($request->user());

        return response()->json([
            'ok' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function getUnreadCount(Request $request)
    {
        $count = $this->notificationService->getUnreadCount($request->user());

        return response()->json([
            'ok' => true,
            'unread_count' => $count
        ]);
    }
}
