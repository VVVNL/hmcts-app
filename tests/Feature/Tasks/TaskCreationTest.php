<?php

namespace Tests\Feature\Tasks;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create();
    }

    private function createDefaultStatus(): Status
    {
        // Ensure there is a status with id = 1 as expected by TaskController@store
        return Status::factory()->create([
            'id' => 1,
            'name' => 'Pending',
        ]);
    }

    public function test_create_task_page_is_accessible()
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get(route('tasks.create'));

        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Tasks/Create')
        );
    }

    public function test_user_can_create_task_with_valid_data(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $this->createDefaultStatus();

        $due = Carbon::now()->addDay()->format('Y-m-d\\TH:i');

        $payload = [
            'title' => 'My Task',
            'description' => 'My task description',
            'due' => $due,
        ];

        $response = $this->post(route('tasks.store'), $payload);

        $response->assertRedirect(route('tasks.create'));
        $response->assertSessionHas('success.message', 'Task created successfully.');
        $response->assertSessionHas('success.task');

        $this->assertDatabaseCount('tasks', 1);

        $task = Task::first();

        $this->assertNotNull($task);
        $this->assertEquals('My Task', $task->title);
        $this->assertEquals('My task description', $task->description);
        $this->assertEquals($user->id, $task->user_id);
        $this->assertEquals(1, $task->status_id);
        $this->assertEquals(Carbon::parse($due)->toDateTimeString(), $task->due->toDateTimeString());
    }

    public function test_store_requires_title(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $this->createDefaultStatus();

        $due = Carbon::now()->addDay()->format('Y-m-d\\TH:i');

        $payload = [
            'title' => '',
            'description' => 'My task description',
            'due' => $due,
        ];

        $response = $this->from(route('tasks.create'))->post(route('tasks.store'), $payload);

        $response->assertRedirect(route('tasks.create'));
        $response->assertSessionHasErrors(['title']);
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_store_requires_due_in_correct_format(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $this->createDefaultStatus();

        // Invalid format (space instead of T)
        $payload = [
            'title' => 'My Task',
            'description' => 'My task description',
            'due' => '2025-12-01 10:00',
        ];

        $response = $this->from(route('tasks.create'))->post(route('tasks.store'), $payload);

        $response->assertRedirect(route('tasks.create'));
        $response->assertSessionHasErrors(['due']);
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_store_rejects_past_due_date(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $this->createDefaultStatus();

        $due = Carbon::now()->subDay()->format('Y-m-d\\TH:i');

        $payload = [
            'title' => 'My Task',
            'description' => 'My task description',
            'due' => $due,
        ];

        $response = $this->from(route('tasks.create'))->post(route('tasks.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['due']);
        $this->assertDatabaseCount('tasks', 0);
    }
}

