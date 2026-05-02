<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups()
            ->with('owner')
            ->withCount('members')
            ->get();

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'monthly_fee' => 'required|numeric|min:0',
            'fee_type' => 'nullable|in:monthly,daily',
        ]);

        $validated['fee_type'] = $validated['fee_type'] ?? 'monthly';

        $group = auth()->user()->ownedGroups()->create($validated);
        auth()->user()->groups()->attach($group, ['role' => 'admin']);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Grupo criado com sucesso!');
    }

    public function show(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Você não é membro deste grupo.');

        $group->load([
            'owner',
            'members',
            'matches' => fn($q) => $q->orderByDesc('scheduled_at')->limit(5),
            'posts' => fn($q) => $q->with('user:id,name,position')->orderByDesc('created_at')->limit(20),
            'polls' => fn($q) => $q->with('creator:id,name', 'match:id,title,scheduled_at,status')->orderByDesc('created_at')->limit(30),
        ]);

        $userRole = $member->pivot->role;
        $userPresenceConfirmed = (bool) $member->pivot->presence_confirmed;
        $rankings = $group->rankings()->with('user:id,name,position')->orderBy('position')->get();

        $userPayout = Payout::where('user_id', auth()->id())
            ->where('group_id', $group->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->orderByDesc('due_date')
            ->first();

        $userDailyPaidToday = $group->fee_type === 'daily'
            ? Payout::where('user_id', auth()->id())
                ->where('group_id', $group->id)
                ->where('status', 'paid')
                ->whereDate('paid_at', today())
                ->exists()
            : false;

        $currentOpenMatch = $group->matches()
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest('scheduled_at')
            ->first();

        $visiblePolls = $group->polls
            ->filter(fn($poll) => $poll->isOpen() || ($poll->match && $poll->match->status !== 'finished'))
            ->values();

        $pollHistory = $group->polls
            ->filter(fn($poll) => !$poll->isOpen())
            ->values();

        return view('groups.show', compact('group', 'userRole', 'userPresenceConfirmed', 'rankings', 'visiblePolls', 'pollHistory', 'userPayout', 'userDailyPaidToday', 'currentOpenMatch'));
    }

    public function membersManagement(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member && $member->pivot->role === 'admin', 403, 'Apenas administradores podem gerenciar jogadores.');

        $group->load(['members', 'blockedUsers:id,name,email']);

        return view('groups.members-management', compact('group'));
    }

    public function edit(Group $group)
    {
        $this->authorize('update', $group);
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'monthly_fee' => 'required|numeric|min:0',
            'fee_type'    => 'nullable|in:monthly,daily',
            'status'      => 'required|in:active,inactive',
            'schedule_type'       => 'nullable|in:scheduled,weekly',
            'weekly_day'          => 'nullable|integer|min:0|max:6',
            'weekly_time'         => 'nullable|date_format:H:i',
            'confirmation_lock_at' => 'nullable|date_format:H:i',
            'ranking_config.win_weight'     => 'nullable|numeric|min:0|max:100',
            'ranking_config.penalty_weight' => 'nullable|numeric|min:0|max:100',
            'ranking_config.mvp_weight'     => 'nullable|numeric|min:0|max:100',
            'ranking_config.rating_weight'  => 'nullable|numeric|min:0|max:100',
        ]);

        $rankingConfig = array_filter([
            'win_weight'     => isset($validated['ranking_config']['win_weight'])     ? (float) $validated['ranking_config']['win_weight']     : null,
            'penalty_weight' => isset($validated['ranking_config']['penalty_weight']) ? (float) $validated['ranking_config']['penalty_weight'] : null,
            'mvp_weight'     => isset($validated['ranking_config']['mvp_weight'])     ? (float) $validated['ranking_config']['mvp_weight']     : null,
            'rating_weight'  => isset($validated['ranking_config']['rating_weight'])  ? (float) $validated['ranking_config']['rating_weight']  : null,
        ], fn($v) => $v !== null);

        $group->update([
            'name'                 => $validated['name'],
            'description'         => $validated['description'],
            'monthly_fee'         => $validated['monthly_fee'],
            'fee_type'            => $validated['fee_type'] ?? 'monthly',
            'status'              => $validated['status'],
            'ranking_config'      => $rankingConfig ?: null,
            'schedule_type'       => $validated['schedule_type'] ?? 'scheduled',
            'weekly_day'          => $validated['weekly_day'] ?? null,
            'weekly_time'         => $validated['weekly_time'] ?? null,
            'confirmation_lock_at' => $validated['confirmation_lock_at'] ?? null,
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Grupo atualizado com sucesso!');
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Grupo deletado com sucesso!');
    }

    public function joinByCode(Request $request)
    {
        $validated = $request->validate([
            'join_code' => 'required|string|size:6',
        ]);

        $group = Group::where('join_code', strtoupper(trim($validated['join_code'])))->first();

        if (!$group) {
            return back()->withErrors(['join_code' => 'Código inválido ou grupo não encontrado.']);
        }

        if ($group->members()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors(['join_code' => 'Você já é membro deste grupo.']);
        }

        if ($group->blockedUsers()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors(['join_code' => 'Você foi bloqueado e não pode entrar neste grupo.']);
        }

        $group->members()->attach(auth()->id(), ['role' => 'player']);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Entrou no grupo com sucesso!');
    }

    public function addMember(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($group->members()->where('user_id', $validated['user_id'])->exists()) {
            return back()->withErrors(['user_id' => 'Usuário já é membro deste grupo.']);
        }

        $group->members()->attach($validated['user_id'], ['role' => 'player']);

        return back()->with('success', 'Membro adicionado com sucesso!');
    }

    public function removeMember(Group $group, User $user)
    {
        $this->authorize('update', $group);

        if ($user->id === $group->user_id) {
            return back()->withErrors(['user' => 'Não é possível remover o dono do grupo.']);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Use a opção de sair do grupo.']);
        }

        $group->members()->detach($user->id);

        return back()->with('success', 'Membro removido com sucesso!');
    }

    public function blockMember(Group $group, User $user)
    {
        $this->authorize('update', $group);

        if ($user->id === $group->user_id) {
            return back()->withErrors(['user' => 'Não é possível bloquear o dono do grupo.']);
        }

        $member = $group->members()->where('user_id', $user->id)->first();
        if ($member && $member->pivot->role === 'admin') {
            return back()->withErrors(['user' => 'Não é possível bloquear outro administrador.']);
        }

        $group->blockedUsers()->syncWithoutDetaching([$user->id]);
        $group->members()->detach($user->id);

        return back()->with('success', "{$user->name} foi bloqueado do grupo.");
    }

    public function unblockMember(Group $group, User $user)
    {
        $this->authorize('update', $group);
        $group->blockedUsers()->detach($user->id);

        return back()->with('success', "{$user->name} foi desbloqueado.");
    }

    public function leave(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Você não é membro deste grupo.');

        if ($group->user_id === auth()->id()) {
            return back()->withErrors(['group' => 'O dono do grupo não pode sair. Transfira a administração antes.']);
        }

        if ($member->pivot->role === 'admin') {
            $adminCount = $group->members()->wherePivot('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['group' => 'Deve existir ao menos um administrador no grupo.']);
            }
        }

        $group->members()->detach(auth()->id());

        return redirect()->route('groups.index')->with('success', 'Você saiu do grupo com sucesso.');
    }

    public function promoteMember(Group $group, User $user)
    {
        $this->authorize('update', $group);

        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['user' => 'Usuário não é membro do grupo.']);
        }

        $group->members()->updateExistingPivot($user->id, ['role' => 'admin']);

        return back()->with('success', "{$user->name} promovido a administrador!");
    }

    public function demoteMember(Group $group, User $user)
    {
        $this->authorize('update', $group);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Você não pode rebaixar a si mesmo.']);
        }

        $group->members()->updateExistingPivot($user->id, ['role' => 'player']);

        return back()->with('success', "{$user->name} rebaixado a jogador.");
    }

    public function generateCode(Group $group)
    {
        $this->authorize('update', $group);

        do {
            $code = strtoupper(Str::random(6));
        } while (Group::where('join_code', $code)->where('id', '!=', $group->id)->exists());

        $group->update(['join_code' => $code]);

        return back()->with('success', "Novo código gerado: {$code}");
    }

    public function confirmPresence(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403);

        $isConfirmed = (bool) $member->pivot->presence_confirmed;

        // Para grupos semanais, só permite confirmar se houver rodada aberta
        if ($group->schedule_type === 'weekly' && !$isConfirmed) {
            $hasOpen = $group->matches()->whereIn('status', ['pending', 'in_progress'])->exists();
            if (!$hasOpen) {
                return back()->withErrors(['presence' => 'Nenhuma rodada aberta para confirmar presença.']);
            }
        }

        // Se está tentando cancelar confirmação, verifica o horário de bloqueio
        if ($isConfirmed && $group->confirmation_lock_at) {
            $weeklyDay = $group->weekly_day; // null para grupos scheduled
            $isMatchDay = $weeklyDay === null || now()->dayOfWeek === $weeklyDay;

            if ($isMatchDay) {
                $lockTime = Carbon::today()->setTimeFromTimeString($group->confirmation_lock_at);
                if (now()->greaterThan($lockTime)) {
                    return back()->withErrors(['presence' => 'Prazo para cancelar confirmação já encerrou.']);
                }
            }
        }

        DB::table('user_groups')
            ->where('user_id', auth()->id())
            ->where('group_id', $group->id)
            ->update(['presence_confirmed' => $isConfirmed ? 0 : 1]);

        $msg = $isConfirmed ? 'Confirmação cancelada.' : 'Presença confirmada!';
        return back()->with('success', $msg);
    }

    public function startRound(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member && $member->pivot->role === 'admin', 403, 'Apenas administradores podem iniciar rodadas.');
        abort_unless($group->schedule_type === 'weekly', 422, 'Grupo não configurado como semanal.');

        $openMatch = $group->matches()->whereIn('status', ['pending', 'in_progress'])->first();
        if ($openMatch) {
            return back()->withErrors(['round' => 'Já existe uma rodada ativa.']);
        }

        // Calcular data da próxima ocorrência
        $day  = $group->weekly_day ?? 0;
        $time = $group->weekly_time ?? '20:00:00';
        [$hour, $minute] = explode(':', $time);

        $scheduledAt = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays($day)->setTime((int) $hour, (int) $minute);
        if ($scheduledAt->isPast()) {
            $scheduledAt->addWeek();
        }

        // Resetar presença de todos os membros
        DB::table('user_groups')->where('group_id', $group->id)->update(['presence_confirmed' => false]);

        $match = $group->matches()->create([
            'scheduled_at' => $scheduledAt,
            'status'       => 'pending',
            'title'        => 'Rodada ' . $scheduledAt->format('d/m/Y'),
        ]);

        return redirect()->route('matches.show', $match)
            ->with('success', 'Rodada iniciada! Jogadores já podem confirmar presença.');
    }

    public function reportDailyPayment(Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403);
        abort_unless($group->fee_type === 'daily', 422, 'Grupo não usa cobrança diária.');

        // Evita registrar mais de uma diária no mesmo dia
        $alreadyPaid = Payout::where('user_id', auth()->id())
            ->where('group_id', $group->id)
            ->where('status', 'paid')
            ->whereDate('paid_at', today())
            ->exists();

        if ($alreadyPaid) {
            return back()->with('info', 'Você já registrou o pagamento da diária hoje.');
        }

        Payout::create([
            'user_id'        => auth()->id(),
            'group_id'       => $group->id,
            'due_date'       => now(),
            'amount'         => $group->monthly_fee,
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => 'auto-report',
        ]);

        return back()->with('success', 'Pagamento da diária registrado!');
    }

    public function storePost(Request $request, Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Você não é membro deste grupo.');

        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $group->posts()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'type' => 'text',
        ]);

        return back()->with('success', 'Publicado com sucesso!');
    }

    public function destroyPost(Group $group, $postId)
    {
        $post = $group->posts()->findOrFail($postId);
        $user = auth()->user();

        $isAdmin = $group->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_unless($post->user_id === $user->id || $isAdmin, 403, 'Sem permissão.');

        $post->delete();

        return back()->with('success', 'Post removido.');
    }
}
