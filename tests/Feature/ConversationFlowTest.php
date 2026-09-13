<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ConversationFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_lists_only_the_authenticated_users_rooms(): void
    {
        $member = User::factory()->create();
        $otherUser = User::factory()->create();
        $memberConversation = Conversation::factory()->create(['name' => 'Member Room']);
        $otherConversation = Conversation::factory()->create(['name' => 'Private Room']);
        $memberConversation->users()->attach($member);
        $otherConversation->users()->attach($otherUser);

        $this->actingAs($member)
            ->get('/home')
            ->assertSee('Member Room')
            ->assertDontSee('Private Room');
    }

    public function test_creator_becomes_a_member_and_is_redirected_to_the_new_room(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/conversations', ['name' => 'Project Chat'])
            ->assertRedirectToRoute('conversations.show', ['conversation' => 1]);

        $this->assertDatabaseHas('conversations', ['id' => 1, 'name' => 'Project Chat']);
        $this->assertDatabaseHas('conversation_user', ['conversation_id' => 1, 'user_id' => $user->id]);
    }

    public function test_empty_room_name_redirects_back_with_a_validation_error(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/home')
            ->post('/conversations', ['name' => ''])
            ->assertRedirect('/home')
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    public function test_guests_are_redirected_from_conversation_actions(): void
    {
        $conversation = Conversation::factory()->create();

        $this->get('/home')->assertRedirect('/login');
        $this->post('/conversations', ['name' => 'Guest Room'])->assertRedirect('/login');
        $this->post('/messages', [
            'conversation_id' => $conversation->id,
            'body' => 'Guest message',
        ])->assertRedirect('/login');
    }

    public function test_non_member_cannot_view_another_users_room(): void
    {
        $member = User::factory()->create();
        $nonMember = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->users()->attach($member);

        $this->actingAs($nonMember)
            ->get(route('conversations.show', $conversation))
            ->assertForbidden();
    }

    public function test_selected_room_only_displays_its_own_messages(): void
    {
        $user = User::factory()->create();
        $selectedConversation = Conversation::factory()->create(['name' => 'Selected Room']);
        $otherConversation = Conversation::factory()->create(['name' => 'Other Room']);
        $selectedConversation->users()->attach($user);
        $otherConversation->users()->attach($user);
        Message::factory()->create([
            'user_id' => $user->id,
            'conversation_id' => $selectedConversation->id,
            'body' => 'Visible message',
        ]);
        Message::factory()->create([
            'user_id' => $user->id,
            'conversation_id' => $otherConversation->id,
            'body' => 'Hidden message',
        ]);

        $this->actingAs($user)
            ->get(route('conversations.show', $selectedConversation))
            ->assertSee('Visible message')
            ->assertDontSee('Hidden message');
    }

    public function test_room_messages_are_html_escaped(): void
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->users()->attach($user);
        Message::factory()->create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'body' => '<script>alert("x")</script>',
        ]);

        $this->actingAs($user)
            ->get(route('conversations.show', $conversation))
            ->assertSee('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("x")</script>', false);
    }

    public function test_member_can_send_a_message_to_the_selected_room(): void
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->users()->attach($user);
        Event::fake([MessageSent::class]);

        $this->actingAs($user)
            ->post('/messages', [
                'conversation_id' => $conversation->id,
                'body' => 'Hello from the room',
            ])
            ->assertRedirectToRoute('conversations.show', $conversation);

        $this->assertDatabaseHas('messages', [
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'body' => 'Hello from the room',
        ]);
        Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($user, $conversation): bool {
            return $event->message->user_id === $user->id
                && $event->message->conversation_id === $conversation->id
                && $event->message->body === 'Hello from the room';
        });
    }

    public function test_non_member_cannot_send_a_message_to_another_users_room(): void
    {
        $member = User::factory()->create();
        $nonMember = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->users()->attach($member);
        Event::fake([MessageSent::class]);

        $this->actingAs($nonMember)
            ->post('/messages', [
                'conversation_id' => $conversation->id,
                'body' => 'Unauthorized message',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('messages', [
            'conversation_id' => $conversation->id,
            'body' => 'Unauthorized message',
        ]);
        Event::assertNotDispatched(MessageSent::class);
    }
}
