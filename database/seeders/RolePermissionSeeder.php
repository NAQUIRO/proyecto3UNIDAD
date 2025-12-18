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
            'manage congress settings',
            
            // Papers/Ponencias
            'view papers',
            'create papers',
            'edit papers',
            'delete papers',
            'review papers',
            'assign reviewers',
            
            // Inscripciones
            'view registrations',
            'create registrations',
            'edit registrations',
            'delete registrations',
            
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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $organizerRole = Role::firstOrCreate(['name' => 'Organizador']);
        $organizerRole->syncPermissions([
            'view dashboard',
            'view thematic areas',
            'view congresses',
            'create congresses',
            'edit congresses',
            'publish congresses',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions([
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

        $reviewerRole = Role::firstOrCreate(['name' => 'Revisor']);
        $reviewerRole->syncPermissions([
            'view congresses',
        ]);

        $authorRole = Role::firstOrCreate(['name' => 'Autor']);
        $authorRole->syncPermissions([
            'view congresses',
        ]);

        $attendeeRole = Role::firstOrCreate(['name' => 'Asistente']);
        $attendeeRole->syncPermissions([
            'view congresses',
        ]);

        $speakerRole = Role::firstOrCreate(['name' => 'Ponente']);
        $speakerRole->syncPermissions([
            'view congresses',
            'create papers',
            'view papers',
            'edit papers',
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
