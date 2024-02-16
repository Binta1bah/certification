<?php

use App\Models\Categorie;
use App\Models\Localite;
use App\Models\User;
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
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->text('description');
            $table->enum('etat', ['Comme Neuf', 'Bon Etat', 'Etat Moyen', 'A Bricoler']);
            $table->enum('type', ['Don', 'Echange']);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Categorie::class)->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Localite::class)->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('statut')->default(1);
            $table->date('date_limite');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};
