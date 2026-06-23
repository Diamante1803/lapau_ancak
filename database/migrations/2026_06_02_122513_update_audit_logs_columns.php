<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'event')) {
                $table->string('event')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('audit_logs', 'auditable_type')) {
                $table->string('auditable_type')->nullable()->after('event');
            }

            if (! Schema::hasColumn('audit_logs', 'auditable_id')) {
                $table->unsignedBigInteger('auditable_id')->nullable()->after('auditable_type');
            }

            if (! Schema::hasColumn('audit_logs', 'description')) {
                $table->text('description')->nullable()->after('auditable_id');
            }
        });

        if (Schema::hasColumn('audit_logs', 'aksi')) {
            DB::table('audit_logs')
                ->whereNull('event')
                ->update(['event' => DB::raw('aksi')]);

            DB::statement('ALTER TABLE audit_logs MODIFY aksi varchar(255) NULL');
        }

        if (Schema::hasColumn('audit_logs', 'entitas')) {
            DB::table('audit_logs')
                ->whereNull('auditable_type')
                ->update(['auditable_type' => DB::raw('entitas')]);

            DB::statement('ALTER TABLE audit_logs MODIFY entitas varchar(255) NULL');
        }

        if (Schema::hasColumn('audit_logs', 'entitas_id')) {
            DB::table('audit_logs')
                ->whereNull('auditable_id')
                ->update(['auditable_id' => DB::raw('entitas_id')]);

            DB::statement('ALTER TABLE audit_logs MODIFY entitas_id bigint(20) unsigned NULL');
        }

        if (Schema::hasColumn('audit_logs', 'deskripsi')) {
            DB::table('audit_logs')
                ->whereNull('description')
                ->update(['description' => DB::raw('deskripsi')]);

            DB::statement('ALTER TABLE audit_logs MODIFY deskripsi text NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('audit_logs', 'aksi') && Schema::hasColumn('audit_logs', 'event')) {
            DB::table('audit_logs')->whereNull('aksi')->update(['aksi' => DB::raw('event')]);
        }

        if (Schema::hasColumn('audit_logs', 'entitas') && Schema::hasColumn('audit_logs', 'auditable_type')) {
            DB::table('audit_logs')->whereNull('entitas')->update(['entitas' => DB::raw('auditable_type')]);
        }

        if (Schema::hasColumn('audit_logs', 'entitas_id') && Schema::hasColumn('audit_logs', 'auditable_id')) {
            DB::table('audit_logs')->whereNull('entitas_id')->update(['entitas_id' => DB::raw('auditable_id')]);
        }

        if (Schema::hasColumn('audit_logs', 'deskripsi') && Schema::hasColumn('audit_logs', 'description')) {
            DB::table('audit_logs')->whereNull('deskripsi')->update(['deskripsi' => DB::raw('description')]);
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('audit_logs', 'auditable_id')) {
                $table->dropColumn('auditable_id');
            }

            if (Schema::hasColumn('audit_logs', 'auditable_type')) {
                $table->dropColumn('auditable_type');
            }

            if (Schema::hasColumn('audit_logs', 'event')) {
                $table->dropColumn('event');
            }
        });
    }
};
