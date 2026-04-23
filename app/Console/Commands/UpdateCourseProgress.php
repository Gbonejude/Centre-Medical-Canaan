<?php

namespace App\Console\Commands;

use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\UserCourse;
use Illuminate\Console\Command;

class UpdateCourseProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-course-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Met à jour la progression des cours à 100% si toutes les leçons sont terminées et un quiz est réussi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔁 Mise à jour des progressions des cours...');

        $userCourses = UserCourse::with('course.modules.lessons')->get();
        $updatedCount = 0;

        foreach ($userCourses as $userCourse) {
            $userId = $userCourse->user_id;
            $course = $userCourse->course;

            if (! $course || $course->modules->isEmpty()) {
                continue;
            }

            $lessonIds = $course->modules->pluck('lessons')->flatten()->pluck('id');

            $totalLessons = $lessonIds->count();

            $completedLessons = LessonProgress::where('user_id', $userId)
                ->whereIn('user_module_id', $lessonIds)
                ->where('is_completed', true)
                ->count();

            $quizPassed = QuizAttempt::where('user_id', $userId)
                ->whereHas('quizze', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })
                ->where('is_completed', true)
                ->where('score', '>=', 75)
                ->exists();

            if ($totalLessons > 0 && $completedLessons == $totalLessons && $quizPassed) {
                if ($userCourse->progress < 100) {
                    $userCourse->update(['progress' => 100]);
                    $this->info("✅ Progression mise à jour à 100% pour l'utilisateur $userId (cours ID: {$course->id})");
                    $updatedCount++;
                }
            }
        }
        $this->info("🎯 $updatedCount progression(s) mise(s) à jour.");
    }
}
