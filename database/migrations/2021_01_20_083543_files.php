<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Files extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // таблица файлов, которые надо обрабатывать построчно
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('file', 255);
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedBigInteger('lines')->nullable();
            $table->unsignedBigInteger('linesProcessed')->default(0);
            $table->enum('status', ['new','invalid','']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
