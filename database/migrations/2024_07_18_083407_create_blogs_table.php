<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->string("title");
            $table->text("description");
            $table->string("image")->nullable();
            $table->foreignUlid("user_id")->constrained("users");
            $table->foreignUlid("category_id")->constrained("categories");
            $table->boolean("status")->default(true);
            $table->boolean("view_count")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};