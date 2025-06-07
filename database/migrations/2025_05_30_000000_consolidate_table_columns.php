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
        // Add social login fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable();
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
        });

        // Add Cambodian address fields to property_details table
        Schema::table('property_details', function (Blueprint $table) {
            if (!Schema::hasColumn('property_details', 'province')) {
                $table->string('province')->nullable();
            }
            if (!Schema::hasColumn('property_details', 'district')) {
                $table->string('district')->nullable();
            }
            if (!Schema::hasColumn('property_details', 'commune')) {
                $table->string('commune')->nullable();
            }
            if (!Schema::hasColumn('property_details', 'village')) {
                $table->string('village')->nullable();
            }
            if (!Schema::hasColumn('property_details', 'street_number')) {
                $table->string('street_number')->nullable();
            }
            if (!Schema::hasColumn('property_details', 'building_number')) {
                $table->string('building_number')->nullable();
            }
        });

        // Add all room details columns
        Schema::table('room_details', function (Blueprint $table) {
            if (!Schema::hasColumn('room_details', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('room_details', 'room_name')) {
                $table->string('room_name')->nullable();
            }
            if (!Schema::hasColumn('room_details', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('room_details', 'available')) {
                $table->boolean('available')->default(true);
            }
            if (!Schema::hasColumn('room_details', 'status')) {
                $table->string('status')->default('vacant');
            }
            if (!Schema::hasColumn('room_details', 'rent_amount')) {
                $table->decimal('rent_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('room_details', 'due_date')) {
                $table->date('due_date')->nullable();
            }
            if (!Schema::hasColumn('room_details', 'pricing_group_id')) {
                $table->unsignedBigInteger('pricing_group_id')->nullable();
            }
        });

        // Add status to rental_details table
        Schema::table('rental_details', function (Blueprint $table) {
            if (!Schema::hasColumn('rental_details', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove social login fields from users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $columns = ['provider', 'provider_id', 'avatar'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // Remove Cambodian address fields from property_details table
        if (Schema::hasTable('property_details')) {
            Schema::table('property_details', function (Blueprint $table) {
                $columns = [
                    'province',
                    'district',
                    'commune',
                    'village',
                    'street_number',
                    'building_number'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('property_details', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // Remove added columns from room_details table
        if (Schema::hasTable('room_details')) {
            Schema::table('room_details', function (Blueprint $table) {
                $columns = [
                    'type',
                    'room_name',
                    'description',
                    'available',
                    'status',
                    'rent_amount',
                    'due_date',
                    'pricing_group_id'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('room_details', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // Remove status from rental_details table
        if (Schema::hasTable('rental_details')) {
            Schema::table('rental_details', function (Blueprint $table) {
                if (Schema::hasColumn('rental_details', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
}; 