<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\User;
use App\Notifications\BirthdayNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckBirthdaysCommand extends Command
{
    protected $signature = 'app:birthdays-check:notification';

    protected $description = 'Check and send birthday notifications for customers and employees';

    public function handle()
    {
        $targetDate = Carbon::now();
        $this->info("Checking birthdays for {$targetDate->format('d/m')}");

        $customers = $this->getCustomersWithBirthday($targetDate);
        $this->info('Customers found: '.$customers->count());

        foreach ($customers as $customer) {
            $this->processCustomerBirthday($customer);
        }

        $employees = $this->getEmployeesWithBirthday($targetDate);
        $this->info('Employees found: '.$employees->count());

        foreach ($employees as $employee) {
            $this->processEmployeeBirthday($employee);
        }

        $totalBirthdays = $customers->count() + $employees->count();

        if ($totalBirthdays > 0) {
            $this->info("{$totalBirthdays} birthday(s) processed successfully");
        } else {
            $this->info('No birthdays found for today');
        }
    }

    private function getCustomersWithBirthday(Carbon $targetDate)
    {
        return Customer::whereNotNull('birthday')
            ->whereNotNull('email')
            ->whereRaw('DAY(birthday) = ?', [$targetDate->day])
            ->whereRaw('MONTH(birthday) = ?', [$targetDate->month])
            ->get();
    }

    private function getEmployeesWithBirthday(Carbon $targetDate)
    {
        return User::whereNotNull('birthday')
            ->whereNotNull('email')
            ->whereRaw('DAY(birthday) = ?', [$targetDate->day])
            ->whereRaw('MONTH(birthday) = ?', [$targetDate->month])
            ->get();
    }

    private function processCustomerBirthday(Customer $customer)
    {
        $birthdayDate = Carbon::parse($customer->birthday)->format('d/m');

        $this->line("Customer: {$customer->firstname} - {$birthdayDate}");

        try {
            $customer->notify(new BirthdayNotification($customer, 'customer'));
            $this->info("Birthday notification sent to {$customer->firstname} ({$customer->email})");
        } catch (\Exception $e) {
            $this->error("Error sending notification to {$customer->firstname}: ".$e->getMessage());
        }
    }

    private function processEmployeeBirthday(User $employee)
    {
        $birthdayDate = Carbon::parse($employee->birthday)->format('d/m');

        $this->line("Employee: {$employee->firstname} - {$birthdayDate}");

        try {
            $employee->notify(new BirthdayNotification($employee, 'employee'));
            $this->info("Birthday notification sent to {$employee->firstname} ({$employee->email})");
        } catch (\Exception $e) {
            $this->error("Error sending notification to {$employee->firstname}: ".$e->getMessage());
            Log::error('Employee birthday notification error', [
                'employee_id' => $employee->id,
                'employee_email' => $employee->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
