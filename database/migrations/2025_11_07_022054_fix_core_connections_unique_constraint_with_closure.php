<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Get ALL foreign key names for core_connections table
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'core_connections'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        // Step 2: Drop ALL foreign keys temporarily
        Schema::table('core_connections', function (Blueprint $table) use ($foreignKeys) {
            foreach ($foreignKeys as $fk) {
                try {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                    echo "Dropped FK: {$fk->CONSTRAINT_NAME}\n";
                } catch (\Exception $e) {
                    echo "Could not drop FK {$fk->CONSTRAINT_NAME}: {$e->getMessage()}\n";
                }
            }
        });

        // Step 3: Drop old unique constraint
        try {
            DB::statement("ALTER TABLE core_connections DROP INDEX core_connections_core_a_id_core_b_id_unique");
            echo "Dropped unique constraint\n";
        } catch (\Exception $e) {
            echo "Could not drop unique constraint: {$e->getMessage()}\n";
        }

        // Step 4: Add new unique constraint (includes closure_id)
        Schema::table('core_connections', function (Blueprint $table) {
            $table->unique(
                ['core_a_id', 'core_b_id', 'closure_id'],
                'unique_connection_per_closure'
            );
        });
        echo "Added new unique constraint\n";

        // Step 5: Check which foreign keys still exist
        $existingFKs = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'core_connections'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        $existingFKNames = array_column($existingFKs, 'CONSTRAINT_NAME');

        // Step 6: Recreate foreign keys only if they don't exist
        Schema::table('core_connections', function (Blueprint $table) use ($existingFKNames) {
            if (!in_array('core_connections_core_a_id_foreign', $existingFKNames)) {
                $table->foreign('core_a_id', 'core_connections_core_a_id_foreign')
                      ->references('id')
                      ->on('fiber_cores')
                      ->onDelete('cascade');
                echo "Created FK: core_connections_core_a_id_foreign\n";
            }

            if (!in_array('core_connections_core_b_id_foreign', $existingFKNames)) {
                $table->foreign('core_b_id', 'core_connections_core_b_id_foreign')
                      ->references('id')
                      ->on('fiber_cores')
                      ->onDelete('cascade');
                echo "Created FK: core_connections_core_b_id_foreign\n";
            }

            if (!in_array('core_connections_closure_id_foreign', $existingFKNames)) {
                $table->foreign('closure_id', 'core_connections_closure_id_foreign')
                      ->references('id')
                      ->on('joint_closures')
                      ->onDelete('cascade');
                echo "Created FK: core_connections_closure_id_foreign\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop ALL foreign keys
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'core_connections'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        Schema::table('core_connections', function (Blueprint $table) use ($foreignKeys) {
            foreach ($foreignKeys as $fk) {
                try {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                } catch (\Exception $e) {
                    // Ignore errors
                }
            }
        });

        // Step 2: Drop new constraint
        Schema::table('core_connections', function (Blueprint $table) {
            try {
                $table->dropUnique('unique_connection_per_closure');
            } catch (\Exception $e) {
                // Ignore errors
            }
        });

        // Step 3: Restore old constraint
        try {
            Schema::table('core_connections', function (Blueprint $table) {
                $table->unique(['core_a_id', 'core_b_id']);
            });
        } catch (\Exception $e) {
            // May fail if duplicate data exists
        }

        // Step 4: Recreate foreign keys
        Schema::table('core_connections', function (Blueprint $table) {
            try {
                $table->foreign('core_a_id')
                      ->references('id')
                      ->on('fiber_cores')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}

            try {
                $table->foreign('core_b_id')
                      ->references('id')
                      ->on('fiber_cores')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}

            try {
                $table->foreign('closure_id')
                      ->references('id')
                      ->on('joint_closures')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}
        });
    }
};
