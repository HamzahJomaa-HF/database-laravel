<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pre_activities', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->json('support'); // Multiple selection
            $table->string('focal_point_username');
            
            // Main Data Group
            $table->string('project');
            $table->text('partners')->nullable();
            $table->string('location');
            $table->string('venue_availability')->nullable();
            $table->date('activity_date')->nullable();
            $table->time('activity_time')->nullable();
            $table->dateTime('activity_date_time')->nullable();
            $table->integer('sessions_number')->nullable();
            $table->string('activity_type')->nullable();
            $table->integer('participants_number')->nullable();
            
            // Data, Monitoring & Evaluation Group
            $table->json('invitations')->nullable();
            $table->json('attendance_list')->nullable();
            $table->json('mom_list')->nullable();
            $table->string('registration_form')->nullable();
            $table->string('post_evaluation')->nullable();
            $table->json('certificate_type')->nullable();
            
            // Logistics & Procurement Group
            $table->json('sound_system')->nullable();
            $table->string('zoom_type')->nullable();
            $table->json('setup')->nullable();
            $table->string('setup_type')->nullable();
            $table->json('screen_type')->nullable();
            $table->json('hardware')->nullable();
            $table->json('wifi')->nullable();
            $table->string('rollups_needed')->nullable();
            $table->string('flags_needed')->nullable();
            $table->string('podium')->nullable();
            $table->json('food')->nullable();
            $table->string('note_papers')->nullable();
            $table->text('staff_names')->nullable();
            $table->integer('volunteer_numbers')->nullable();
            $table->text('support_areas')->nullable();
            
            // Communications Group
            $table->json('coverage')->nullable();
            $table->json('media')->nullable();
            $table->string('video_interview')->nullable();
            $table->text('other_communications')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pre_activities');
    }
};