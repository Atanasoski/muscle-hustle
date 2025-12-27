<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteMemberRequest;
use App\Mail\MemberInvitationMail;
use App\Models\MemberInvitation;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MemberInvitationController extends Controller
{
    /**
     * Display the members management page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $partner = Partner::with('identity')->findOrFail($user->partner_id);

        // Get all members of this gym
        $members = $partner->users()
            ->with('profile')
            ->latest()
            ->paginate(15);

        // Get pending invitations
        $pendingInvitations = $partner->invitations()
            ->with('inviter')
            ->pending()
            ->latest()
            ->get();

        // Get expired invitations (last 30 days)
        $expiredInvitations = $partner->invitations()
            ->with('inviter')
            ->expired()
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->get();

        return view('members.index', compact('partner', 'members', 'pendingInvitations', 'expiredInvitations'));
    }

    /**
     * Store a new member invitation.
     */
    public function store(InviteMemberRequest $request): RedirectResponse
    {
        $user = $request->user();
        $partner = Partner::with('identity')->findOrFail($user->partner_id);

        // Create the invitation
        $invitation = MemberInvitation::create([
            'partner_id' => $partner->id,
            'invited_by' => $user->id,
            'email' => $request->email,
            'token' => MemberInvitation::generateToken(),
            'expires_at' => now()->addDays(config('app.invitation_expiry_days', 7)),
        ]);

        // Generate signup URL with token
        $signupUrl = route('register', ['invitation' => $invitation->token]);

        // Send the invitation email
        Mail::to($invitation->email)
            ->send(new MemberInvitationMail($invitation, $partner, $signupUrl));

        return redirect()->back()
            ->with('success', "Invitation sent to {$invitation->email}!");
    }

    /**
     * Resend an existing invitation.
     */
    public function resend(Request $request, MemberInvitation $invitation): RedirectResponse
    {
        $user = $request->user();

        // Verify the invitation belongs to the user's partner
        if ($invitation->partner_id !== $user->partner_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if invitation is already accepted
        if ($invitation->isAccepted()) {
            return redirect()->back()
                ->with('error', 'This invitation has already been accepted.');
        }

        // Update expiration date
        $invitation->update([
            'expires_at' => now()->addDays(config('app.invitation_expiry_days', 7)),
        ]);

        $partner = Partner::with('identity')->findOrFail($invitation->partner_id);

        // Generate signup URL with token
        $signupUrl = route('register', ['invitation' => $invitation->token]);

        // Resend the invitation email
        Mail::to($invitation->email)
            ->send(new MemberInvitationMail($invitation, $partner, $signupUrl));

        return redirect()->back()
            ->with('success', "Invitation resent to {$invitation->email}!");
    }

    /**
     * Cancel/delete an invitation.
     */
    public function destroy(Request $request, MemberInvitation $invitation): RedirectResponse
    {
        $user = $request->user();

        // Verify the invitation belongs to the user's partner
        if ($invitation->partner_id !== $user->partner_id) {
            abort(403, 'Unauthorized action.');
        }

        $email = $invitation->email;
        $invitation->delete();

        return redirect()->back()
            ->with('success', "Invitation to {$email} has been cancelled.");
    }
}
