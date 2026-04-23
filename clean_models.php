<?php

$modelsToDelete = [
    'Assurance.php', 'Attendance.php', 'BudgetsCategory.php', 'Candidate.php',
    'CandidateEducation.php', 'CandidateEmploymentHistory.php', 'CandidatePersonalReference.php',
    'CareHouse.php', 'CashRegister.php', 'ClientPayment.php', 'Course.php', 'CourseUserModuleProgress.php',
    'Customer.php', 'Dependent.php', 'Document.php', 'DocumentType.php', 'Expense.php', 'ExpenseSubcategory.php',
    'ExpenseType.php', 'Income.php', 'IncomeType.php', 'Invoice.php', 'InvoicePayment.php', 'JobApplication.php',
    'JobOffer.php', 'JobOfferResponse.php', 'JobOfferSchedule.php', 'Lesson.php', 'Module.php', 'Payment.php',
    'PayrollBatch.php', 'PayrollHourOverride.php', 'PayrollNote.php', 'PayrollRecord.php', 'Question.php',
    'QuizAttempt.php', 'Quizze.php', 'Rate.php', 'TimeOff.php', 'Timesheet.php', 'ToPayrollBatch.php',
    'ToPayrollRecord.php', 'Unpaid.php'
];

foreach ($modelsToDelete as $file) {
    @unlink('app/Models/' . $file);
}

$controllersToDelete = [
    'BackOffice/AdminController.php', // Wait, no! If I delete AdminController, the dashboard breaks! I'll dummy it out instead.
];

echo "Deleted old models.\n";
