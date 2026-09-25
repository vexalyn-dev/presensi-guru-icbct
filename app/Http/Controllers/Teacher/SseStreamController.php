<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SseStreamController extends Controller
{
    private const EVENT_CACHE_TTL = 120;
    private const KEEPALIVE_SEC = 15;
    private const MAX_EVENTS_PER_USER = 50;

    public function __invoke(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            abort(401, 'Unauthorized');
        }

        $eventCacheKey = "sse_events_{$userId}";
        $lastEventId = (int) ($request->header('Last-Event-ID') ?: 0);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('Access-Control-Allow-Origin: *');

        ignore_user_abort(true);
        set_time_limit(0);

        while (true) {
            if (connection_aborted()) {
                break;
            }

            $events = Cache::get($eventCacheKey, []);
            $pending = [];

            foreach ($events as $event) {
                if ((int) $event['id'] > $lastEventId) {
                    $pending[] = $event;
                }
            }

            foreach ($pending as $event) {
                $eventId = $event['id'];
                $eventType = $event['type'] ?? 'update';
                $eventData = json_encode($event['data'] ?? []);

                echo "id: {$eventId}\n";
                echo "event: {$eventType}\n";
                echo "data: {$eventData}\n\n";
                ob_flush();
                flush();
                $lastEventId = (int) $eventId;
            }

            echo ": keepalive\n\n";
            ob_flush();
            flush();

            sleep(self::KEEPALIVE_SEC);
        }
    }

    public static function push(int $userId, string $type, array $data): void
    {
        $eventCacheKey = "sse_events_{$userId}";
        $events = Cache::get($eventCacheKey, []);

        $eventId = time() . '_' . uniqid();
        $events[] = [
            'id'    => $eventId,
            'type'  => $type,
            'data'  => $data,
            'ts'    => time(),
        ];

        if (count($events) > self::MAX_EVENTS_PER_USER) {
            $events = array_slice($events, -self::MAX_EVENTS_PER_USER);
        }

        Cache::put($eventCacheKey, $events, self::EVENT_CACHE_TTL);
    }
}
