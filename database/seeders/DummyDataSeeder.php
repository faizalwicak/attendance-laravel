<?php

namespace Database\Seeders;

use App\Models\Attend;
use App\Models\Grade;
use App\Models\Leave;
use App\Models\Quote;
use App\Models\Record;
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
        $year = 2022;
        $month = 11;

        $school_name = ['SMA Mawar Melati', 'SMA Semua Indah'];
        foreach ($school_name as $name) {
            $school = School::create([
                'name' => $name,
                'lat' => -7.6832335,
                'lng' => 110.339018,
                'distance' => 100,
                'clock_in' => '07:00',
                'clock_out' => '14:00'
            ]);

            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'username' => 'admin' . $school->id . $i,
                    'name' => fake('id_ID')->name(), //'Admin '.$school->id.' - '.$i,
                    'school_id' => $school->id,
                    'email' => 'admin' . $school->id . $i . '@mail.com',
                    'password' => Hash::make('password'),
                    'role' => 'ADMIN',
                ]);
            }
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'username' => 'operator' . $school->id . $i,
                    'name' => fake('id_ID')->name(),
                    'school_id' => $school->id,
                    'email' => 'operator' . $school->id . $i . '@mail.com',
                    'password' => Hash::make('password'),
                    'role' => 'OPERATOR',
                ]);
            }
            for ($i = 10; $i <= 12; $i++) {
                for ($j = 1; $j <= 3; $j++) {
                    $grade = Grade::create([
                        'name' => 'Kelas ' . $i . '-' . $j,
                        'grade' => $i,
                        'school_id' => $school->id
                    ]);

                    for ($k = 0; $k <= 9; $k++) {
                        $gender = fake()->randomElement(['MALE', 'FEMALE']);

                        $user = User::create([
                            'username' => '00' . $school->id . $grade->id . $k,
                            'name' => fake()->firstname(strtolower($gender)) . ' ' . fake()->lastname(strtolower($gender)), //'Siswa '.$school->id.' - '.$grade->id.' - '.$k,
                            'gender' => $gender,
                            'school_id' => $school->id,
                            'grade_id' => $grade->id,
                            'password' => Hash::make('password'),
                            'role' => 'USER',
                        ]);

                        for ($l = 0; $l < 30; $l++) {
                            $digit = fake()->randomDigit();
                            if (in_array($digit, [0])) {
                            } else if (in_array($digit, [1])) {
                                $record = Record::create([
                                    'user_id' => $user->id,
                                    'date' => $year . '/' . $month . '/' . $l + 1,
                                    'is_leave' => 1
                                ]);
                                Leave::create([
                                    'record_id' => $record->id,
                                    'type' => fake()->randomDigit() % 2 == 0 ? 'SICK' : 'LEAVE',
                                    'description' => fake()->sentence(),
                                    'leave_status' => 'WAITING'
                                ]);
                            } else {
                                $tt = fake()->time('H:i:s');
                                $record = Record::create([
                                    'user_id' => $user->id,
                                    'date' => $year . '/' . $month . '/' . $l + 1,
                                    'is_leave' => 0
                                ]);

                                Attend::create([
                                    'record_id' => $record->id,
                                    'clock_in_time' => $tt,
                                    'clock_out_time' => fake()->time('H:i:s'),
                                    'clock_in_lat' => fake()->randomFloat(6, -8, -6),
                                    'clock_in_lng' => fake()->randomFloat(6, 109, 111),
                                    'clock_out_lat' => fake()->randomFloat(6, -8, -6),
                                    'clock_out_lng' => fake()->randomFloat(6, 109, 111),
                                    'clock_in_status' => strtotime($tt) <= strtotime($school->clock_in) ? 'ON_TIME' : 'LATE',
                                ]);
                            }
                        }
                    }
                }
            }
            for ($i = 0; $i < 10; $i++) {
                Quote::create([
                    'message' => fake()->text(100),
                    'active' => $i == 0 ? 1 : 0,
                    'school_id' => $school->id,
                ]);
            }
        }
    }
}
