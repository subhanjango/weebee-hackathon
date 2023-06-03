<?php

namespace Tests\Feature;

use App\Helpers\SlotHelper;
use App\Models\ProviderService;
use App\Models\ProviderServicesBreak;
use App\Models\ProviderServicesPlannedOffSchedule;
use App\Models\ProviderServicesSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ProviderServiceBookingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function availableSlots()
    {
        $service = ProviderService::with(['schedule', 'breaks'])->first();
        $schedule = $service->schedule[0];
        $slots = SlotHelper::createSlots($schedule['start_time'], $schedule['end_time'], $service->minute_duration_per_booking, $service->prep_break_in_minute);
        return collect($slots)
            ->where('slot_start_time', '>', now()->format('H:i'))
            ->first();
    }

    public function __construct(string $name)
    {
        $this->setUpFaker();
        parent::__construct($name);
    }

    public function initSchedule()
    {
        $days = config('constants.days');

        $service = ProviderService::first();

        $schedule = [
            [
                'day_number' => $days['MONDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $service->id
            ],
            [
                'day_number' => $days['TUESDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $service->id
            ],
            [
                'day_number' => $days['WEDNESDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $service->id
            ],
            [
                'day_number' => $days['THURSDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $service->id
            ],
            [
                'day_number' => $days['FRIDAY'],
                'start_time' => '08:00',
                'end_time' => '22:00',
                'service_id' => $service->id
            ],
            [
                'day_number' => $days['SATURDAY'],
                'start_time' => '10:00',
                'end_time' => '22:00',
                'service_id' => $service->id
            ],
            [
                'day_number' => $days['SUNDAY'],
                'day_off' => true,
                'service_id' => $service->id
            ],
        ];

        foreach ($schedule as $value) {
            ProviderServicesSchedule::create($value);
        }
    }

    /**
     * @test
     */
    public function get_slots_validation_test(): void
    {
        $response = $this->get('/api/get-schedule', ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function get_slots_response_test(): void
    {
        $start_date = Carbon::now()->format('Y-m-d');
        $end_date = Carbon::now()->addDays(30)->format('Y-m-d');

        $response = $this->get('/api/get-schedule?' . http_build_query(compact('start_date', 'end_date')), ['Accept' => 'application/json']);

        $response->assertStatus(200);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_validation_test()
    {
        $response = $this->post('/api/book-slot', [], ['Accept' => 'application/json']);

        $response->assertStatus(422);

        $primary_validations = [
            'service_id',
            'date',
            'start_time',
            'users'
        ];

        $this->assertEquals(!count(array_diff(array_keys((array)json_decode($response->content(), true)['errors']), $primary_validations)), true);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_secondary_user_validation_test()
    {
        $service = ProviderService::create([
            'name' => 'Men Haircut',
            'max_booking_future_days_limit' => 7,
            'max_client_per_booking' => 3,
            'minute_duration_per_booking' => 10,
            'prep_break_in_minute' => 5
        ]);

        $this->initSchedule();

        $data = [
            'service_id' => $service->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $response->assertStatus(200);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_on_already_booked_slot_validation_test()
    {
        $service = ProviderService::create([
            'name' => 'Men Haircut',
            'max_booking_future_days_limit' => 7,
            'max_client_per_booking' => 3,
            'minute_duration_per_booking' => 10,
            'prep_break_in_minute' => 5
        ]);

        $this->initSchedule();

        $data = [
            'service_id' => $service->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $data = [
            'service_id' => $service->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $data = [
            'service_id' => $service->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $data = [
            'service_id' => $service->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $data = [
            'service_id' => $service->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $response->assertStatus(400);

    }

    /**
     * @return void
     * @test
     */
    public function book_slot_on_non_bookable_slot_validation_test()
    {
        $service = ProviderService::create([
            'name' => 'Men Haircut',
            'max_booking_future_days_limit' => 7,
            'max_client_per_booking' => 3,
            'minute_duration_per_booking' => 10,
            'prep_break_in_minute' => 5
        ]);

        $this->initSchedule();

        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'), // is open
            'start_time' => Carbon::create($this->availableSlots()['slot_start_time'])->addMinutes(18)->format('H:i'),
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $error_reason = 'Invalid slot';

        $this->assertEquals(in_array($error_reason, (array)json_decode($response->content(), true)['reason']), true);

        $response->assertStatus(400);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_on_break_slot_validation_test()
    {
        $day_number = config('constants.days')['WEDNESDAY'];

        $service = ProviderService::create([
            'name' => 'Men Haircut',
            'max_booking_future_days_limit' => 7,
            'max_client_per_booking' => 3,
            'minute_duration_per_booking' => 10,
            'prep_break_in_minute' => 5
        ]);

        $this->initSchedule();

        $slot_time = $this->availableSlots();

        $break = ProviderServicesBreak::create([
            'day_number' => $day_number,
            'service_id' => $service->id,
            'reason' => 'Lunch Break',
            'start_time' => $slot_time['slot_start_time'],
            'end_time' => $slot_time['slot_end_time'],
        ]);

        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'), // is open
            'start_time' => $break->start_time,
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $error_reason = 'Lunch Break';

        $this->assertEquals(in_array($error_reason, (array)json_decode($response->content(), true)['reason']), true);

        $response->assertStatus(400);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_on_holiday_validation_test()
    {

        $service = ProviderService::create([
            'name' => 'Men Haircut',
            'max_booking_future_days_limit' => 7,
            'max_client_per_booking' => 3,
            'minute_duration_per_booking' => 10,
            'prep_break_in_minute' => 5
        ]);

        $this->initSchedule();

        $holiday = ProviderServicesPlannedOffSchedule::create([
            'date' => now()->addDays(3)->format('Y-m-d'),
            'reason' => 'Public Holiday',
            'service_id' => $service->id,
            'full_day_off' => true
        ]);

        $data = [
            'service_id' => $service->id,
            'date' => $holiday->date,
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ]
            ]
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $response->assertStatus(400);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_with_exceed_secondary_users_limit_validation_test()
    {

        $service = ProviderService::create([
            'name' => 'Men Haircut',
            'max_booking_future_days_limit' => 7,
            'max_client_per_booking' => 3,
            'minute_duration_per_booking' => 10,
            'prep_break_in_minute' => 5
        ]);

        $this->initSchedule();

        $data = [
            'service_id' => $service->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'), // is open
            'start_time' => $this->availableSlots()['slot_start_time'],
            'users' => [
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ],
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ],
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ],
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ],
                [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                ],
            ]
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $error_reason = 'User limit exceeded';

        $this->assertEquals(in_array($error_reason, (array)json_decode($response->content(), true)['reason']), true);

        $response->assertStatus(400);
    }
}
