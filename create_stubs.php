<?php
// Stub models needed by routes/controllers that reference deleted models
// These are minimal stubs to prevent autoloader errors

$stubs = [
    'ExpenseSubcategory' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass ExpenseSubcategory extends Model { protected \$fillable = ['name']; }",
    'Expense' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Expense extends Model { protected \$fillable = ['amount','description']; }",
    'ExpenseType' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass ExpenseType extends Model { protected \$fillable = ['name']; }",
    'Income' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Income extends Model { protected \$fillable = ['amount_paid']; public function type() { return \$this->belongsTo(IncomeType::class); } }",
    'IncomeType' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass IncomeType extends Model { protected \$fillable = ['name']; }",
    'Payment' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Payment extends Model { protected \$fillable = ['total','amount_due','is_insurance_payment']; }",
    'Assurance' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Assurance extends Model { protected \$fillable = ['active']; }",
    'Customer' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Customer extends Model { protected \$fillable = ['type','name']; }",
    'JobOffer' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass JobOffer extends Model { protected \$fillable = ['title','description','status','reference','uuid']; }",
    'JobApplication' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass JobApplication extends Model { protected \$fillable = ['status']; }",
    'Candidate' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Candidate extends Model { protected \$fillable = ['name']; }",
    'Document' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Document extends Model { protected \$fillable = ['name']; public function scopeValid(\$q) { return \$q; } public function scopeExpiringSoon(\$q) { return \$q->whereRaw('1=0'); } public function scopeExpired(\$q) { return \$q->whereRaw('1=0'); } }",
    'DocumentType' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass DocumentType extends Model { protected \$fillable = ['name']; }",
    'Attendance' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Attendance extends Model { protected \$fillable = ['user_id','check_in_time','check_out_time']; public function user() { return \$this->belongsTo(User::class); } public function schedule() { return \$this->belongsTo(Schedule::class); } }",
    'CareHouse' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass CareHouse extends Model { protected \$fillable = ['name','latitude','longitude']; }",
    'Course' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Course extends Model { protected \$fillable = ['title','has_certification']; public function modules() { return \$this->hasMany(Module::class); } public function quizzes() { return \$this->hasMany(Quizze::class); } }",
    'Module' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Module extends Model { protected \$fillable = ['title']; public function lessons() { return \$this->hasMany(Lesson::class); } }",
    'Lesson' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Lesson extends Model { protected \$fillable = ['title']; }",
    'Quizze' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Quizze extends Model { protected \$fillable = ['title']; }",
    'QuizAttempt' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass QuizAttempt extends Model { protected \$fillable = ['score','is_completed']; public function quizze() { return \$this->belongsTo(Quizze::class,'quiz_id'); } }",
    'Rate' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Rate extends Model { protected \$fillable = ['name']; }",
    'PayrollRecord' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass PayrollRecord extends Model { protected \$fillable = ['user_id']; }",
    'PayrollBatch' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass PayrollBatch extends Model { protected \$fillable = ['name']; }",
    'PayrollNote' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass PayrollNote extends Model { protected \$fillable = ['note']; }",
    'ToPayrollBatch' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass ToPayrollBatch extends Model { protected \$fillable = ['name']; }",
    'ToPayrollRecord' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass ToPayrollRecord extends Model { protected \$fillable = ['user_id']; }",
    'PayrollHourOverride' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass PayrollHourOverride extends Model { protected \$fillable = ['hours']; }",
    'TimeOff' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass TimeOff extends Model { protected \$fillable = ['user_id']; }",
    'Timesheet' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Timesheet extends Model { protected \$fillable = ['user_id']; }",
    'Unpaid' => "<?php\nnamespace App\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass Unpaid extends Model { protected \$fillable = ['amount']; }",
];

foreach ($stubs as $name => $content) {
    $path = "app/Models/{$name}.php";
    if (!file_exists($path)) {
        file_put_contents($path, $content);
        echo "Created {$path}\n";
    } else {
        echo "Skipped {$path} (exists)\n";
    }
}

echo "Done!\n";
