<?php

namespace App\Services\Privacy;

use App\Models\Athlete;
use App\Models\District;
use App\Models\MedalHistory;
use App\Models\Sport;
use App\Models\Venue;
use App\Models\YouthOrg;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AggregationService
{
    public function __construct(
        protected SmallCellSuppressor $suppressor
    ) {}

    /**
     * Athletes aggregated by sport with K-anonymity suppression.
     */
    public function athletesBySport(): array
    {
        $sports = Sport::withCount(['athletes' => function ($q) {
            $q->where('state', 'published');
        }])->get();

        $data = $sports->map(function ($sport) {
            $count = (int) $sport->athletes_count;
            return [
                'sport_id' => $sport->id,
                'sport_name' => $sport->name,
                'count' => $this->suppressor->suppress($count),
                'is_suppressed' => $this->suppressor->isSuppressed($count),
            ];
        })->values()->all();

        return $data;
    }

    /**
     * Athletes aggregated by district with K-anonymity suppression.
     */
    public function athletesByDistrict(): array
    {
        $districts = District::withCount(['athletes' => function ($q) {
            $q->where('state', 'published');
        }])->get();

        $data = $districts->map(function ($district) {
            $count = (int) $district->athletes_count;
            return [
                'district_id' => $district->id,
                'district_name' => $district->name,
                'population' => (int) $district->population,
                'count' => $this->suppressor->suppress($count),
                'is_suppressed' => $this->suppressor->isSuppressed($count),
            ];
        })->values()->all();

        return $data;
    }

    /**
     * Athlete demographics (age group & gender pyramid) with K-anonymity suppression.
     */
    public function athleteDemographics(): array
    {
        $currentYear = (int) date('Y');
        $athletes = Athlete::where('state', 'published')->get(['birth_year', 'gender']);

        $groups = [
            '< 15' => ['min' => 0, 'max' => 14],
            '15 - 18' => ['min' => 15, 'max' => 18],
            '19 - 23' => ['min' => 19, 'max' => 23],
            '> 23' => ['min' => 24, 'max' => 120],
        ];

        $pyramid = [];

        foreach ($groups as $label => $range) {
            $maleCount = 0;
            $femaleCount = 0;

            foreach ($athletes as $a) {
                $age = $currentYear - (int) $a->birth_year;
                if ($age >= $range['min'] && $age <= $range['max']) {
                    if (strtolower($a->gender) === 'm') {
                        $maleCount++;
                    } else {
                        $femaleCount++;
                    }
                }
            }

            $total = $maleCount + $femaleCount;

            $pyramid[] = [
                'age_group' => $label,
                'male_count' => $this->suppressor->suppress($maleCount),
                'is_male_suppressed' => $this->suppressor->isSuppressed($maleCount),
                'female_count' => $this->suppressor->suppress($femaleCount),
                'is_female_suppressed' => $this->suppressor->isSuppressed($femaleCount),
                'total_count' => $this->suppressor->suppress($total),
                'is_total_suppressed' => $this->suppressor->isSuppressed($total),
            ];
        }

        return $pyramid;
    }

    /**
     * Athletes aggregated by competitive level.
     */
    public function athletesByLevel(): array
    {
        $levels = ['club', 'district', 'province', 'national'];
        $counts = Athlete::where('state', 'published')
            ->select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->pluck('total', 'level')
            ->all();

        $result = [];
        foreach ($levels as $lvl) {
            $raw = (int) ($counts[$lvl] ?? 0);
            $result[] = [
                'level' => $lvl,
                'count' => $this->suppressor->suppress($raw),
                'is_suppressed' => $this->suppressor->isSuppressed($raw),
            ];
        }

        return $result;
    }

    /**
     * Youth organizations (OKP & Karang Taruna) by district.
     */
    public function youthOrgsByDistrict(): array
    {
        $districts = District::with(['youthOrgs' => function ($q) {
            $q->where('state', 'published');
        }])->get();

        return $districts->map(function ($d) {
            $okp = $d->youthOrgs->filter(fn ($o) => strtoupper($o->type) === 'OKP');
            $kt = $d->youthOrgs->filter(fn ($o) => str_replace(' ', '', strtoupper($o->type)) === 'KARANGTARUNA');

            return [
                'district_id' => $d->id,
                'district_name' => $d->name,
                'okp_count' => $okp->count(),
                'okp_members' => $okp->sum('members'),
                'karang_taruna_count' => $kt->count(),
                'karang_taruna_members' => $kt->sum('members'),
                'total_orgs' => $d->youthOrgs->count(),
                'total_members' => $d->youthOrgs->sum('members'),
            ];
        })->values()->all();
    }

    /**
     * Medal history aggregated by event and sport.
     */
    public function medalAggregates(): array
    {
        $byEvent = MedalHistory::where('state', 'published')
            ->select('event', 'year', DB::raw('SUM(gold) as gold'), DB::raw('SUM(silver) as silver'), DB::raw('SUM(bronze) as bronze'))
            ->groupBy('event', 'year')
            ->orderBy('year', 'desc')
            ->get();

        $bySport = Sport::with(['medalHistories' => function ($q) {
            $q->where('state', 'published');
        }])->get()->map(function ($s) {
            return [
                'sport_id' => $s->id,
                'sport_name' => $s->name,
                'gold' => $s->medalHistories->sum('gold'),
                'silver' => $s->medalHistories->sum('silver'),
                'bronze' => $s->medalHistories->sum('bronze'),
                'total_medals' => $s->medalHistories->sum('gold') + $s->medalHistories->sum('silver') + $s->medalHistories->sum('bronze'),
                'total_points' => (3 * $s->medalHistories->sum('gold')) + (2 * $s->medalHistories->sum('silver')) + (1 * $s->medalHistories->sum('bronze')),
            ];
        })->values()->all();

        return [
            'by_event' => $byEvent,
            'by_sport' => $bySport,
        ];
    }

    /**
     * System-wide summary and data quality ratios.
     */
    public function summaryStats(): array
    {
        $publishedVenues = Venue::where('state', 'published')->count();
        $totalAthletes = Athlete::where('state', 'published')->count();
        $totalYouthOrgs = YouthOrg::where('state', 'published')->count();

        $medals = MedalHistory::where('state', 'published')->select(
            DB::raw('SUM(gold) as total_gold'),
            DB::raw('SUM(silver) as total_silver'),
            DB::raw('SUM(bronze) as total_bronze')
        )->first();

        // Calculate dummy ratio across main published records
        $venueDummy = Venue::where('state', 'published')->where('is_dummy', true)->count();
        $athleteDummy = Athlete::where('state', 'published')->where('is_dummy', true)->count();
        $medalDummy = MedalHistory::where('state', 'published')->where('is_dummy', true)->count();
        $orgDummy = YouthOrg::where('state', 'published')->where('is_dummy', true)->count();

        $totalDummy = $venueDummy + $athleteDummy + $medalDummy + $orgDummy;
        $totalRecords = $publishedVenues + $totalAthletes + (MedalHistory::where('state', 'published')->count()) + $totalYouthOrgs;
        $dummyRatio = $totalRecords > 0 ? round($totalDummy / $totalRecords, 3) : 0.0;

        // Last updated date
        $latestDates = [
            Venue::max('updated_at'),
            Athlete::max('updated_at'),
            MedalHistory::max('updated_at'),
            YouthOrg::max('updated_at'),
        ];
        $lastUpdated = collect($latestDates)->filter()->max() ?: now()->toIso8601String();

        return [
            'total_venues' => $publishedVenues,
            'total_athletes' => $totalAthletes,
            'total_youth_orgs' => $totalYouthOrgs,
            'medals' => [
                'gold' => (int) ($medals->total_gold ?? 0),
                'silver' => (int) ($medals->total_silver ?? 0),
                'bronze' => (int) ($medals->total_bronze ?? 0),
                'total' => (int) (($medals->total_gold ?? 0) + ($medals->total_silver ?? 0) + ($medals->total_bronze ?? 0)),
            ],
            'dummy_ratio' => $dummyRatio,
            'dummy_percentage' => round($dummyRatio * 100, 1),
            'last_updated' => $lastUpdated,
        ];
    }
}
