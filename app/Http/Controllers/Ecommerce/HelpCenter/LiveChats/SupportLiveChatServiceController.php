<?php

namespace App\Http\Controllers\Ecommerce\HelpCenter\LiveChats;

use App\Http\Controllers\Controller;
use App\Models\AgentStatus;
use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class SupportLiveChatServiceController extends Controller
{
    public function index(): Response|ResponseFactory
    {
        $previousLiveChats = LiveChat::with(["user:id,name,avatar","agent:id,name,avatar"])
                                     ->where("user_id", auth()->id())
                                     ->where("is_active", false)
                                     ->get();

        $currentLiveChat = LiveChat::with(["user:id,name,avatar","agent:id,name,avatar"])
                                   ->where("user_id", auth()->id())
                                   ->where("is_active", true)
                                   ->first();

        if($currentLiveChat) {

            $liveChatMessages = LiveChatMessage::with(["chatFileAttachments","user:id,avatar","agent:id,avatar"])
                                               ->where("live_chat_id", $currentLiveChat->id)
                                               ->orderBy("id", "asc")
                                               ->get();

            return inertia("Ecommerce/HelpCenter/LiveChat/Index", compact("currentLiveChat", "previousLiveChats", "liveChatMessages"));
        }

        return inertia("Ecommerce/HelpCenter/LiveChat/Index", compact("currentLiveChat", "previousLiveChats"));
    }

    public function store(Request $request): RedirectResponse
    {

        $liveChat = LiveChat::firstOrCreate(
            [
                "user_id" => $request->user_id,
                "agent_id" => $request->agent_id,
                "is_active" => true
            ],
            [
                "user_id" => $request->user_id,
                "agent_id" => $request->agent_id,
                "is_active" => true
            ]
        );

        $availableAgent = AgentStatus::where("online_status", "online")
        ->where("chat_status", "avaliable")
        ->whereColumn('current_chat_count', '<', 'max_chat_capacity')
        ->first();

        if ($availableAgent) {
            $liveChat->update(["agent_id" => $availableAgent->agent_id,"is_active" => true]);
        }

        return to_route("service.live-chat.index");
    }

    public function update(LiveChat $liveChat): RedirectResponse
    {
        $liveChat->update([
            "is_active" => false,
            "ended_at" => now()
        ]);

        return to_route("home");
    }

}
