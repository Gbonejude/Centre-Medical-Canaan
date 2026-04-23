<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateEducation>
 */
class CandidateEducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $educationLevel = $this->faker->randomElement(['high_school', 'college', 'university', 'certification']);

        return [
            'candidate_id' => Candidate::factory(),
            'uuid' => Str::uuid(),
            'institution_name' => $this->getInstitutionName($educationLevel),
            'institution_location' => $this->faker->city().', '.$this->faker->stateAbbr().', USA',
            'education_level' => $educationLevel,
            'degree_type' => $this->getDegreeType($educationLevel),
            'field_of_study' => $this->getFieldOfStudy($educationLevel),
            'start_date' => $this->faker->dateTimeBetween('-10 years', '-4 years')->format('Y-m-d'),
            'end_date' => $this->faker->optional(0.8)->dateTimeBetween('-4 years', 'now')?->format('Y-m-d'),
            'graduated' => $this->faker->boolean(75),
            'gpa' => $this->faker->optional(0.7)->randomFloat(2, 2.0, 4.0),
            'order' => $this->faker->numberBetween(1, 5),
        ];
    }

    /**
     * State for high school education
     */
    public function highSchool(): static
    {
        return $this->state(fn (array $attributes) => [
            'education_level' => 'high_school',
            'institution_name' => $this->faker->randomElement([
                'Lincoln High School',
                'Washington High School',
                'Jefferson High School',
                'Roosevelt High School',
                'Kennedy High School',
                'Central High School',
                'North High School',
                'Westfield High School',
            ]),
            'degree_type' => 'High School Diploma',
            'field_of_study' => 'General Studies',
            'start_date' => $this->faker->dateTimeBetween('-8 years', '-4 years')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('-4 years', '-1 year')->format('Y-m-d'),
            'order' => 1,
        ]);
    }

    /**
     * State for university education
     */
    public function university(): static
    {
        return $this->state(fn (array $attributes) => [
            'education_level' => 'university',
            'institution_name' => $this->faker->randomElement([
                'University of California',
                'Stanford University',
                'Harvard University',
                'MIT',
                'Yale University',
                'Princeton University',
                'University of Michigan',
                'Northwestern University',
            ]),
            'degree_type' => $this->faker->randomElement([
                'Bachelor of Arts (BA)',
                'Bachelor of Science (BS)',
                'Master of Arts (MA)',
                'Master of Science (MS)',
                'Master of Business Administration (MBA)',
            ]),
            'field_of_study' => $this->faker->randomElement([
                'Computer Science',
                'Business Administration',
                'Engineering',
                'Psychology',
                'Biology',
                'Economics',
                'Marketing',
            ]),
            'order' => 2,
        ]);
    }

    /**
     * State for certification
     */
    public function certification(): static
    {
        return $this->state(fn (array $attributes) => [
            'education_level' => 'certification',
            'institution_name' => $this->faker->randomElement([
                'Google',
                'Microsoft',
                'Amazon Web Services',
                'Cisco',
                'Oracle',
                'Salesforce',
                'CompTIA',
            ]),
            'institution_location' => 'Online',
            'degree_type' => 'Professional Certificate',
            'field_of_study' => $this->faker->randomElement([
                'Google Analytics Certified',
                'Microsoft Azure Fundamentals',
                'AWS Cloud Practitioner',
                'Cisco CCNA',
                'Project Managment Professional (PMP)',
                'Digital Marketing Certificate',
            ]),
            'start_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'graduated' => true,
            'gpa' => null,
            'order' => 3,
        ]);
    }

    private function getInstitutionName(string $level): string
    {
        return match ($level) {
            'high_school' => $this->faker->randomElement([
                'Lincoln High School', 'Washington High School', 'Jefferson High School',
                'Roosevelt High School', 'Kennedy High School', 'Central High School',
            ]),
            'university', 'college' => $this->faker->randomElement([
                'University of California', 'Stanford University', 'Harvard University',
                'MIT', 'Yale University', 'Northwestern University',
            ]),

            'certification' => $this->faker->randomElement([
                'Google', 'Microsoft', 'Amazon Web Services', 'Cisco', 'Oracle',
            ]),
            default => $this->faker->company().' Institute'
        };
    }

    private function getDegreeType(string $level): string
    {
        return match ($level) {
            'high_school' => 'High School Diploma',
            'college' => $this->faker->randomElement([
                'Associate of Arts (AA)', 'Associate of Science (AS)',
                'Associate of Applied Science (AAS)',
            ]),
            'university' => $this->faker->randomElement([
                'Bachelor of Arts (BA)', 'Bachelor of Science (BS)',
                'Master of Arts (MA)', 'Master of Science (MS)',
                'Master of Business Administration (MBA)', 'Doctor of Philosophy (PhD)',
            ]),

            'certification' => 'Professional Certificate',
            default => 'Certificate'
        };
    }

    private function getFieldOfStudy(string $level): string
    {
        return match ($level) {
            'high_school' => 'General Studies',
            default => $this->faker->randomElement([
                'Computer Science', 'Business Administration', 'Engineering',
                'Psychology', 'Biology', 'Chemistry', 'Mathematics',
                'English Literature', 'Economics', 'Marketing', 'Finance',
            ])
        };
    }
}
