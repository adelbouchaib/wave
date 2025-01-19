<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('library_id')->unique();
            $table->string('collation_id')->unique();
            $table->integer('collation_count');


            $table->string('page_id');
            $table->string('page_name');
            $table->string('page_url');
            $table->text('page_picture');


            $table->text('copy');
            $table->string('description');
            $table->string('headline');
            $table->string('cta');
            $table->text('url');
            
            $table->string('creative_type');
            $table->text('creative_url');

            $table->integer('starting_date');
            $table->text('count')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ads');
    }
}

?>