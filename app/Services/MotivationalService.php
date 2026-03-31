<?php

namespace App\Services;

use App\Models\User;
use App\Models\StudySession;
use Carbon\Carbon;

class MotivationalService
{
    /**
     * Get greeting based on time of day.
     */
    public function getGreeting($name)
    {
        $hour = Carbon::now()->hour;
        
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }
        
        return $greeting . ', ' . $name . '!';
    }

    /**
     * Get motivational message based on study time.
     */
    public function getStudyMessage(User $student)
    {
        $todaySeconds = StudySession::where('student_id', $student->id)
            ->whereDate('started_at', Carbon::today())
            ->sum('duration_seconds');
        
        $weekSeconds = StudySession::where('student_id', $student->id)
            ->whereBetween('started_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('duration_seconds');
        
        if ($todaySeconds > 7200) { // 2+ hours
            return [
                'icon' => '🔥',
                'message' => 'Excellent progress today! Your dedication is inspiring.',
                'color' => '#f59e0b'
            ];
        } elseif ($todaySeconds > 3600) { // 1+ hour
            return [
                'icon' => '⚡',
                'message' => 'Great momentum! You\'ve studied for over an hour today.',
                'color' => '#10b981'
            ];
        } elseif ($todaySeconds > 1800) { // 30+ minutes
            return [
                'icon' => '📊',
                'message' => 'Solid progress! Keep building on today\'s momentum.',
                'color' => '#3b82f6'
            ];
        } elseif ($todaySeconds > 0) { // > 0 minutes
            return [
                'icon' => '🌱',
                'message' => 'Every minute counts. You\'ve made a great start today.',
                'color' => '#8b5cf6'
            ];
        } else {
            if ($weekSeconds > 0) {
                return [
                    'icon' => '⏰',
                    'message' => 'You haven\'t studied today. Start a session now to maintain your streak.',
                    'color' => '#f59e0b'
                ];
            } else {
                return [
                    'icon' => '🎯',
                    'message' => 'Ready to begin your learning journey? Start your first study session.',
                    'color' => '#ec4899'
                ];
            }
        }
    }

    /**
     * Get deadline reminder message.
     */
    public function getDeadlineReminder($deadline)
    {
        $daysLeft = Carbon::now()->diffInDays($deadline->due_date, false);
        
        if ($daysLeft < 0) {
            return [
                'icon' => '⚠️',
                'message' => 'Overdue: ' . $deadline->title . ' - ' . $deadline->unit->code,
                'type' => 'overdue'
            ];
        } elseif ($daysLeft == 0) {
            return [
                'icon' => '⏰',
                'message' => 'Due today: ' . $deadline->title . ' - ' . $deadline->unit->code,
                'type' => 'urgent'
            ];
        } elseif ($daysLeft <= 3) {
            return [
                'icon' => '📅',
                'message' => $daysLeft . ' day' . ($daysLeft > 1 ? 's' : '') . ' left: ' . $deadline->title,
                'type' => 'warning'
            ];
        }
        
        return null;
    }

    /**
     * Get achievement message.
     */
    public function getAchievementMessage($type, $value = null)
    {
        $achievements = [
            'first_session' => [
                'icon' => '🏁',
                'message' => 'First study session completed. You\'re on your way!',
                'badge' => 'Rookie'
            ],
            'one_hour' => [
                'icon' => '⏱️',
                'message' => '1 hour of total study time. Every minute adds up!',
                'badge' => 'Bronze'
            ],
            'five_hours' => [
                'icon' => '📈',
                'message' => '5 hours of study. You\'re building strong habits!',
                'badge' => 'Silver'
            ],
            'ten_hours' => [
                'icon' => '🏆',
                'message' => '10 hours milestone reached. Outstanding dedication!',
                'badge' => 'Gold'
            ],
            'weekly_streak' => [
                'icon' => '📊',
                'message' => $value . '-week streak maintained. Consistency is key!',
                'badge' => 'Streak'
            ]
        ];
        
        return $achievements[$type] ?? null;
    }

    /**
     * Get focus tip.
     */
    public function getFocusTip()
    {
        $tips = [
            'Try the Pomodoro technique: 25 minutes of focus, 5 minutes break.',
            'Eliminate distractions by putting your phone away during study sessions.',
            'Active recall is more effective than passive reading.',
            'Take handwritten notes to improve information retention.',
            'Regular short breaks improve long-term focus.',
            'Stay hydrated to maintain cognitive performance.',
            'Review material within 24 hours to strengthen memory.',
            'Create a dedicated study space free from interruptions.',
            'Set specific, measurable goals for each study session.',
            'Teach concepts to others to deepen your understanding.'
        ];
        
        return $tips[array_rand($tips)];
    }
}