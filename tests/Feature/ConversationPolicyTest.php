<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ConversationPolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_members_can_view_and_send_messages_to_their_conversation(): void
    {
        $member = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->users()->attach($member);

        $this->assertTrue($member->can('view', $conversation));
        $this->assertTrue($member->can('sendMessage', $conversation));
    }

    public function test_non_members_cannot_view_or_send_messages_to_a_conversation(): void
    {
        $nonMember = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->assertFalse($nonMember->can('view', $conversation));
        $this->assertFalse($nonMember->can('sendMessage', $conversation));
    }
}
