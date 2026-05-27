<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. "QST_0001"
            $table->foreignId('question_type_id')->constrained();
            $table->foreignId('primary_skill_type_id')->constrained();
            $table->foreignId('sub_skill_type_id')->constrained();

        // Question content stored as structured JSON per type (Multi-language supported)
        $table->json('question_contents');

        /*
        * question_contents JSON schema by type:
        *
        * MCQ / Multi-Select:
        * {
        * "stem": {"en": "What is the capital of India?", "as": "ভাৰতৰ ৰাজধানী কি?", "bn": "ভারতের রাজধানী কি?"},
        * "media": {"type": "image|video|audio", "path": "uploads/media/delhi.png"},
        * "options": [
        * {
        * "id": "A",
        * "text": {"en": "New Delhi", "as": "নতুন দিল্লী", "bn": "নয়াদিল্লি"},
        * "media": null,
        * "is_correct": true
        * },
        * {
        * "id": "B",
        * "text": {"en": "Mumbai", "as": "মুম্বাই", "bn": "মুম্বই"},
        * "media": null,
        * "is_correct": false
        * }
        * ],
        * "explanation": {"en": "New Delhi is the capital.", "as": "নতুন দিল্লী হৈছে ৰাজধানী।", "bn": "নয়াদিল্লি হলো রাজধানী।"}
        * }
        *
        * Numerical Answer:
        * {
        * "stem": {"en": "What is 5 + 5?", "as": "৫ + ৫ কিমান হয়?", "bn": "৫ + ৫ কত হয়?"},
        * "media": null,
        * "correct_value": 10,
        * "tolerance": 0.0,
        * "unit": {"en": "units", "as": "একক", "bn": "একক"},
        * "explanation": {"en": "Simple addition.", "as": "সাধাৰণ যোগফল।", "bn": "সাধারণ যোগফল।"}
        * }
        *
        * Sequencing/Ordering:
        * {
        * "stem": {"en": "Arrange by size (smallest to largest):", "as": "আকাৰ অনুসৰি সজোৱা (সৰুৰ পৰা ডাঙৰলৈ):", "bn": "আকার অনুযায়ী সাজাও (ছোট থেকে বড়):"},
        * "items": [
        * {"id": 1, "text": {"en": "Ant", "as": "পৰুৱা", "bn": "পিপড়ে"}, "correct_position": 1},
        * {"id": 2, "text": {"en": "Elephant", "as": "হাতী", "bn": "হাতি"}, "correct_position": 2}
        * ],
        * "explanation": {"en": "Ants are smaller than elephants.", "as": "পৰুৱা হাতীতকৈ সৰু।", "bn": "পিপড়ে হাতির চেয়ে ছোট।"}
        * }
        *
        * Match the Following:
        * {
        * "stem": {"en": "Match animals to their sounds:", "as": "জীৱ-জন্তুসমূহক সিহঁতৰ মাতৰ সৈতে মিলোৱা:", "bn": "পশুপাখিদের তাদের ডাকের সাথে মেলাও:"},
        * "left_items":  [{"id": "L1", "text": {"en": "Dog", "as": "কুকুৰ", "bn": "কুকুর"}}, ...],
        * "right_items": [{"id": "R1", "text": {"en": "Bark", "as": "ভুকভুকোৱা", "bn": "ঘেউ ঘেউ"}}, ...],
        * "correct_pairs": [{"left": "L1", "right": "R1"}],
        * "explanation": {"en": "Dogs bark.", "as": "কুকুৰে ভুকে।", "bn": "কুকুর ঘেউ ঘেউ করে।"}
        * }
        *
        * Pattern Series Completion:
        * {
        * "stem": {"en": "What comes next in this visual pattern?", "as": "এই দৃশ্যমান আৰ্হিত ইয়াৰ পিছত কি আহিব?", "bn": "এই ভিজ্যুয়াল প্যাটার্নে এর পরে কী আসবে?"},
        * "series": ["img1.png", "img2.png", "img3.png", "?"],
        * "options": [{"id": "A", "path": "opt1.png", "is_correct": true}],
        * "explanation": {"en": "The pattern rotates 90 degrees.", "as": "আৰ্হিটো ৯০ ডিগ্ৰী ঘূৰি যায়।", "bn": "প্যাটার্নটি ৯০ ডিগ্রি ঘোরে।"}
        * }
        *
        * Visual/Image Based Reasoning:
        * {
        * "stem": {"en": "Identify this freedom fighter:", "as": "এই স্বাধীনতা সংগ্ৰামীগৰাকীক চিনাক্ত কৰা:", "bn": "এই স্বাধীনতা সংগ্রামীকে সনাক্ত করো:"},
        * "media": {"type": "image", "path": "uploads/images/fighter.png"},
        * "options": [...same structure as localized MCQ...],
        * "explanation": {"en": "...", "as": "...", "bn": "..."}
        * }
        *
        * Memory Recall:
        * {
        * "stem": {"en": "Remember these items:", "as": "এই বস্তুবোৰ মনত ৰাখা:", "bn": "এই জিনিসগুলো মনে রাখো:"},
        * "memory_phase": {
        * "items": [
        * {"en": "Red Apple", "as": "ৰঙা আপেল", "bn": "লাল আপেল"},
        * {"en": "Blue Pen", "as": "নীলা কলম", "bn": "নীল কলম"}
        * ],
        * "display_seconds": 10
        * },
        * "recall_type": "list",
        * "options": [...same structure as localized MCQ...],
        * "explanation": {"en": "...", "as": "...", "bn": "..."}
        * }
        *
        * Situation Judgement:
        * {
        * "scenario": {"en": "Your teammate misses a deadline...", "as": "আপোনাৰ সতীৰ্থই সময়সীমা পাৰ কৰিলে...", "bn": "আপনার সতীর্থ একটি সময়সীমা মিস করেছে..."},
        * "media": null,
        * "options": [
        * {"id": "A", "text": {"en": "Talk to them", "as": "তেওঁলোকৰ সৈতে কথা পাতক", "bn": "তাদের সাথে কথা বলুন"}, "score_value": 4},
        * {"id": "B", "text": {"en": "Ignore it", "as": "উপেক্ষা কৰক", "bn": "উপেক্ষা করুন"}, "score_value": 1}
        * ]
        * }
        *
        * Likert Scale:
        * {
        * "stem": {"en": "I find it easy to learn programming.", "as": "মই প্ৰগ্ৰেমিং শিকাটো সহজ পাওঁ।", "bn": "আমি প্রোগ্রামিং শেখা সহজ মনে করি।"},
        * "scale": 5,
        * "labels": {
        * "1": {"en": "Strongly Disagree", "as": "দৃঢ়ভাৱে সন্মত নহয়", "bn": "দৃঢ়ভাবে অসম্মত"},
        * "5": {"en": "Strongly Agree", "as": "দৃঢ়ভাৱে সন্মত", "bn": "দৃঢ়ভাবে সম্মত"}
        * },
        * "reverse_scored": false
        * }
        *
        * Open Text Response:
        * {
        * "stem": {"en": "Describe your greatest achievement.", "as": "আপোনাৰ আটাইতকৈ ডাঙৰ সাফল্যৰ বৰ্ণনা কৰা।", "bn": "আপনার সবচেয়ে বড় কৃতিত্বের বর্ণনা দিন।"},
        * "max_words": 200,
        * "rubric": {"en": "Look for leadership traits.", "as": "নেতৃত্বৰ গুণাগুণ পৰীক্ষা কৰক।", "bn": "নেতৃত্বের গুণাবলী লক্ষ্য করুন।"}
        * }
        *
        * Ranking Preference:
        * {
        * "stem": {"en": "Rank by importance:", "as": "গুৰুত্ব অনুসৰি স্থান দিয়া:", "bn": "গুরুত্ব অনুযায়ী র‍্যাঙ্ক করো:"},
        * "items": [
        * {"id": 1, "text": {"en": "Salary", "as": "দৰমহা", "bn": "বেতন"}},
        * {"id": 2, "text": {"en": "Work Life Balance", "as": "কৰ্ম-জীৱনৰ ভাৰসাম্য", "bn": "কর্ম ও জীবনের ভারসাম্য"}}
        * ],
        * "ideal_order": [2, 1]
        * }
        *
        * Yes/No Behavioural:
        * {
        * "stem": {"en": "Have you ever led a team?", "as": "আপুনি কেতিয়াবা দল পৰিচালনা কৰিছে?", "bn": "আপনি কি কখনো দল পরিচালনা করেছেন?"},
        * "options": [
        * {"id": "yes", "text": {"en": "Yes", "as": "হয়", "bn": "হ্যাঁ"}},
        * {"id": "no", "text": {"en": "No", "as": "নহয়", "bn": "না"}}
        * ],
        * "scored_option": "yes",
        * "score_value": 1
        * }
        *
        * Case Study Analysis:
        * {
        * "case_text": {"en": "Company X faced a crisis...", "as": "কোম্পানী X এটা সংকটৰ মুখামুখি হৈছিল...", "bn": "কোম্পানি X একটি সংকটের মুখোমুখি হয়েছিল..."},
        * "media": null,
        * "sub_questions": [
        * {
        * "stem": {"en": "What was the main cause?", "as": "প্ৰধান কাৰণ কি আছিল?", "bn": "প্রধান কারণ কী ছিল?"},
        * "question_type": "mcq",
        * "options": [...],
        * "explanation": {"en": "...", "as": "...", "bn": "..."}
        * }
        * ]
        * }
        *
        * Rapid Fire:
        * {
        * "stem": {"en": "Name a fruit starting with A", "as": "A দি আৰম্ভ হোৱা এটা ফলৰ নাম লিখা", "bn": "A দিয়ে শুরু হওয়া একটি ফলের নাম লেখো"},
        * "time_limit_seconds": 5,
        * "expected_answers": {
        * "en": ["Apple", "Apricot"],
        * "as": ["আপেল"],
        * "bn": ["আপেল"]
        * },
        * "case_insensitive": true
        * }
        */

            $table->unsignedSmallInteger('time_limit')->nullable();  // seconds; null = use set-level timer
            $table->decimal('max_score', 6, 2)->default(1.00);       // per-question max score
            $table->text('admin_notes')->nullable();                  // internal notes for content team
            $table->unsignedInteger('usage_count')->default(0);      // how many sets use this question
            $table->enum('status', ['draft', 'publish', 'unpublish'])->default('draft');
            $table->foreignId('created_by')->constrained('admins')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
