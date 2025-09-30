<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"crm","permissions":["view_appointment","view_any_appointment","create_appointment","update_appointment","restore_appointment","restore_any_appointment","replicate_appointment","reorder_appointment","delete_appointment","delete_any_appointment","force_delete_appointment","force_delete_any_appointment","admin_appointment","view_appointment::extra","view_any_appointment::extra","create_appointment::extra","update_appointment::extra","restore_appointment::extra","restore_any_appointment::extra","replicate_appointment::extra","reorder_appointment::extra","delete_appointment::extra","delete_any_appointment::extra","force_delete_appointment::extra","force_delete_any_appointment::extra","admin_appointment::extra","view_appointment::module","view_any_appointment::module","create_appointment::module","update_appointment::module","restore_appointment::module","restore_any_appointment::module","replicate_appointment::module","reorder_appointment::module","delete_appointment::module","delete_any_appointment::module","force_delete_appointment::module","force_delete_any_appointment::module","admin_appointment::module","view_branch","view_any_branch","create_branch","update_branch","restore_branch","restore_any_branch","replicate_branch","reorder_branch","delete_branch","delete_any_branch","force_delete_branch","force_delete_any_branch","admin_branch","view_category","view_any_category","create_category","update_category","restore_category","restore_any_category","replicate_category","reorder_category","delete_category","delete_any_category","force_delete_category","force_delete_any_category","admin_category","view_customer","view_any_customer","create_customer","update_customer","restore_customer","restore_any_customer","replicate_customer","reorder_customer","delete_customer","delete_any_customer","force_delete_customer","force_delete_any_customer","admin_customer","view_discount::template","view_any_discount::template","create_discount::template","update_discount::template","restore_discount::template","restore_any_discount::template","replicate_discount::template","reorder_discount::template","delete_discount::template","delete_any_discount::template","force_delete_discount::template","force_delete_any_discount::template","admin_discount::template","view_module::setting","view_any_module::setting","create_module::setting","update_module::setting","restore_module::setting","restore_any_module::setting","replicate_module::setting","reorder_module::setting","delete_module::setting","delete_any_module::setting","force_delete_module::setting","force_delete_any_module::setting","admin_module::setting","view_notification::template","view_any_notification::template","create_notification::template","update_notification::template","restore_notification::template","restore_any_notification::template","replicate_notification::template","reorder_notification::template","delete_notification::template","delete_any_notification::template","force_delete_notification::template","force_delete_any_notification::template","admin_notification::template","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_room","view_any_room","create_room","update_room","restore_room","restore_any_room","replicate_room","reorder_room","delete_room","delete_any_room","force_delete_room","force_delete_any_room","admin_room","view_service","view_any_service","create_service","update_service","restore_service","restore_any_service","replicate_service","reorder_service","delete_service","delete_any_service","force_delete_service","force_delete_any_service","admin_service","view_service::package","view_any_service::package","create_service::package","update_service::package","restore_service::package","restore_any_service::package","replicate_service::package","reorder_service::package","delete_service::package","delete_any_service::package","force_delete_service::package","force_delete_any_service::package","admin_service::package","view_todo","view_any_todo","create_todo","update_todo","restore_todo","restore_any_todo","replicate_todo","reorder_todo","delete_todo","delete_any_todo","force_delete_todo","force_delete_any_todo","admin_todo","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","admin_user","view_work::time","view_any_work::time","create_work::time","update_work::time","restore_work::time","restore_any_work::time","replicate_work::time","reorder_work::time","delete_work::time","delete_any_work::time","force_delete_work::time","force_delete_any_work::time","admin_work::time","view_work::time::group","view_any_work::time::group","create_work::time::group","update_work::time::group","restore_work::time::group","restore_any_work::time::group","replicate_work::time::group","reorder_work::time::group","delete_work::time::group","delete_any_work::time::group","force_delete_work::time::group","force_delete_any_work::time::group","admin_work::time::group","page_AdminSettings","page_GeneralSettings","page_ManageFrontend","page_ManageNotifications","page_Notification","page_Settings","page_ActivityLog","page_OnlineAppointments","widget_AppointmentCalendarWidget"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
