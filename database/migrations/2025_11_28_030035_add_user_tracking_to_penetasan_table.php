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
        $tableName = $this->resolvePenetasanTable();
        $userTable = $this->resolvePenggunaTable();

        if (!$tableName || !$userTable) {
            return;
        }

        $self = $this;

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $userTable, $self) {
            if (!Schema::hasColumn($tableName, 'updated_by')) {
                $column = $table->foreignId('updated_by')->nullable();

                if (Schema::hasColumn($tableName, 'created_by')) {
                    $column->after('created_by');
                }
            } elseif ($self->hasForeignKey($tableName, 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }

            if (Schema::hasColumn($tableName, 'updated_by')) {
                $table->foreign('updated_by')
                    ->references('id')
                    ->on($userTable)
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn($tableName, 'created_by')) {
                return;
            }

            if ($self->hasForeignKey($tableName, 'created_by')) {
                $table->dropForeign(['created_by']);
            }

            $table->foreign('created_by')
                ->references('id')
                ->on($userTable)
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = $this->resolvePenetasanTable();

        if (!$tableName) {
            return;
        }

        $self = $this;

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $self) {
            if (Schema::hasColumn($tableName, 'updated_by')) {
                if ($self->hasForeignKey($tableName, 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                }

                $table->dropColumn('updated_by');
            }

            if (Schema::hasColumn($tableName, 'created_by') && $self->hasForeignKey($tableName, 'created_by')) {
                $table->dropForeign(['created_by']);
            }
        });
    }

    private function resolvePenetasanTable(): ?string
    {
        if (Schema::hasTable('vf_penetasan')) {
            return 'vf_penetasan';
        }

        if (Schema::hasTable('penetasan')) {
            return 'penetasan';
        }

        return null;
    }

    private function resolvePenggunaTable(): ?string
    {
        if (Schema::hasTable('vf_pengguna')) {
            return 'vf_pengguna';
        }

        if (Schema::hasTable('pengguna')) {
            return 'pengguna';
        }

        return null;
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        $tableName = $connection->getTablePrefix() . $table;

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();
    }
};
