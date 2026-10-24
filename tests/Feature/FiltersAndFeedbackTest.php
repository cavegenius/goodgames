<?php

namespace Tests\Feature;

use App\Filter;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class FiltersAndFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private function filterData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Favorites',
            'filter' => ['favorite' => ['true'], 'status' => [], 'platform' => []],
            'searchTerm' => '',
        ], $overrides);
    }

    public function test_saved_filters_can_be_created_listed_applied_renamed_updated_and_deleted(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->postJson('/filters/add', $this->filterData())
            ->assertExactJson(['Status' => 'Success', 'Message' => 'Filter Created Successfully']);
        $filter = Filter::where('userId', $user->id)->firstOrFail();

        $this->postJson('/filters/filterList')->assertJsonPath('Status', 'Success')->assertJsonCount(1, 'Filters');
        $this->postJson('/filters/apply', ['id' => $filter->id])->assertJsonPath('Filter.name', 'Favorites');
        $this->postJson('/filters/updateName', ['id' => $filter->id, 'name' => 'Backlog picks'])
            ->assertJson(['Status' => 'Success', 'Message' => 'Filter Updated Successfully']);
        $this->postJson('/filters/updateFilter', [
            'id' => $filter->id, 'filter' => '{"status":["Backlog"]}', 'searchTerm' => 'Zelda',
        ])->assertJson(['Status' => 'Success', 'Message' => 'Filter Updated Successfully']);
        $this->assertDatabaseHas('filters', [
            'id' => $filter->id, 'name' => 'Backlog picks', 'searchTerm' => 'Zelda',
        ]);
        $this->postJson('/filters/delete', ['id' => $filter->id])
            ->assertJson(['Status' => 'Success', 'Message' => 'Filter Deleted Successfully']);
        $this->assertDatabaseMissing('filters', ['id' => $filter->id]);
    }

    public function test_filter_creation_requires_a_name_and_at_least_one_criterion(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('/filters/add', ['filter' => [], 'searchTerm' => ''])
            ->assertUnprocessable()->assertJsonValidationErrors(['name']);
        $this->postJson('/filters/add', ['name' => 'Empty', 'filter' => [], 'searchTerm' => ''])
            ->assertJson(['Status' => 'Error', 'Message' => 'A search term must be entered or one or more filters must be selected']);
    }

    public function test_duplicate_filter_criteria_and_names_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('/filters/add', $this->filterData())->assertOk();
        $this->postJson('/filters/add', $this->filterData(['name' => 'Other']))
            ->assertJson(['Status' => 'Error', 'Message' => 'This filter already exists']);
        $this->postJson('/filters/add', $this->filterData(['name' => 'Favorites', 'searchTerm' => 'Zelda']))
            ->assertJson(['Status' => 'Error', 'Message' => 'This name already exists']);
    }

    public function test_saved_filters_cannot_be_read_or_changed_by_another_user(): void
    {
        $owner = User::factory()->create();
        $filter = new Filter();
        $filter->userId = $owner->id;
        $filter->name = 'Private';
        $filter->filter = '{}';
        $filter->searchTerm = '';
        $filter->save();
        $this->actingAs(User::factory()->create());
        $this->postJson('/filters/apply', ['id' => $filter->id])
            ->assertJson(['Status' => 'Error', 'Message' => 'Filter Not Found']);
        $this->postJson('/filters/updateFilter', ['id' => $filter->id, 'filter' => '{}'])
            ->assertJson(['Status' => 'Error', 'Message' => 'Invalid Filter']);
        $this->postJson('/filters/delete', ['id' => $filter->id])
            ->assertJson(['Status' => 'Error', 'Message' => 'Invalid Filter']);
        $this->assertDatabaseHas('filters', ['id' => $filter->id, 'name' => 'Private']);
    }

    public function test_feedback_requires_name_and_comments_and_sends_mail(): void
    {
        Mail::shouldReceive('send')->once()->with(
            'emails.feedback',
            Mockery::on(fn ($data) => $data['name'] === 'Player' && $data['comments'] === 'Great app'),
            Mockery::on(fn ($callback) => $callback instanceof \Closure)
        )->andReturnUsing(function ($view, $data, $callback) {
            $message = Mockery::mock();
            $message->shouldReceive('to')->once()->with('wyatt@cavegenius.com')->andReturnSelf();
            $message->shouldReceive('subject')->once()->with('Feedback from Your Game Lists');
            $callback($message);
        });
        $this->actingAs(User::factory()->create());

        $this->postJson('/feedback/submit', ['name' => 'Player'])
            ->assertUnprocessable()->assertJsonValidationErrors(['comments']);
        $this->postJson('/feedback/submit', [
            'name' => 'Player', 'email' => 'player@example.test', 'comments' => 'Great app',
        ])->assertExactJson(['Status' => 'Success', 'Message' => 'Your feedback has been submitted successfully']);

    }
}
