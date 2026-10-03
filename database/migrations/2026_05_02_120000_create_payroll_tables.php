<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Designations
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Add fields to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('branch_id');
            $table->foreignId('designation_id')->nullable()->after('department_id');
            $table->string('father_name')->nullable()->after('name');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('nid')->nullable()->after('email');
            $table->date('dob')->nullable()->after('nid');
            $table->string('image')->nullable()->after('dob');
        });

        // Salary Sheets
        Schema::create('salary_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreignId('branch_id');
            $table->integer('month');
            $table->integer('year');
            $table->integer('absent_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->decimal('overtime_hours', 10, 2)->default(0);
            $table->decimal('overtime_rate', 10, 2)->default(0);
            $table->decimal('overtime_amount', 10, 2)->default(0);
            $table->decimal('present_amount', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('deduction', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
            $table->tinyInteger('status')->default(0); // 0: Pending, 1: Paid
            $table->date('payment_date')->nullable();
            $table->foreignId('bank_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_sheets');
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['department_id', 'designation_id', 'father_name', 'mother_name', 'nid', 'dob', 'image']);
        });
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
};
