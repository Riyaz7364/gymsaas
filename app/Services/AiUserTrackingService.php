<?php

namespace App\Services;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AiUserTrackingService
{
    public function evaluate(Member $member, Collection $attendancePatternRows): array
    {
        $reasons = [];
        $score = 0;

        $lastCheckIn = $member->last_check_in ? Carbon::parse($member->last_check_in) : null;
        $daysSinceLastVisit = $lastCheckIn ? $lastCheckIn->diffInDays(now()) : null;
        $attendance30 = (int) ($member->attendance_30_count ?? 0);
        $attendancePrev30 = (int) ($member->attendance_prev_30_count ?? 0);
        $overdueInvoices = (int) ($member->overdue_invoices_count ?? 0);
        $pendingBalance = (float) ($member->pending_balance_sum ?? 0);

        $planEndDate = $member->activePlan?->end_date;
        $planDaysToExpire = $planEndDate ? now()->startOfDay()->diffInDays($planEndDate->copy()->startOfDay(), false) : null;

        if ($daysSinceLastVisit === null || $daysSinceLastVisit > 21) {
            $score += 35;
            $reasons[] = 'No recent gym visit in the last 3 weeks.';
        } elseif ($daysSinceLastVisit > 14) {
            $score += 25;
            $reasons[] = "Last visit was {$daysSinceLastVisit} days ago.";
        } elseif ($daysSinceLastVisit > 7) {
            $score += 15;
            $reasons[] = "Visit gap is increasing ({$daysSinceLastVisit} days).";
        }

        if ($attendance30 <= 2) {
            $score += 20;
            $reasons[] = "Only {$attendance30} visits in last 30 days.";
        } elseif ($attendance30 <= 5) {
            $score += 12;
            $reasons[] = "Low attendance trend ({$attendance30} visits in last 30 days).";
        }

        if ($attendancePrev30 > 0 && $attendance30 < (int) ceil($attendancePrev30 * 0.6)) {
            $score += 15;
            $reasons[] = "Attendance dropped from {$attendancePrev30} to {$attendance30} visits.";
        }

        if ($planDaysToExpire !== null) {
            if ($planDaysToExpire < 0) {
                $score += 30;
                $reasons[] = 'Membership plan is expired.';
            } elseif ($planDaysToExpire <= 3) {
                $score += 20;
                $reasons[] = "Plan expires in {$planDaysToExpire} day(s).";
            } elseif ($planDaysToExpire <= 7) {
                $score += 12;
                $reasons[] = "Plan expires soon ({$planDaysToExpire} days).";
            }
        } else {
            $score += 15;
            $reasons[] = 'No active membership plan found.';
        }

        if ($overdueInvoices > 0) {
            $score += 15;
            $reasons[] = "{$overdueInvoices} overdue invoice(s) and pending dues.";
        }

        if (in_array($member->status, ['expired', 'inactive', 'frozen'], true)) {
            $statusScores = ['frozen' => 20, 'expired' => 25, 'inactive' => 30];
            $score += $statusScores[$member->status] ?? 0;
            $reasons[] = 'Member status is currently ' . $member->status . '.';
        }

        if ($member->trainer->isEmpty()) {
            $score += 5;
            $reasons[] = 'No trainer currently assigned.';
        }

        $score = max(0, min(100, $score));
        $riskLevel = $score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'low');

        $bestTime = $this->bestOfferTime($attendancePatternRows);
        $offerType = $this->offerType($riskLevel, $planDaysToExpire);
        $canSendOffer = $score >= 40 && (bool) $member->whatsapp_optin && !empty($member->phone);

        return [
            'member' => $member,
            'risk_score' => $score,
            'risk_level' => $riskLevel,
            'days_since_last_visit' => $daysSinceLastVisit,
            'attendance_30_count' => $attendance30,
            'attendance_prev_30_count' => $attendancePrev30,
            'plan_days_to_expire' => $planDaysToExpire,
            'overdue_invoices_count' => $overdueInvoices,
            'pending_balance_sum' => $pendingBalance,
            'best_offer_time' => $bestTime,
            'offer_type' => $offerType,
            'can_send_offer' => $canSendOffer,
            'reasons' => array_slice(array_unique($reasons), 0, 4),
        ];
    }

    private function offerType(string $riskLevel, ?int $planDaysToExpire): string
    {
        if ($planDaysToExpire !== null && $planDaysToExpire <= 7) {
            return 'Renewal Offer';
        }

        return match ($riskLevel) {
            'high' => 'Win-Back Offer',
            'medium' => 'Retention Offer',
            default => 'Upsell Offer',
        };
    }

    private function bestOfferTime(Collection $rows): string
    {
        if ($rows->isEmpty()) {
            return 'Evening (6 PM - 8 PM)';
        }

        $topHour = (int) $rows->groupBy('hour_of_day')
            ->map(fn (Collection $items) => $items->sum('visits'))
            ->sortDesc()
            ->keys()
            ->first();

        $topDay = (int) $rows->groupBy('day_idx')
            ->map(fn (Collection $items) => $items->sum('visits'))
            ->sortDesc()
            ->keys()
            ->first();

        $dayMap = [1 => 'Sun', 2 => 'Mon', 3 => 'Tue', 4 => 'Wed', 5 => 'Thu', 6 => 'Fri', 7 => 'Sat'];
        $hour12 = $topHour % 12 === 0 ? 12 : $topHour % 12;
        $ampm = $topHour >= 12 ? 'PM' : 'AM';

        return ($dayMap[$topDay] ?? 'Weekday') . " around {$hour12}:00 {$ampm}";
    }
}
