<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Áreas temáticas
            'view thematic areas',
            'create thematic areas',
            'edit thematic areas',
            'delete thematic areas',
            
            // Congresos
            'view congresses',
            'create congresses',
            'edit congresses',
            'delete congresses',
            'publish congresses',
            
            // Usuarios
            'view users',
            'create users',
            'edit users',
            'delete users',
            'impersonate users',
            
            // Dashboard
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'view dashboard',
            'view thematic areas',
            'create thematic areas',
            'edit thematic areas',
            'delete thematic areas',
            'view congresses',
            'create congresses',
            'edit congresses',
            'delete congresses',
            'publish congresses',
        ]);

        $reviewerRole = Role::create(['name' => 'Revisor']);
        $reviewerRole->givePermissionTo([
            'view congresses',
        ]);

        $speakerRole = Role::create(['name' => 'Ponente']);
        $speakerRole->givePermissionTo([
            'view congresses',
        ]);

        $attendeeRole = Role::create(['name' => 'Asistente']);
        $attendeeRole->givePermissionTo([
            'view congresses',
        ]);

        // Crear usuario Super Admin por defecto
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@congresos.com'],
            [
                'name' => 'Super Administrador',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('Super Admin');
    }
}
