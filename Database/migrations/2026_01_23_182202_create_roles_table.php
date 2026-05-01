<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // student, owner, broker
            $table->string('name_ar')->nullable();
            $table->timestamps();
        });

        // Seed default roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'student', 'name_ar' => 'طالب', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'owner', 'name_ar' => 'مالك', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'broker', 'name_ar' => 'سمسار', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
