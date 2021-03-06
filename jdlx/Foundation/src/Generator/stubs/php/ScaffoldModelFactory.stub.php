<?php
namespace Database\Factories;

use App\Generated\__MODEL__\__MODEL__Fields;
use App\Models\__MODEL__;
use Illuminate\Database\Eloquent\Factories\Factory;

class __MODEL__Factory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = __MODEL__::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return __MODEL__Fields::factory($this->faker);
    }
}



// EXAMPLES TO WORK WITH
// ------------------------

/**
type' => $faker->words(3, true),
'user_id' => $faker->randomNumber(2),
'source' => $faker->company,
'cause_id' => $faker->randomNumber(1) + 1,
'charity' => $faker->company,
'amount' => $faker->randomFloat(0, 1, 6)*0.25,
'status' => $faker->randomElement([App\Donation::PLEDGED, \App\Donation::PENDING, \App\Donation::PAID]),
'visibility' => $type,
'remark' => $faker->paragraph,
'created_at' => $faker->dateTimeBetween('-1 years', '-1 hour'),
'ticket' => $type === App\Donation::REDACTED ?  $faker->numberBetween(100, 200) : null,
'redacted_at' => $type === App\Donation::REDACTED ? $faker->dateTimeBetween('-1 years', '-1 hour') : null,
'redact_reason' => $type === App\Donation::REDACTED ? $faker->words(3, true) : null
'firstname' => $faker->firstName,
'lastname' => $faker->lastName,
'nickname' => $faker->userName,
'email' => $faker->unique()->safeEmail,
'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
'onboarded' => true,
'one_signal_id' => null,
'remember_token' => \Illuminate\Support\Str::random(10),
'test_account' => false
 */
