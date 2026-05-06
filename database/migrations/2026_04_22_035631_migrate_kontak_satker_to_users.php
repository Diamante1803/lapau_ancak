<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Satker;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $satkers = Satker::with('users')->get();

        foreach ($satkers as $satker) {
            foreach ($satker->users as $user) {

                // hanya isi kalau user belum punya kontak
                if (!$user->kontak) {
                    $user->update([
                        'kontak' => $satker->kontak
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
