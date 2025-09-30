<?php

namespace Database\Seeders;

use App\Models\ServicePackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

//use App\Models\ServiceServicePackage;

class ServicePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServicePackage::upsert([
            [
                'id' => '1',
                'name' => 'Damen Paket 1',
                'short_code' => 'DP1',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:50',
                'updated_at' => '2021-05-20 15:54:30',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '2',
                'name' => 'Damen Paket 2',
                'short_code' => 'DP2',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:50',
                'updated_at' => '2023-04-06 18:02:54',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '3',
                'name' => 'Damen Paket 3',
                'short_code' => 'DP3',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:50',
                'updated_at' => '2022-09-06 16:21:29',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '4',
                'name' => 'Damen Paket 4',
                'short_code' => 'DP4',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2024-04-03 16:29:50',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '5',
                'name' => 'Damen Paket 5',
                'short_code' => 'DP5',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2022-11-21 14:07:12',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '6',
                'name' => 'Damen Paket 6A',
                'short_code' => 'DP6A',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2024-04-03 16:30:13',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '7',
                'name' => 'Damen Paket 7',
                'short_code' => 'DP7',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2024-04-03 16:30:29',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '8',
                'name' => 'Damen Paket 8',
                'short_code' => 'DP8',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2021-05-20 15:55:25',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '9',
                'name' => 'Damen Paket 9',
                'short_code' => 'DP9',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2021-05-20 15:55:31',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '10',
                'name' => 'Damen Paket 10',
                'short_code' => 'DP10',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2024-04-03 16:30:46',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '11',
                'name' => 'Damen Paket 11',
                'short_code' => 'DP11',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2024-04-03 16:31:01',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '12',
                'name' => 'Herren Paket 1',
                'short_code' => 'HP1',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:51',
                'updated_at' => '2021-05-20 15:55:49',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '13',
                'name' => 'Herren Paket 2',
                'short_code' => 'HP2',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:52',
                'updated_at' => '2021-05-20 15:55:57',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '14',
                'name' => 'Herren Paket 3',
                'short_code' => 'HP3',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:52',
                'updated_at' => '2021-05-20 15:56:04',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '15',
                'name' => 'Herren Paket 4',
                'short_code' => 'HP4',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:52',
                'updated_at' => '2021-05-20 15:56:12',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '16',
                'name' => 'Herren Paket 5',
                'short_code' => 'HP5',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:52',
                'updated_at' => '2021-05-20 15:56:21',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '17',
                'name' => 'Herren Paket 6',
                'short_code' => 'HP6',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:52',
                'updated_at' => '2021-05-20 15:56:27',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '18',
                'name' => 'Herren Paket 7',
                'short_code' => 'HP7',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:52',
                'updated_at' => '2022-10-17 09:18:03',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '19',
                'name' => 'Herren Paket 8',
                'short_code' => 'HP8',
                'category_id' => 1,
                'created_at' => '2021-03-08 11:52:52',
                'updated_at' => '2023-10-11 16:31:38',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '21',
                'name' => 'Damen Paket 6B ',
                'short_code' => 'DP6B',
                'category_id' => 1,
                'created_at' => '2023-01-25 13:38:37',
                'updated_at' => '2023-01-25 13:40:40',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '28',
                'name' => 'Herren Paket 10',
                'short_code' => 'HP10',
                'category_id' => 1,
                'created_at' => '2023-04-03 14:09:34',
                'updated_at' => '2023-10-11 16:31:53',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '29',
                'name' => 'Damen Paket 13',
                'short_code' => 'DP13',
                'category_id' => 1,
                'created_at' => '2023-04-03 14:13:16',
                'updated_at' => '2023-10-11 16:32:21',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '30',
                'name' => 'Damen Paket 12',
                'short_code' => 'DP12',
                'category_id' => 1,
                'created_at' => '2023-04-03 14:21:21',
                'updated_at' => '2023-04-05 15:02:32',
                'deleted_at' => null,
                'gender' => 'female',
            ],
            [
                'id' => '31',
                'name' => 'Herren Paket  9',
                'short_code' => 'HP9',
                'category_id' => 1,
                'created_at' => '2023-04-03 14:25:41',
                'updated_at' => '2023-04-05 15:02:55',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '36',
                'name' => 'Beine Komplett BK',
                'short_code' => 'BK',
                'category_id' => 1,
                'created_at' => '2023-04-24 12:05:38',
                'updated_at' => '2023-04-24 12:05:38',
                'deleted_at' => null,
                'gender' => 'non-binary',
            ],
            [
                'id' => '55',
                'name' => 'Gesäß inkl. Pofalte',
                'short_code' => 'GEPOF',
                'category_id' => 1,
                'created_at' => '2023-06-15 17:07:42',
                'updated_at' => '2023-06-15 17:07:42',
                'deleted_at' => null,
                'gender' => 'non-binary',
            ],
            [
                'id' => '73',
                'name' => 'OL/KI Oberlippe Kinn',
                'short_code' => 'OLKI',
                'category_id' => 1,
                'created_at' => '2023-07-05 15:52:09',
                'updated_at' => '2023-12-19 12:54:36',
                'deleted_at' => null,
                'gender' => 'non-binary',
            ],
            [
                'id' => '171',
                'name' => 'Ges/Hals',
                'short_code' => 'GESH',
                'category_id' => 1,
                'created_at' => '2023-09-01 13:53:16',
                'updated_at' => '2023-09-01 13:53:16',
                'deleted_at' => null,
                'gender' => 'non-binary',
            ],
            [
                'id' => '520',
                'name' => 'Herren Paket  11',
                'short_code' => 'HP11',
                'category_id' => 1,
                'created_at' => '2024-01-19 19:41:22',
                'updated_at' => '2024-01-19 19:41:22',
                'deleted_at' => null,
                'gender' => 'male',
            ],
            [
                'id' => '685',
                'name' => 'Damen Paket 14',
                'short_code' => 'DP14',
                'category_id' => 1,
                'created_at' => '2024-04-11 14:27:45',
                'updated_at' => '2024-04-11 14:27:45',
                'deleted_at' => null,
                'gender' => 'female',
            ],
        ], ['id']);

        DB::table('service_service_package')->upsert([
            [
                'id' => 1, 'service_package_id' => '1', 'service_id' => '11',
            ],
            [
                'id' => 2, 'service_package_id' => '1', 'service_id' => '20',
            ],
            [
                'id' => 3, 'service_package_id' => '2', 'service_id' => '11',
            ],
            [
                'id' => 4, 'service_package_id' => '2', 'service_id' => '20',
            ],
            [
                'id' => 5, 'service_package_id' => '2', 'service_id' => '101',
            ],
            [
                'id' => 6, 'service_package_id' => '3', 'service_id' => '20',
            ],
            [
                'id' => 7, 'service_package_id' => '3', 'service_id' => '5',
            ],
            [
                'id' => 8, 'service_package_id' => '3', 'service_id' => '101',
            ],
            [
                'id' => 9, 'service_package_id' => '4', 'service_id' => '22',
            ],
            [
                'id' => 10, 'service_package_id' => '4', 'service_id' => '12',
            ],
            [
                'id' => 11, 'service_package_id' => '4', 'service_id' => '15',
            ],
            [
                'id' => 12, 'service_package_id' => '5', 'service_id' => '25',
            ],
            [
                'id' => 13, 'service_package_id' => '5', 'service_id' => '26',
            ],
            [
                'id' => 14, 'service_package_id' => '5', 'service_id' => '27',
            ],
            [
                'id' => 15, 'service_package_id' => '6', 'service_id' => '26',
            ],
            [
                'id' => 16, 'service_package_id' => '6', 'service_id' => '27',
            ],
            [
                'id' => 17, 'service_package_id' => '6', 'service_id' => '11',
            ],
            [
                'id' => 18, 'service_package_id' => '7', 'service_id' => '26',
            ],
            [
                'id' => 19, 'service_package_id' => '7', 'service_id' => '11',
            ],
            [
                'id' => 20, 'service_package_id' => '7', 'service_id' => '20',
            ],
            [
                'id' => 21, 'service_package_id' => '7', 'service_id' => '27',
            ],
            [
                'id' => 22, 'service_package_id' => '8', 'service_id' => '26',
            ],
            [
                'id' => 23, 'service_package_id' => '8', 'service_id' => '25',
            ],
            [
                'id' => 24, 'service_package_id' => '8', 'service_id' => '20',
            ],
            [
                'id' => 25, 'service_package_id' => '8', 'service_id' => '27',
            ],
            [
                'id' => 26, 'service_package_id' => '9', 'service_id' => '25',
            ],
            [
                'id' => 27, 'service_package_id' => '9', 'service_id' => '26',
            ],
            [
                'id' => 28, 'service_package_id' => '9', 'service_id' => '11',
            ],
            [
                'id' => 29, 'service_package_id' => '9', 'service_id' => '20',
            ],
            [
                'id' => 30, 'service_package_id' => '9', 'service_id' => '27',
            ],
            [
                'id' => 31, 'service_package_id' => '10', 'service_id' => '25',
            ],
            [
                'id' => 32, 'service_package_id' => '10', 'service_id' => '26',
            ],
            [
                'id' => 33, 'service_package_id' => '10', 'service_id' => '11',
            ],
            [
                'id' => 34, 'service_package_id' => '10', 'service_id' => '20',
            ],
            [
                'id' => 35, 'service_package_id' => '10', 'service_id' => '27',
            ],
            [
                'id' => 36, 'service_package_id' => '10', 'service_id' => '101',
            ],
            [
                'id' => 37, 'service_package_id' => '11', 'service_id' => '11',
            ],
            [
                'id' => 38, 'service_package_id' => '11', 'service_id' => '12',
            ],
            [
                'id' => 39, 'service_package_id' => '11', 'service_id' => '13',
            ],
            [
                'id' => 40, 'service_package_id' => '11', 'service_id' => '18',
            ],
            [
                'id' => 41, 'service_package_id' => '11', 'service_id' => '20',
            ],
            [
                'id' => 42, 'service_package_id' => '11', 'service_id' => '22',
            ],
            [
                'id' => 43, 'service_package_id' => '11', 'service_id' => '24',
            ],
            [
                'id' => 44, 'service_package_id' => '11', 'service_id' => '25',
            ],
            [
                'id' => 45, 'service_package_id' => '11', 'service_id' => '26',
            ],
            [
                'id' => 46, 'service_package_id' => '11', 'service_id' => '27',
            ],
            [
                'id' => 47, 'service_package_id' => '11', 'service_id' => '15',
            ],
            [
                'id' => 48, 'service_package_id' => '11', 'service_id' => '17',
            ],
            [
                'id' => 49, 'service_package_id' => '11', 'service_id' => '19',
            ],
            [
                'id' => 50, 'service_package_id' => '11', 'service_id' => '97',
            ],
            [
                'id' => 51, 'service_package_id' => '11', 'service_id' => '34',
            ],
            [
                'id' => 52, 'service_package_id' => '12', 'service_id' => '14',
            ],
            [
                'id' => 53, 'service_package_id' => '12', 'service_id' => '13',
            ],
            [
                'id' => 54, 'service_package_id' => '13', 'service_id' => '18',
            ],
            [
                'id' => 55, 'service_package_id' => '13', 'service_id' => '21',
            ],
            [
                'id' => 56, 'service_package_id' => '13', 'service_id' => '24',
            ],
            [
                'id' => 57, 'service_package_id' => '14', 'service_id' => '18',
            ],
            [
                'id' => 58, 'service_package_id' => '14', 'service_id' => '21',
            ],
            [
                'id' => 59, 'service_package_id' => '14', 'service_id' => '24',
            ],
            [
                'id' => 60, 'service_package_id' => '14', 'service_id' => '22',
            ],
            [
                'id' => 61, 'service_package_id' => '15', 'service_id' => '18',
            ],
            [
                'id' => 62, 'service_package_id' => '15', 'service_id' => '21',
            ],
            [
                'id' => 63, 'service_package_id' => '15', 'service_id' => '24',
            ],
            [
                'id' => 64, 'service_package_id' => '15', 'service_id' => '14',
            ],
            [
                'id' => 65, 'service_package_id' => '15', 'service_id' => '13',
            ],
            [
                'id' => 66, 'service_package_id' => '16', 'service_id' => '18',
            ],
            [
                'id' => 67, 'service_package_id' => '16', 'service_id' => '21',
            ],
            [
                'id' => 68, 'service_package_id' => '16', 'service_id' => '24',
            ],
            [
                'id' => 69, 'service_package_id' => '16', 'service_id' => '14',
            ],
            [
                'id' => 70, 'service_package_id' => '16', 'service_id' => '13',
            ],
            [
                'id' => 71, 'service_package_id' => '16', 'service_id' => '22',
            ],
            [
                'id' => 72, 'service_package_id' => '17', 'service_id' => '22',
            ],
            [
                'id' => 73, 'service_package_id' => '17', 'service_id' => '12',
            ],
            [
                'id' => 74, 'service_package_id' => '17', 'service_id' => '15',
            ],
            [
                'id' => 75, 'service_package_id' => '18', 'service_id' => '25',
            ],
            [
                'id' => 76, 'service_package_id' => '18', 'service_id' => '26',
            ],
            [
                'id' => 77, 'service_package_id' => '18', 'service_id' => '27',
            ],
            [
                'id' => 78, 'service_package_id' => '19', 'service_id' => '11',
            ],
            [
                'id' => 79, 'service_package_id' => '19', 'service_id' => '12',
            ],
            [
                'id' => 80, 'service_package_id' => '19', 'service_id' => '13',
            ],
            [
                'id' => 81, 'service_package_id' => '19', 'service_id' => '14',
            ],
            [
                'id' => 82, 'service_package_id' => '19', 'service_id' => '15',
            ],
            [
                'id' => 83, 'service_package_id' => '19', 'service_id' => '18',
            ],
            [
                'id' => 84, 'service_package_id' => '19', 'service_id' => '21',
            ],
            [
                'id' => 85, 'service_package_id' => '19', 'service_id' => '22',
            ],
            [
                'id' => 86, 'service_package_id' => '19', 'service_id' => '24',
            ],
            [
                'id' => 87, 'service_package_id' => '19', 'service_id' => '25',
            ],
            [
                'id' => 88, 'service_package_id' => '19', 'service_id' => '26',
            ],
            [
                'id' => 89, 'service_package_id' => '19', 'service_id' => '27',
            ],
            [
                'id' => 90, 'service_package_id' => '19', 'service_id' => '20',
            ],
            [
                'id' => 91, 'service_package_id' => '19', 'service_id' => '95',
            ],
            [
                'id' => 92, 'service_package_id' => '21', 'service_id' => '20',
            ],
            [
                'id' => 93, 'service_package_id' => '21', 'service_id' => '26',
            ],
            [
                'id' => 94, 'service_package_id' => '21', 'service_id' => '27',
            ],
            [
                'id' => 95, 'service_package_id' => '28', 'service_id' => '25',
            ],
            [
                'id' => 96, 'service_package_id' => '28', 'service_id' => '26',
            ],
            [
                'id' => 97, 'service_package_id' => '28', 'service_id' => '27',
            ],
            [
                'id' => 98, 'service_package_id' => '28', 'service_id' => '20',
            ],
            [
                'id' => 99, 'service_package_id' => '28', 'service_id' => '95',
            ],
            [
                'id' => 100, 'service_package_id' => '29', 'service_id' => '25',
            ],
            [
                'id' => 101, 'service_package_id' => '29', 'service_id' => '26',
            ],
            [
                'id' => 102, 'service_package_id' => '29', 'service_id' => '27',
            ],
            [
                'id' => 103, 'service_package_id' => '29', 'service_id' => '34',
            ],
            [
                'id' => 104, 'service_package_id' => '29', 'service_id' => '20',
            ],
            [
                'id' => 105, 'service_package_id' => '30', 'service_id' => '11',
            ],
            [
                'id' => 106, 'service_package_id' => '30', 'service_id' => '12',
            ],
            [
                'id' => 107, 'service_package_id' => '30', 'service_id' => '13',
            ],
            [
                'id' => 108, 'service_package_id' => '30', 'service_id' => '15',
            ],
            [
                'id' => 109, 'service_package_id' => '30', 'service_id' => '18',
            ],
            [
                'id' => 110, 'service_package_id' => '30', 'service_id' => '22',
            ],
            [
                'id' => 111, 'service_package_id' => '30', 'service_id' => '17',
            ],
            [
                'id' => 112, 'service_package_id' => '30', 'service_id' => '19',
            ],
            [
                'id' => 113, 'service_package_id' => '30', 'service_id' => '24',
            ],
            [
                'id' => 114, 'service_package_id' => '30', 'service_id' => '97',
            ],
            [
                'id' => 115, 'service_package_id' => '31', 'service_id' => '12',
            ],
            [
                'id' => 116, 'service_package_id' => '31', 'service_id' => '13',
            ],
            [
                'id' => 117, 'service_package_id' => '31', 'service_id' => '14',
            ],
            [
                'id' => 118, 'service_package_id' => '31', 'service_id' => '15',
            ],
            [
                'id' => 119, 'service_package_id' => '31', 'service_id' => '18',
            ],
            [
                'id' => 120, 'service_package_id' => '31', 'service_id' => '21',
            ],
            [
                'id' => 121, 'service_package_id' => '31', 'service_id' => '22',
            ],
            [
                'id' => 122, 'service_package_id' => '31', 'service_id' => '24',
            ],
            [
                'id' => 123, 'service_package_id' => '31', 'service_id' => '11',
            ],
            [
                'id' => 124, 'service_package_id' => '36', 'service_id' => '25',
            ],
            [
                'id' => 125, 'service_package_id' => '36', 'service_id' => '26',
            ],
            [
                'id' => 126, 'service_package_id' => '36', 'service_id' => '27',
            ],
            [
                'id' => 127, 'service_package_id' => '55', 'service_id' => '95',
            ],
            [
                'id' => 128, 'service_package_id' => '55', 'service_id' => '96',
            ],
            [
                'id' => 129, 'service_package_id' => '73', 'service_id' => '6',
            ],
            [
                'id' => 130, 'service_package_id' => '73', 'service_id' => '7',
            ],
            [
                'id' => 131, 'service_package_id' => '171', 'service_id' => '1',
            ],
            [
                'id' => 132, 'service_package_id' => '171', 'service_id' => '10',
            ],
            [
                'id' => 133, 'service_package_id' => '520', 'service_id' => '94',
            ],
            [
                'id' => 134, 'service_package_id' => '520', 'service_id' => '96',
            ],
            [
                'id' => 135, 'service_package_id' => '685', 'service_id' => '20',
            ],
            [
                'id' => 136, 'service_package_id' => '685', 'service_id' => '101',
            ],
        ], ['id']);
    }
}
