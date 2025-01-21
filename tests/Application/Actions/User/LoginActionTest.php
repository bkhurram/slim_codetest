<?php

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Application\Models\User;
use App\Application\Services\JwtService;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Support\Arr;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
//		$this->setUpDatabase();
    }

    protected function setUp(): void
    {
//		$this->setUpDatabase();
    }

    protected function setUpDatabase($container): void
    {
        $capsule = $container->get(CapsuleManager::class);

        // Create SQLite database schema
        $capsule::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('givenName')->nullable();
            $table->string('familyName')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('dateOfBirth')->nullable();
            $table->string('createdAt')->nullable();

            $table->timestamps();
        });

        // Seed a test user
//		$capsule::table('users')->insert([
//			'email' => 'test@example.com',
//			'password' => password_hash('password123', PASSWORD_BCRYPT),
//		]);

    }

    public function testAction()
    {
        $app = $this->getAppInstance();
        $container = $app->getContainer();

        $this->setUpDatabase($container);

        $data = [
            'givenName'  => 'John',
            'familyName' => 'Doe',
            'email'      => 'johndoe@example.com',
            'password'   => 'password',
        ];

        $user = new User();
        $user->forceFill(Arr::except($data, 'password'));
        $user->password = password_hash('password', PASSWORD_BCRYPT);
        $user->save();

        $request = $this->createPostJsonRequest('/login', Arr::only($data, ['email', 'password']));
        $response = $app->handle($request);

        $payload = json_decode($response->getBody());
//		$expectedPayload = new ActionPayload(200, [$user]);
//		$serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $jwtService = new JwtService($_ENV['JWT_SECRET']);
        $userData = $jwtService->decodeToken($payload->jwt);

        $this->assertTrue($jwtService->validateToken($payload->jwt));
        $this->assertEquals($data['email'], $userData['email']);
    }
}
