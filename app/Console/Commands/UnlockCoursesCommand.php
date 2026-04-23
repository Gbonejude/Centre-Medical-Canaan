<?php

namespace App\Console\Commands;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnlockCoursesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:courses:unlock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Débloquer les cours du mois en cours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $courses = Course::where('scheduled_month', $currentMonth)
            ->where('scheduled_year', $now->year)
            ->where('is_unlocked', false)
            ->get();

        foreach ($courses as $course) {
            $course->update(['is_unlocked' => true]);
        }

        $this->info(count($courses).' cours débloqués pour le mois en cours.');
    }
}
