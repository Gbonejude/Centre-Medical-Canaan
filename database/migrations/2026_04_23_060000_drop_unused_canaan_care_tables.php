<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables à supprimer — ne font pas partie du système de gestion
     * des rendez-vous hospitaliers (ESGIS Groupe 6, 2023-2026).
     */
    private array $tablesToDrop = [
        // ── Ancienne gestion clients (Canaan Care) ──
        'customers',
        'customer_user',

        // ── Paiements & finances ──
        'payments',
        'unpaid',
        'unpaids',
        'income_types',
        'incomes',
        'expense_subcategories',
        'expense_types',
        'expenses',
        'bonuses',
        'contractor_hours',

        // ── Assurances ──
        'assurances',
        'assurance_payments',

        // ── Offres d'emploi & candidatures ──
        'job_offers',
        'job_offer_schedules',
        'job_offer_responses',
        'job_applications',
        'candidate_educations',
        'candidate_employment_histories',
        'candidate_personal_references',
        'certificates',

        // ── Archives ──
        'archive_permission',
        'archives',

        // ── Maisons de soins & planning ──
        'care_house_permission',
        'care_houses',
        'schedules',
        'attendances',
        'leave_requests',
        'sick_hours',

        // ── Paie ──
        'hourly_rate_histories',
        'shift_rate_settings',
        'payroll_hour_overrides',
        'payroll_notes',
        'rates',
        'services',

        // ── Formations & quiz ──
        'courses',
        'modules',
        'lessons',
        'lesson_progress',
        'quiz_attempts',
        'quizzes',
        'questions',
        'options',
        'answers',
        'user_answers',
        'user_courses',
        'user_modules',
        'user_module_views',

        // ── Rapports & notes ──
        'individual_monthly_updates',
        'individual_report_quarterly_report',
        'individual_reports',
        'quarterly_report_outcomes',
        'quarterly_reports',
        'important_notes',
        'house_inspection_items',
        'house_inspections',

        // ── Contacts & appels ──
        'contacts',
        'calls',

        // ── Locations ──
        'rentals',
        'renters',
        'rents',

        // ── Référrals ──
        'referrals',
        'providers',

        // ── Réseaux sociaux internes ──
        'reactions',
        'shares',
        'mentions',
        'comments',

        // ── Contenu & catégories ──
        'content_sections',
        'categories',

        // ── Divers inutilisés ──
        'guests',
        'user_devices',
    ];

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Pas de rollback — ces tables ne sont plus nécessaires
    }
};
