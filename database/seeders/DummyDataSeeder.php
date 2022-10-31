<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school_name = ['SMA Mawar Melati', 'SMA Semua Indah'];
        foreach($school_name as $name) {
            $school = School::create([
                'name' => $name, 
                'lat' => 0, 
                'lng' => 0, 
                'distance' => 100, 
                'clock_in' => '07:00', 
                'clock_out' => '14:00'
            ]);
            
            for ($i=1; $i<=3; $i++) {
                User::create([
                    'username' => 'admin_'.$school->id.'_'.$i,
                    'name' => 'Admin '.$school->id.' - '.$i,
                    'school_id' => $school->id,
                    'email' => 'admin_'.$school->id.'_'.$i.'@mail.com',
                    'password' => Hash::make('password'),
                    'role' => 'ADMIN',
                ]);
            }
            for ($i=1; $i<=3; $i++) {
                User::create([
                    'username' => 'operator_'.$school->id.'_'.$i,
                    'name' => 'Operator '.$school->id.' - '.$i,
                    'school_id' => $school->id,
                    'email' => 'operator_'.$school->id.'_'.$i.'@mail.com',
                    'password' => Hash::make('password'),
                    'role' => 'OPERATOR',
                ]);
            }
            for ($j=10; $j<=12; $j++) {
                for ($i = 1; $i <= 3; $i ++) {
                    $grade = Grade::create([
                        'name' => 'Kelas '.$j.'-'.$i, 
                        'grade' => $j, 
                        'school_id' => $school->id
                    ]);

                    for ($k = 1; $k <= 10; $k++) {
                        User::create([
                            'username' => $school->id.'_'.$grade->id.'_'.$k,
                            'name' => 'Siswa '.$school->id.' - '.$grade->id.' - '.$k,
                            'school_id' => $school->id,
                            'grade_id' => $grade->id,
                            'password' => Hash::make('password'),
                            'role' => 'USER',
                        ]);
                    }
                }
            } 
        }
        
    }
}
