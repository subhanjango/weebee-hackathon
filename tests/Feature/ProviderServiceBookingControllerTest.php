<?php

namespace Tests\Feature;

use App\Models\ProviderService;
use App\Models\ProviderServicesBreak;
use App\Models\ProviderServicesPlannedOffSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ProviderServiceBookingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $seed = true;

    public function __construct(string $name)
    {
        $this->setUpFaker();
        parent::__construct($name);
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
            'end_time',
            'primary_user_first_name',
            'primary_user_last_name',
            'primary_user_email_address',
            'secondary_users_active'
        ];

        $this->assertEquals(!count(array_diff(array_keys((array)json_decode($response->content(), true)['errors']), $primary_validations)), true);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_without_secondary_user_validation_test()
    {
        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '08:10',
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => false
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $response->assertStatus(200);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_secondary_user_validation_test()
    {
        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '08:10',
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => true
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $response->assertStatus(422);

        $primary_validations = [
            'secondary_users'
        ];

        $this->assertEquals(!count(array_diff(array_keys((array)json_decode($response->content(), true)['errors']), $primary_validations)), true);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_on_already_booked_slot_validation_test()
    {
        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '08:10',
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => false
        ];

        $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '08:10',
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => false
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $response->assertStatus(400);

        $error_reason = 'Already booked';

        $this->assertEquals(in_array($error_reason, (array)json_decode($response->content(), true)['reason']), true);

    }

    /**
     * @return void
     * @test
     */
    public function book_slot_on_non_bookable_slot_validation_test()
    {
        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'), // is open
            'start_time' => '08:00',
            'end_time' => '08:55',
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => false
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

        $break = ProviderServicesBreak::where('day_number', $day_number)->where('service_id', ProviderService::first()->id)->first();

        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'), // is open
            'start_time' => $break->start_time,
            'end_time' => $break->end_time,
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => false
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $error_reason = 'Lunch Time';

        $this->assertEquals(in_array($error_reason, (array)json_decode($response->content(), true)['reason']), true);

        $response->assertStatus(400);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_on_holiday_validation_test()
    {
        $holiday = ProviderServicesPlannedOffSchedule::where('service_id', ProviderService::first()->id)->first();

        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => $holiday->date,
            'start_time' => '12:00',
            'end_time' => '12:10',
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => false
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $error_reason = $holiday->reason;

        $this->assertEquals(in_array($error_reason, (array)json_decode($response->content(), true)['reason']), true);

        $response->assertStatus(400);
    }

    /**
     * @return void
     * @test
     */
    public function book_slot_with_secondary_users_validation_test()
    {
        $data = [
            'service_id' => ProviderService::first()->id,
            'date' => now()->next('Wednesday')->format('Y-m-d'), // is open
            'start_time' => '08:00',
            'end_time' => '08:10',
            'primary_user_first_name' => $this->faker->firstName,
            'primary_user_last_name' => $this->faker->lastName,
            'primary_user_email_address' => $this->faker->email,
            'secondary_users_active' => true,
            'secondary_users' => [
                [
                    'clone_primary_user' => true,
                ],
                [
                    'clone_primary_user' => false,
                    'email' => $this->faker->email,
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName
                ]
            ]
        ];

        $response = $this->post('/api/book-slot', $data, ['Accept' => 'application/json']);

        $response->assertStatus(200);
    }
}
