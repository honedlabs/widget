<?php

use Honed\Widget\Migrations\WidgetMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends WidgetMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string('group')->nullable();
            $table->string('name');
            $table->string('scope');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['group', 'name', 'scope']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->getTable());
    }
};
